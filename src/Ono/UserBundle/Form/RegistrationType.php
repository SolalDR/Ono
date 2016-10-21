<?php

namespace Ono\UserBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\DateTime;

use FOS\UserBundle\Form\Type\ProfileFormType;


class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder->add('country', EntityType::class, array(
          'class'        => 'OnoMapBundle:Country',
          'choice_label' => 'libCountry',
          'multiple'     => false,
        ))
        ->add('name',       TextType::class, array(
          "label"=>"Nom"
        ))
        ->add('firstname',  TextType::class, array(
          'label'=>"Prénom"
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
        return 'FOS\UserBundle\Form\Type\RegistrationFormType';
    }

    public function getBlockPrefix()
    {
        return 'app_user_registration';
    }

    public function getName()
    {
        return $this->getBlockPrefix();
    }

}
