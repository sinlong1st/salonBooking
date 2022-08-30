<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Controller;

/**
 * Description of AdminShopCRUDController
 *
 * @author trieu
 */
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Shop;
use App\Entity\Services;
use App\Entity\ShopService;

class AdminShopCRUDController extends CRUDController {

    protected $container;

    /**
     * 
     * @param type $id
     * @param Request $request
     * @param ServicesRepository $serviceRepository
     * @return Response
     */
    public function servicesAction($id, Request $request, EntityManagerInterface $entityManager): Response {

        $listLink = $this->admin->generateUrl('list');
        $services = $entityManager->getRepository(Services::class)->getListServiceActivated();
        $listDefault = $entityManager->getRepository(ShopService::class)->getShopServices($id);

        $shopObj = $entityManager->getRepository(Shop::class)->find($id);

        if ($request->isMethod('post') && $shopObj->getId() > 0) {
            $servicesSelected = $request->request->get('services');
            $prices = $request->request->get('prices');

            $defaultService = array_column($listDefault, 'id', 'service_id');
            //if select then update
            if (is_array($servicesSelected)) {
                foreach ($servicesSelected as $selected) {
                    $tmpPrice = 0;
                    if (isset($prices[$selected])) {
                        $tmpPrice = floatval($prices[$selected]);
                    }
                    $servicesObj = $entityManager->getRepository(Services::class)->find($selected);
                    if ($servicesObj == null || $servicesObj->getId() < 1) {
                        continue;
                    }
                    if (isset($defaultService[$selected])) {
                        $shopServiceObject = $entityManager->getRepository(ShopService::class)->find($defaultService[$selected]);
                        unset($defaultService[$selected]);
                    } else {
                        $shopServiceObject = new ShopService();
                        $shopServiceObject->setShop($shopObj);
                        $shopServiceObject->setService($servicesObj);
                        $shopServiceObject->setServiceTime(new \DateTime());
                    }
                    $shopServiceObject->setPrice($tmpPrice);
                    $entityManager->persist($shopServiceObject);
                    $entityManager->flush();
                }
            }
            $this->removeListServiceNotSelected($entityManager, $defaultService);
            return new RedirectResponse($this->admin->generateUrl('list'));
        }
        return $this->render('CRUD/shop_services.html.twig', [
                    'listLink' => $listLink,
                    'services' => $services,
                    'listDefault' => $listDefault
        ]);
    }

    private function removeListServiceNotSelected(EntityManagerInterface $entityManager, $services) {
        if (is_array($services) && count($services) > 0) {
            $ids = array_values($services);
            $entityManager->getRepository(ShopService::class)->removeListID($ids);
        }
    }

}
