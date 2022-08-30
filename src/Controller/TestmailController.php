<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

class TestmailController extends AbstractController {

    /**
     * @Route("/testmail", name="testmail")
     */
    public function index(MailerInterface $mailer): Response {
        $email = (new Email())
                ->from('hello@example.com')
                ->to('trieu181989@gmail.com')
                //->cc('cc@example.com')
                //->bcc('bcc@example.com')
                //->replyTo('fabien@example.com')
                //->priority(Email::PRIORITY_HIGH)
                ->subject('Time for Symfony Mailer!')
                ->text('Sending emails is fun again!')
                ->html('<p>See Twig integration for better HTML integration!</p>');

        $mailer->send($email);
        return $this->render('testmail/index.html.twig', [
                    'controller_name' => 'TestmailController',
        ]);
    }

}
