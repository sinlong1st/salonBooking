<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CustomerAdmin
 *
 * @author trieu
 */

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use App\Entity\Customer;

class CustomerAdmin extends AbstractAdmin {

    private $container;

    protected function configureFormFields(FormMapper $form): void {
        $requirePassword = false;
        if ($this->isCurrentRoute('create')) {
            $requirePassword = true;
        }
        $form->add('email', EmailType::class);
        $form->add('plainPassword', PasswordType::class, ['mapped' => false, 'required' => $requirePassword]);
        $form->add('firstName', TextType::class, ['required' => true]);
        $form->add('lastName', TextType::class, ['required' => true]);
        $form->add('address', TextType::class, ['required' => false]);
        $form->add('address2', TextType::class, ['required' => false]);
        $form->add('phone', TextType::class, ['required' => false]);
        $form->add('city', TextType::class, ['required' => false]);
        $form->add('state', TextType::class, ['required' => false, 'attr' => ['maxlength' => '2']]);
        $form->add('zipcode', TextType::class, ['required' => false, 'attr' => ['maxlength' => '10']]);
        $form->add('isVerified', ChoiceType::class, ['required' => true,
            'choices' => [
                'Yes' => true,
                'No' => false,
            ]
        ]);
    }

    protected function configureDatagridFilters(DatagridMapper $datagrid): void {
        $datagrid->add('email');
        $datagrid->add('firstName');
        $datagrid->add('lastName');
        $datagrid->add('address');
        $datagrid->add('address2');
        $datagrid->add('phone');
        $datagrid->add('city');
        $datagrid->add('state');
        $datagrid->add('zipcode');
    }

    protected function configureListFields(ListMapper $list): void {
        $list->addIdentifier('id', null, ['route' => ['name' => 'edit']]);
        $list->addIdentifier('email', null, ['route' => ['name' => 'edit']]);
        $list->add('firstName');
        $list->add('lastName');
        $list->add('address');
        $list->add('address2');
        $list->add('phone');
        $list->add('city');
        $list->add('state');
        $list->add('zipcode');
    }

    protected function preUpdate(object $object): void {
        $plainPassword = $this->getForm()->get('plainPassword')->getData();
        if (empty($plainPassword)) {
            $tmpObject = $this->getModelManager()->find($this->getClass(), $object->getId());
            $object->setPassword($tmpObject->getPassword());
        } else {
            $this->updateNewPassword($object);
        }
    }

    protected function prePersist(object $object): void {
        $this->updateNewPassword($object);
    }

    private function updateNewPassword(object $object) {
        $plainPassword = $this->getForm()->get('plainPassword')->getData();

        $encoder = $this->container->get('security.password_encoder');
        $encoded = $encoder->encodePassword($object, $plainPassword);
        $object->setPassword($encoded);
    }

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container) {
        $this->container = $container;
    }

}
