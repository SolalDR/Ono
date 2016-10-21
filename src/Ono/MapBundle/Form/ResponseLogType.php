<?php
// src/OC/PlatformBundle/Form/AdvertEditType.php

namespace Ono\MapBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


use Ono\MapBundle\Form\ResponseType;

class ResponseLogType extends ResponseType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    parent::buildForm($builder, $options);
    $builder
    ->remove("author")
    ->remove("dtnaissance")
    ->remove("language")
    ->remove("country");
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
