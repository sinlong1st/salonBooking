<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Admin;

/**
 * Description of BookingAdmin
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
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\AdminBundle\FieldDescription\FieldDescriptionInterface;
use App\Entity\Shop;
use App\Entity\ShopService;

class BookingAdmin extends AbstractAdmin {

    private $container;

    public function __construct(?string $code = null, ?string $class = null, ?string $baseControllerName = null) {
        parent::__construct($code, $class, $baseControllerName);

        $this->datagridValues = array(
            '_page' => 1,
            '_sort_order' => 'DESC',
            '_sort_by' => 'bookingStatus',
        );
    }

    protected function configureFormFields(FormMapper $form): void {
        $subject = $this->getSubject();
        $choiceOption = [
        ];
        if ($this->hasSubject()) {
            $subject = $this->getSubject();
            if (empty($subject->getBookingStatus())) {
                $choiceOption[''] = 0;
                $choiceOption['Check In'] = 1;
            } else if ($subject->getBookingStatus() == 1) {
                $choiceOption['Check In'] = 1;
                $choiceOption['Check Out'] = 2;
            } else if ($subject->getBookingStatus() == 2) {
                $choiceOption['Check Out'] = 2;
            }
        } else {
            $choiceOption[''] = 0;
            $choiceOption['Check In'] = 1;
        }
        $form->add('CustomerName', TextType::class, ['required' => true]);
        $form->add('Phone', TextType::class, ['required' => true]);
        $form->add('date', DateType::class, ['required' => true]);
        $form->add('start_time', TimeType::class, ['required' => true]);
        $form->add('end_time', TimeType::class, ['required' => true]);
        $form->add('bookingStatus', ChoiceType::class, ['required' => true,
            'choices' => [
                '' => 0,
                'Check Out' => 2,
                'Check In' => 1,
            ],
        ]);
        $form->add('Address', TextType::class, ['required' => false]);
        $form->add('Address2', TextType::class, ['required' => false]);
        $form->add('City', TextType::class, ['required' => false]);
        $form->add('State', TextType::class, ['required' => false]);
        $form->add('ZipCode', TextType::class, ['required' => false]);
         $form->add('ServiceType', ChoiceType::class, ['required' => true,
            'choices' => [
                'Shop' => 1,
                'Home' => 2,
            ],
        ]);
    }

    protected function configureDatagridFilters(DatagridMapper $datagrid): void {
        $datagrid->add('CustomerName');
        $datagrid->add('date');
        $datagrid->add('start_time');
        $datagrid->add('end_time');
        $datagrid->add('ServiceType', null, [
            'field_type' => ChoiceType::class,
            'field_options' => [
                'choices' => [
                    'Pending' => '0',
                    'Checked in' => '1',
                    'Checked Out' => '2',
                ],
                'multiple' => true,
            ],
                ]
        );
        $datagrid->add('bookingStatus', null, [
            'field_type' => ChoiceType::class,
            'field_options' => [
                'choices' => [
                    'Shop' => '1',
                    'Home' => '2'
                ],
                'multiple' => true,
            ],
                ]
        );
    }

    protected function configureListFields(ListMapper $list): void {
        $list->addIdentifier('id', null, ['route' => ['name' => 'edit']]);
        $list->addIdentifier('CustomerName', null, ['route' => ['name' => 'edit']]);
        $list->addIdentifier('date', null, ['route' => ['name' => 'edit']]);
        $list->addIdentifier('start_time', null, ['route' => ['name' => 'edit']]);
        $list->addIdentifier('end_time', null, ['route' => ['name' => 'edit']]);
        $list->addIdentifier('ServiceType', FieldDescriptionInterface::TYPE_CHOICE, [
            'route' => ['name' => 'edit'],
            'choices' => [
                1 => 'Shop',
                2 => 'Home',
            ],
        ]);
        $list->addIdentifier('bookingStatus', FieldDescriptionInterface::TYPE_CHOICE, [
            'route' => ['name' => 'edit'],
            'choices' => [
                0 => 'Pending',
                1 => 'Checked in',
                2 => 'Checked Out',
            ],
        ]);
    }

    public function setContainer($container) {
        $this->container = $container;
    }

    public function getPoolContainer() {
        return $this->container;
    }

    protected function prePersist(object $object): void {
        if ($object->getBookingStatus() > 1) {
            $object->setBookingStatus(0);
        }
    }

    protected function preUpdate(object $object): void {
        $tmpObject = $this->getModelManager()->find($this->getClass(), $object->getId());
        if ($tmpObject->getBookingStatus() == 2) {
            $object->setBookingStatus(2);
        }
    }

}
