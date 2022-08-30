<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Admin;

/**
 * Description of ShopAdmin
 *
 * @author trieu
 */
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Admin\AdminInterface;
use Knp\Menu\ItemInterface as MenuItemInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use App\Entity\Shop;
use App\Entity\ShopService;

class ShopAdmin extends AbstractAdmin {

    private $container;

    protected function configureRoutes(RouteCollectionInterface $collection): void {
        $collection->add('services', $this->getRouterIdParameter() . '/services'); // Action gets added automatically
        $collection->add('schedule', $this->getRouterIdParameter() . '/schedule'); // Action gets added automatically
    }

    protected function configureFormFields(FormMapper $form): void {
        $form->add('Name', TextType::class, ['required' => true]);
        $form->add('Description', TextareaType::class, ['required' => false]);
        $form->add('Phone', TextType::class, ['required' => true, 'attr' => ['maxlength' => '15']]);
        $form->add('Address1', TextType::class, ['required' => true]);
        $form->add('Address2', TextType::class, ['required' => false]);
        $form->add('City', TextType::class, ['required' => true]);
        $form->add('State', TextType::class, ['required' => true]);
        $form->add('ZipCode', TextType::class, ['required' => true]);
        $form->add('Country', CountryType::class, ['required' => true]);
        $form->add('start_time', TimeType::class, ['required' => true]);
        $form->add('end_time', TimeType::class, ['required' => true]);

        $form->add('Active', ChoiceType::class, ['required' => true,
            'choices' => [
                'Yes' => true,
                'No' => false,
            ],
        ]);
    }

    protected function configureDatagridFilters(DatagridMapper $datagrid): void {
        $datagrid->add('Name');
        $datagrid->add('Address1');
        $datagrid->add('Address2');
        $datagrid->add('City');
        $datagrid->add('State');
        $datagrid->add('ZipCode');
        $datagrid->add('Country');
        $datagrid->add('Active');
    }

    protected function configureListFields(ListMapper $list): void {
        $list->addIdentifier('id', null, ['route' => ['name' => 'edit']]);
        $list->addIdentifier('Name', null, ['route' => ['name' => 'edit']]);
        $list->add('Address1');
        $list->add('Address2');
        $list->add('City');
        $list->add('State');
        $list->add('ZipCode');
        $list->add('Country');
        $list->add('Active');
        $list->add(ListMapper::NAME_ACTIONS, null, [
            'actions' => [
                'edit' => [],
                'delete' => [],
                'services' => [
                    'template' => 'CRUD/list__action_services.html.twig'
                ],
                'schedule' => [
                    'template' => 'CRUD/shop/list__action_schedule.html.twig'
                ]
            ]
        ]);
    }

    public function setContainer($container) {
        $this->container = $container;
    }

    public function getPoolContainer() {
        return $this->container;
    }

    protected function preRemove(object $object): void {
        $shopServices = $this->getModelManager(ShopService::class)->findBy(ShopService::class, ['Shop' => $object->getId()]);
        if (count($shopServices) > 0) {
            foreach ($shopServices as $shopservice) {
                $object->removeService($shopservice);
            }
        }
    }

}
