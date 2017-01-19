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
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Ono\MapBundle\Form\TagType;
use Ono\MapBundle\Form\ResourceType;


class ArticleType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            // ->add('content',    TextareaType::class)
            ->add('title', TextType::class)
            ->add('description', TextareaType::class)
            ->add('content', CKEditorType::class, array(
                'config' => array(
                    'uiColor' => '#ffffff',
                )
            ))
            ->add('published', CheckboxType::class, array(
              "required" => false
            ))
            ->add('country', EntityType::class, array(
              'class'        => 'OnoMapBundle:Country',
              'choice_label' => 'libCountry',
              'multiple'     => false,
            ))
            ->add('tags', CollectionType::class, array(
              'entry_type'   => TagType::class,
              'allow_add'    => true,
              'allow_delete' => true
            ))
            ->add('language', EntityType::class, array(
              'class'        => 'OnoMapBundle:Language',
              'choice_label' => 'libLanguageFr',
              'multiple'     => false,
            ))
            ->add('themes',  EntityType::class, array(
              'class'        => 'OnoMapBundle:Theme',
              'choice_label' => 'libTheme',
              'multiple'     => true,
              "expanded" => true
            ))
            ->add('resources', CollectionType::class, array(
              'entry_type'   => ResourceType::class,
              'allow_add'    => true,
              'allow_delete' => true
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
            'data_class' => 'Ono\MapBundle\Entity\Article'
        ));
    }
}
