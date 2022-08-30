<?php

namespace App\Controller;

use App\Entity\Services;
use App\Entity\Shop;
use App\Entity\ShopService;
use App\Entity\Booking;
use App\Entity\SpecialDate;
use App\Entity\Customer;
use App\Form\CancelBookingForm;
use App\Repository\CustomerRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Helper\Tools;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ServiceController extends AbstractController {

    /**
     * @Route("/service", name="app_shop")
     */
    public function index(ManagerRegistry $doctrine): Response {
        $title = 'All shops: ';
        $shops = $doctrine->getRepository(Shop::class)->findBy(['Active' => 1]);

        if (!$shops) {
            throw $this->createNotFoundException(
                    'No shop available'
            );
        }
        return $this->render('service/index.html.twig', [
                    'shops' => $shops
        ]);
    }

    /**
     * @Route("/loadservice/{id}", name="load_service")
     */
    public function LoadService(ManagerRegistry $doctrine, int $id): JsonResponse {
        $shop = $doctrine->getRepository(ShopService::class)->LoadServices($id);
        $dateNotAvailable = $doctrine->getRepository(SpecialDate::class)->getShopDateUnAvailable($id);
        $skipDates = [];
        if (count($dateNotAvailable) > 0) {
            foreach ($dateNotAvailable as $date) {
                $skipDates[] = $date['date']->format('m/d/Y');
            }
        }
        $url = $this->generateUrl('load_availabletime', array(
            'id_shop' => $id,
                )
        );
        return $response = new JsonResponse(['id_shop' => $id, 'shop' => $shop, 'url' => $url, 'skipdate' => $skipDates]);
    }

    /**
     * @Route("/loadavaitime/", name="load_availabletime")
     */
    public function LoadAvailableTime(ManagerRegistry $doctrine, Request $request): JsonResponse {
        $id_shop = $request->query->get('id_shop');
        $id_service = $request->query->get('id_service');
        $booking_date = $request->query->get('booking_date');
        if (empty($booking_date) || !( $date = date_timestamp_set(date_create(), strtotime($booking_date)))) {
            throw new \Exception("date booking format");
        }
        $shop = $doctrine->getRepository(Shop::class)->find($id_shop);
        if (!$shop || !$shop->getActive()) {
            throw new \Exception('Invalid shop');
        }

        $serviceTime = $doctrine->getRepository(ShopService::class)->getShopServiceTime($id_shop, $id_service);
        if (empty($serviceTime)) {
            throw new \Exception('Invalid service');
        }

        $dateFrom = new \DateTime();
        $dateFrom->setDate($date->format('Y'), $date->format('m'), $date->format('d'));
        $beginHour = 9;
        $beginMinute = 0;

        $checkSpecialDate = $doctrine->getRepository(SpecialDate::class)->checkDateBookingInSpecialDate($id_shop, $date);
        if ($checkSpecialDate != null) {
            if (!$checkSpecialDate->getActive()) {
                throw new \Exception("date booking format");
            }
            $beginHour = intval($checkSpecialDate->getStartTime()->format('H'));
            $beginMinute = intval($checkSpecialDate->getStartTime()->format('i'));
        } else {
            $beginHour = intval($shop->getStartTime()->format('H'));
            $beginMinute = intval($shop->getStartTime()->format('i'));
        }
        $dateFrom->setTime($beginHour, $beginMinute);
        if ($dateFrom->format('Ymd') < date('Ymd')) {
            throw new Exception("date booking format");
        }
        if ($dateFrom->format('Y-m-d') == date('Y-m-d')) {
            if ($dateFrom->format('H') < date('H')) {
                $dateFrom->setTime(date('H'), date('i'));
            }
        }

        $dateTo = new \DateTime();
        $dateTo->setDate($date->format('Y'), $date->format('m'), $date->format('d'));
        $endHour = 20;
        $endMinute = 0;
        if ($checkSpecialDate != null) {
            $endHour = intval($checkSpecialDate->getEndTime()->format('H'));
            $endMinute = intval($checkSpecialDate->getEndTime()->format('i'));
        } else {
            $endHour = intval($shop->getEndTime()->format('H'));
            $endMinute = intval($shop->getEndTime()->format('i'));
        }
        $dateTo->setTime($endHour, $endMinute);
        $existingBooking = $doctrine->getRepository(Booking::class)->getShopBookingByDate(3, $dateFrom, $dateTo);
        $rs = Tools::getTimeRangeAvailable($serviceTime['service_time'], $dateFrom, $dateTo, $existingBooking);
        return $response = new JsonResponse([
            'shop_id' => $id_shop,
            'service_id' => $id_service,
            'timeavailable' => $rs
        ]);
    }
    /**
     * @Route("/service/confirm", name="booking_confirm")
     */
    public function Confirm(Request $request, ManagerRegistry $doctrine, CustomerRepository $customerRepository, SessionInterface $session): Response {
        $booking = $session->get('booking');
        $customerInfo = null;
        
        if ($booking == null){
            $this->addFlash('error', 'Your appointment was not booked properly!');
            return $this->redirectToRoute('app_shop');
        }
        $bookingshop = $booking['bookingshop'];
        $bookingservice = $booking['bookingservice'];
        $date = $booking['date'];
        $bookingtime = $booking['bookingtime'];
        $hourDigit = floor($bookingtime / 100);
        $minDigit = $bookingtime % 100;
        $shopInfo = $doctrine->getRepository(Shop::class)->find($bookingshop);
        
        $userid = $session->get('userid');
            if ($userid <= 0) {
                return $this->redirectToRoute('customer_login');
            }
        $customerInfo = $doctrine->getRepository(Customer::class)->find($userid);
        //$firstname = $customerInfo.getFirstName();
        if ($request->getMethod() == "POST"){
            $newAddress=$request->request->get('newAdress');
            $newAddress2=$request->request->get('newAdress2');
            $newCity=$request->request->get('newCity');
            $newState=$request->request->get('newState');
            $newZipcode=$request->request->get('newZipcode');

            $entityManager = $doctrine->getManager();
            $finalbooking = new Booking();
            //$finalbooking->setDate('2022-01-01');
            $finalbooking->setAddress($newAddress);
            $finalbooking->setAddress2($newAddress2);
            $finalbooking->setCity($newCity);
            $finalbooking->setState($newState);
            $finalbooking->setZipCode($newZipcode);
            $finalbooking->setServiceType($bookingservice);
            $finalbooking->setCustomerName($customerInfo->getFirstName().$customerInfo->getLastName());
            $finalbooking->setPhone($customerInfo->getPhone());
            $finalbooking->setBookingStatus(1);
            $entityManager->persist($finalbooking);
            $entityManager->flush();
            return $this->redirectToRoute('booking_history');

        }
        //var_dump($customerInfo);
        return $this->render('service/confirm.html.twig', [
             'customer' => $customerInfo,
             'shopInfo' => $shopInfo,
             'bookingservice' => $bookingservice,
             'date' => $date,
             'minDigit' => $minDigit,
             'hourDigit' => $hourDigit
                //'Service: '.$service->getName()
        ]);
    }
    /**
     * @Route("/service/appointment", name="booking_appointment")
     */
    public function Appointment(Request $request, ManagerRegistry $doctrine, CustomerRepository $customerRepository, SessionInterface $session): Response {
        $bookingshop=$request->request->get('bookingshop');
        $bookingservice=$request->request->get('bookingservice');
        $date=$request->request->get('date');
        $bookingtime=$request->request->get('bookingtime');

        $session->set('booking', [
            'bookingshop' => $bookingshop,
            'bookingservice' => $bookingservice,
            'date' => $date,
            'bookingtime' => $bookingtime,
        ]);
        return $this->redirectToRoute('booking_confirm');

    }

    /**
     * @Route("/booking/cancel/{id}", name="booking_cancel",requirements={"id"="\d+"})
     */
    public function cancelBooking(int $id, SessionInterface $session, ManagerRegistry $doctrine, Request $request) : Response{
        $userid = $session->get('userid');
        $customer = null;
        if (empty($userid) || ( $customer = $doctrine->getRepository(Customer::class)->find($userid)) == null) {
            return $this->redirectToRoute('customer_login');
        }

        $booking = $doctrine->getRepository(Booking::class)->find($id);
        if ($booking == null || $booking->getCustomerId() != $userid) {
            throw $this->createNotFoundException('The product does not exist');
        }
        $form = $this->createForm(CancelBookingForm::class);
        $form->get('bookingid')->setData($id);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $doctrine->getManager();
            $entityManager->remove($booking);
            $entityManager->flush();
            return $this->redirectToRoute('booking_history');
        }
        return $this->render('service/confirm_cancel.html.twig', [
                    'form' => $form->createView()
        ]);
    }

}
