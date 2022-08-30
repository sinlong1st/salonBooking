<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Form;

/**
 * Description of CustomerEditForm
 *
 * @author trieu
 */
use App\Entity\Customer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class CustomerEditForm extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
                ->add('firstName', null, ['required' => true])
                ->add('lastName', null, ['required' => true])
                ->add('address', null, ['required' => false])
                ->add('address2', null, ['required' => false])
                ->add('phone', TelType::class, ['required' => false])
                ->add('city', null, ['required' => false])
                ->add('state', null, ['required' => false])
                ->add('zipcode', null, ['required' => false])
                ->add('additionalInfo', TextareaType::class, ['required' => false])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void {
        $resolver->setDefaults([
            'data_class' => Customer::class,
        ]);
    }

}
