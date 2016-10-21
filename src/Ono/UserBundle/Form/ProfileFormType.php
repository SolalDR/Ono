<?php
// src/AppBundle/Form/RegistrationType.php

namespace Ono\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;


use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

use Symfony\Component\Validator\Constraints\DateTime;

class ProfileFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
      parent::buildForm($builder, $options);

      $builder->add('country', EntityType::class, array(
        'class'        => 'OnoMapBundle:Country',
        'choice_label' => 'libCountry',
        'multiple'     => false,
      ))
      ->remove("current_password")
      ->add('current_password', HiddenType::class, array(
        'translation_domain' => 'AcmeUserBundle',
        'mapped' => false,
        'required' => false,
    ))

      ->add('language', EntityType::class, array(
        'class'        => 'OnoMapBundle:Language',
        'choice_label' => 'libLanguageFr',
        'multiple'     => false,
      ))

      ->add('description', TextareaType::class, array("required"=>false))

      ->add('dtnaissance', DateType::class, array(
        "required" => false,
        "label" => "Date de naissance",
        "years" => range(1900, date("Y")),
        "format" => "dd / MM / yyyy"
      ));
    }

    public function getParent()
    {
        return 'FOS\UserBundle\Form\Type\ProfileFormType';

        // Or for Symfony < 2.8
        // return 'fos_user_registration';
    }


    // For Symfony 2.x
    public function getName()
    {
        return "fos_user_profile";
    }
}
