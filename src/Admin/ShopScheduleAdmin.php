<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Admin;

/**
 * Description of ShopSchedule
 *
 * @author trieu
 */
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use App\Entity\Shop;
use App\Entity\ShopService;
use App\Entity\SpecialDate;

class ShopScheduleAdmin extends AbstractAdmin {

    private $shop_id = 0;
    protected $baseRouteName = 'admin_shopschedule';


    /**
     * @return array<string, string|string[]> [action1 => requiredRole1, action2 => [requiredRole2, requiredRole3]]
     */
    protected function getAccessMapping(): array {
        $shop_id = intval($this->getRequest()->get('shop_id'));
        if ($shop_id <= 0) {
            throw new \InvalidArgumentException(
                    'Request could not be found id '
                    . ' Please make sure your action is defined id'
            );
        }
        $this->shop_id = $shop_id;
        return [];
    }

    protected function configurePersistentParameters(): array {
        if (!$this->getRequest()) {
            return [];
        }

        return [
            'shop_id' => $this->getRequest()->get('shop_id', 0),
        ];
    }

    protected function configureQuery(ProxyQueryInterface $query): ProxyQueryInterface {
        $rootAlias = current($query->getRootAliases());

        $query->andWhere(
                $query->expr()->eq($rootAlias . '.shop', ':shopid')
        );
        $query->setParameter('shopid', $this->shop_id);
        return $query;
    }

    protected function configureDatagridFilters(DatagridMapper $datagrid): void {

        $datagrid->add('date');
        $datagrid->add('start_time');
        $datagrid->add('end_time');
    }

    protected function configureListFields(ListMapper $list): void {
        $list->addIdentifier('id', null, ['route' => ['name' => 'edit']]);
        $list->addIdentifier('date', null, ['route' => ['name' => 'edit']]);
        $list->addIdentifier('start_time', null, ['route' => ['name' => 'edit']]);
        $list->addIdentifier('end_time', null, ['route' => ['name' => 'edit']]);
        $list->addIdentifier('active', null, ['route' => ['name' => 'edit']]);
    }

    protected function configureFormFields(FormMapper $form): void {
        $form->add('date', DateType::class, ['required' => true]);
        $form->add('start_time', TimeType::class, ['required' => true]);
        //$form->add('shop', HiddenType::class, ['required' => true]);
        $form->add('end_time', TimeType::class, ['required' => true]);
        $form->add('active', ChoiceType::class, ['required' => true,
            'choices' => [
                'Yes' => true,
                'No' => false,
            ],
        ]);
    }

    protected function prePersist(object $object): void {

        $currentShop = $this->getModelManager()->find(Shop::class, $this->shop_id);
        $object->setShop($currentShop);
    }

    protected function preUpdate(object $object): void {
        $shop = $this->getObject($object->getId());
        $object->setShop($shop->getShop());
    }

}
