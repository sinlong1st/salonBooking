<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Booking;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class BookingHistoryController extends AbstractController {

    /**
     * @Route("/booking/history", name="booking_history")
     */
    public function index(ManagerRegistry $doctrine, SessionInterface $session): Response {
        if ($session->get('userid') < 0) {
            return $this->redirectToRoute('customer');
        }
        $customerId = $session->get('userid');
        $bookingdata = $doctrine->getRepository(Booking::class)->getCustomerBooking($customerId);
        return $this->render('booking_history/index.html.twig', [
                    'bookingdata' => $bookingdata
        ]);
    }

}
