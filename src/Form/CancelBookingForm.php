<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Description of CancelBookingForm
 *
 * @author trieu
 */
class CancelBookingForm extends AbstractType{
   public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('bookingid', HiddenType::class)
            ;
    }
}
