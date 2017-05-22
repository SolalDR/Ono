<?php
// src/OC/PlatformBundle/Form/IndefinitionAdminType.php

namespace Ono\MapBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


use Ono\MapBundle\Form\ResponseType;

class IndefinitionAdminType extends IndefinitionType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder->add('tag',     EntityType::class, array(
      'class'        => 'OnoMapBundle:Tag',
      'choice_label' => 'libTag',
      'multiple'     => false,
    ));
    parent::buildForm($builder, $options);
  }

  public function getName()
  {
    return 'ono_admin_add_tag';
  }

  // public function getParent()
  // {
  //   return new ResponseType();
  // }
}
