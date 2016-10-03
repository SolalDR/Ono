<?php
// src/OC/PlatformBundle/Form/AdvertEditType.php

namespace Ono\MapBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


use Ono\MapBundle\Form\ResponseType;

class ResponseAdminType extends ResponseType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    parent::buildForm($builder, $options);
    $builder->add('dtcreation',   DateTimeType::class)
    ->add('question',     EntityType::class, array(
      'class'        => 'OnoMapBundle:Question',
      'choice_label' => 'libQuestion',
      'multiple'     => false,
    ));
  }

  public function getName()
  {
    return 'ono_admin_add_question';
  }

  // public function getParent()
  // {
  //   return new ResponseType();
  // }
}
