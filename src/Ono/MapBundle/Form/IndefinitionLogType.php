<?php
// src/OC/PlatformBundle/Form/IndefinitionAdminType.php

namespace Ono\MapBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


use Ono\MapBundle\Form\ResponseType;

class IndefinitionLogType extends IndefinitionType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    parent::buildForm($builder, $options);
    $builder->remove('author');
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
