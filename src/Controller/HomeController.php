<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Services;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class HomeController extends AbstractController {

    /**
     * @Route("/", name="home")
     */
    public function index(Request $request, ManagerRegistry $doctrine, SessionInterface $session): Response {
        $userid = $session->get('userid');
        $loggedIn = false;
        if ($userid != null) {
            $loggedIn = true;
        }
        $services = $doctrine->getRepository(Services::class)->getListHomepageServiceActivated();
        foreach ($services as &$service) {
            if (!empty($service['Thumbnail'])) {
                $service['Thumbnail'] = $request->getSchemeAndHttpHost() . DIRECTORY_SEPARATOR . Services::SERVER_PATH_TO_IMAGE_FOLDER . DIRECTORY_SEPARATOR . $service['Thumbnail'];
            }
        }
        return $this->render('home/index.html.twig', [
                    'controller_name' => 'HomeController',
                    'services' => $services,
                    'loggedIn' => $loggedIn
        ]);
    }

}
