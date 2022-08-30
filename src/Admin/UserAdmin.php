<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Admin;

/**
 * Description of UserAdmin
 *
 * @author trieu
 */
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use App\Entity\Customer;

class UserAdmin extends AbstractAdmin {

    private $container;

    protected function configureFormFields(FormMapper $form): void {
        $requirePassword = false;
        if ($this->isCurrentRoute('create')) {
            $requirePassword = true;
        }
        $form->add('email', TextType::class);
        $form->add('plainPassword', PasswordType::class, ['mapped' => false, 'required' => $requirePassword]);
    }

    protected function configureDatagridFilters(DatagridMapper $datagrid): void {
        $datagrid->add('email');
    }

    protected function configureListFields(ListMapper $list): void {
        $list->addIdentifier('id', null, ['route' => ['name' => 'edit']]);
        $list->addIdentifier('email', null, ['route' => ['name' => 'edit']]);
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
        $this->addAdminRole($object);
    }

    private function updateNewPassword(object $object) {
        $plainPassword = $this->getForm()->get('plainPassword')->getData();

        $encoder = $this->container->get('security.password_encoder');
        $encoded = $encoder->encodePassword($object, $plainPassword);
        $object->setPassword($encoded);
        $this->addAdminRole($object);
    }

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container) {
        $this->container = $container;
    }

    private function addAdminRole(object $object): void {
        $object->setRoles(["ROLE_SUPER_ADMIN",
            "ROLE_SONATA_ADMIN"]);
    }

}
