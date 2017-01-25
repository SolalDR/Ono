<?php

namespace Ono\MapBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;

class ResponseType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('content', CKEditorType::class, array(
                'config' => array(
                    'uiColor' => '#ffffff',
                )
            ))
            ->add('author',     TextType::class)
            ->add('dtnaissance', DateType::class, array(
              "required" => true,
              "label" => "Date de naissance",
              "years" => range(1900,2016),
              "format" => "dd / MM / yyyy"
            ))
            ->add('country', EntityType::class, array(
              'class'        => 'OnoMapBundle:Country',
              'choice_label' => 'libCountry',
              'multiple'     => false,
            ))
            ->add('language', EntityType::class, array(
              'class'        => 'OnoMapBundle:Language',
              'choice_label' => 'libLanguageFr',
              'multiple'     => false,
            ))
            ->add('resource', ResourceType::class, array(
              "required" => false
            ))
            ->add('save',       SubmitType::class)
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Ono\MapBundle\Entity\Response'
        ));
    }
}
