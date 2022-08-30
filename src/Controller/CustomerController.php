<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Repository\CustomerRepository;
use App\Form\RegistrationFormType;
use App\Form\CustomerEditForm;
use App\Form\ChangePasswordForm;
use App\Form\ForgotPasswordForm;
use App\Form\CustomerLoginForm;
use App\Form\ResetPasswordForm;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Form\FormError;
use SymfonyCasts\Bundle\VerifyEmail\Generator\VerifyEmailTokenGenerator;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CustomerController extends AbstractController {

    private EmailVerifier $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier) {
        $this->emailVerifier = $emailVerifier;
    }

    /**
     * @Route("/customer", name="customer")
     */
    public function index(CustomerRepository $customerRepository, SessionInterface $session): Response {
        $customer = $this->getCustomer($customerRepository, $session);
        if (empty($customer)) {
            return $this->redirectToRoute('customer_login');
        }
        return $this->render('customer/index.html.twig');
    }

    /**
     * @Route("/customer/login", name="customer_login")
     */
    public function login(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, SessionInterface $session): Response {
        if ($session->get('userid') > 0) {
            return $this->redirectToRoute('customer');
        }
        $form = $this->createForm(CustomerLoginForm::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->get('email')->getData();
            $password = $form->get('plainPassword')->getData();

            $customerLogin = $entityManager->getRepository(Customer::class)->findOneBy(['email' => $email, 'isVerified' => true]);
            if (!empty($customerLogin) && $customerLogin->getId() > 0 && $userPasswordHasher->isPasswordValid($customerLogin, $password)) {
                $session->set('userid', $customerLogin->getId());
                return $this->redirectToRoute('customer');
            } else {
                $this->addFlash('error', 'Invalid Email or Password');
            }
        }
        return $this->render('customer/login.html.twig', [
                    'loginForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/customer/logout", name="customer_logout")
     */
    public function logout(SessionInterface $session): Response {
        $session->remove('userid');
        return $this->redirectToRoute('customer_login');
    }

    /**
     * @Route("/customer/register", name="customer_register")
     */
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response {
        $customer = new Customer();
        $form = $this->createForm(RegistrationFormType::class, $customer);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $customer->setPassword(
                    $userPasswordHasher->hashPassword(
                            $customer,
                            $form->get('plainPassword')->getData()
                    )
            );

            $entityManager->persist($customer);
            $entityManager->flush();
            $this->sendEmailVerifier($customer);
            return $this->redirectToRoute('customer');
        }

        return $this->render('customer/register.html.twig', [
                    'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("customer/verify/email", name="customer_verify_email")
     */
    public function verifyUserEmail(Request $request, CustomerRepository $customerRepository): Response {
        // $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');


        $customer = $customerRepository->findOneBy(['verifySignature' => $request->getUri()]);

        if (!empty($customer)) {
            try {
                $this->emailVerifier->handleEmailConfirmation($request, $customer);
                return $this->redirectToRoute('customer_login');
            } catch (VerifyEmailExceptionInterface $exception) {
                $this->addFlash('verify_email_error', $exception->getReason());
                return $this->redirectToRoute('customer_register');
            }
        }

        // validate email confirmation link, sets User::isVerified=true and persists
        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('verify_email_error', 'Invalid request');

        return $this->redirectToRoute('customer_register');
    }

    /**
     * @Route("/customer/edit/profile", name="customer_edit_profile")
     */
    public function edit(Request $request, CustomerRepository $customerRepository, EntityManagerInterface $entityManager, SessionInterface $session): Response {
        $customer = $this->getCustomer($customerRepository, $session);
        if (empty($customer)) {
            return $this->redirectToRoute('customer_login');
        }

        $form = $this->createForm(CustomerEditForm::class, $customer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // var_dump($customer);
            $customer->setFirstName($form->get('firstName')->getData());
            $customer->setLastName($form->get('lastName')->getData());
            $customer->setAddress($form->get('address')->getData());
            $customer->setAddress2($form->get('address2')->getData());
            $customer->setPhone($form->get('phone')->getData());
            $customer->setCity($form->get('city')->getData());
            $customer->setState($form->get('state')->getData());
            $customer->setZipcode($form->get('zipcode')->getData());
            $customer->setAdditionalInfo($form->get('additionalInfo')->getData());
            $entityManager->persist($customer);
            $entityManager->flush();
            return $this->redirectToRoute('customer_edit_profile');
        }



        return $this->render('customer/editprofile.html.twig', [
                    'customer' => $customer,
                    'editForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/customer/edit/password", name="customer_edit_password")
     */
    public function changePassword(Request $request, UserPasswordHasherInterface $userPasswordHasher, CustomerRepository $customerRepository, EntityManagerInterface $entityManager, SessionInterface $session): Response {
        $customer = $this->getCustomer($customerRepository, $session);
        if (empty($customer)) {
            return $this->redirectToRoute('customer_login');
        }

        $form = $this->createForm(ChangePasswordForm::class, $customer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $form->get('oldPassword')->getData();
            $customerLogin = $entityManager->getRepository(Customer::class)->find($customer->getId());
            if ($userPasswordHasher->isPasswordValid($customerLogin, $password)) {
                $customer->setPassword(
                        $userPasswordHasher->hashPassword(
                                $customer,
                                $form->get('newPassword')->getData()
                        )
                );
                $entityManager->flush();
                $this->addFlash('success', 'Your password has been reset!');
                return $this->redirectToRoute('customer');
            } else {
                $this->addFlash('error', 'Old password incorrect');
            }
        }
        return $this->render('customer/changepassword.html.twig', [
                    'customer' => $customer,
                    'changePasswordForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/customer/reset/password", name="customer_reset_password")
     */
    public function resetPassword(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher) {
        $token = $request->query->get('token');
        if (empty($token)) {
            $this->addFlash('error', 'Can not found user');
            return $this->redirectToRoute('customer_login');
        }
        $customer = $entityManager->getRepository(Customer::class)->findOneBy(['resetPasswordToken' => $token]);

        if (empty($customer)) {
            $this->addFlash('error', 'Can not found user');
            return $this->redirectToRoute('customer');
        }
        $form = $this->createForm(ResetPasswordForm::class, $customer);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $customer->setResetPasswordToken('');
            $customer->setPassword(
                    $userPasswordHasher->hashPassword(
                            $customer,
                            $form->get('plainPassword')->getData()
                    )
            );
            $entityManager->persist($customer);
            $entityManager->flush();
            return $this->redirectToRoute('customer_login');
        }
        return $this->render('customer/resetpassword.html.twig', [
                    'customer' => $customer,
                    'resetPasswordForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/customer/forgotpassword", name="customer_forgotpassword")
     */
    public function forgotPassword(Request $request, EntityManagerInterface $entityManager, MailerInterface $mailer): Response {
        //$customer = new Customer();

        $form = $this->createForm(ForgotPasswordForm::class);
        if ($request->isMethod('POST')) {
            $form->submit($request->request->get('forgot_password_form'));
            if ($form->isSubmitted() && $form->isValid()) { //isValid ??
                $email = $form->get('resetemail')->getData();
                $customer = $entityManager->getRepository(Customer::class)->findOneBy(['email' => $email]);
                if (!empty($customer)) {
                    $token = (new VerifyEmailTokenGenerator(time()))->createToken($customer->getId(), $customer->getEmail());
                    $customer->setResetPasswordToken($token);
                    $entityManager->persist($customer);
                    $entityManager->flush();
                    $this->sendEmailResetPassword($customer, $mailer);
                    return $this->redirectToRoute('customer');
                }
                $form->get('resetemail')->addError(new FormError("User cannot found"));
            }
        }

        return $this->render('customer/forgotpassword.html.twig', [
                    'forgotPasswordForm' => $form->createView()
        ]);
    }

    private function getCustomer(CustomerRepository $customerRepository, SessionInterface $session) {
        $userid = $session->get('userid');
        $customer = null;
        if (empty($userid) || ( $customer = $customerRepository->find($userid)) == null) {
            return null;
        }
        return $customer;
    }

    private function sendEmailVerifier(Customer $customer) {
        // generate a signed url and email it to the user
        $this->emailVerifier->sendEmailConfirmation('customer_verify_email', $customer,
                (new TemplatedEmail())
                        ->from(new Address('test@gmail.com', 'Webmaster'))
                        ->to($customer->getEmail())
                        ->subject('Please Confirm your Email')
                        ->htmlTemplate('customer/confirmation_email.html.twig')
        );
        // do anything else you need here, like send an email
    }

    private function sendEmailResetPassword(Customer $customer, MailerInterface $mailer) {
        $url = $this->generateUrl('customer_reset_password', array('token' => $customer->getResetPasswordToken()), UrlGeneratorInterface::ABSOLUTE_URL);
        $email = (new TemplatedEmail())
                ->from(new Address('test@gmail.com', 'Webmaster'))
                ->to($customer->getEmail())
                ->subject('Reset password')
                // path of the Twig template to render
                ->htmlTemplate('customer/reset_password_email.html.twig')
                ->context([
            'link' => $url
        ]);
        $mailer->send($email);
    }

}
