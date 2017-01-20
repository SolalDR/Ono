<?php

namespace Ono\MapBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Vich\UploaderBundle\Form\Type\VichFileType;


class ResourceType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title', TextType::class, array(
          "label" => false,
          "attr"=> array(
            "placeholder" => "Titre"
          )
        ))
        ->add('legend', TextType::class, array(
          "label" => false,
          "attr"=> array(
            "placeholder" => "Légende"
          )
        ))
        ->add('file', VichFileType::class, array(
            "required" => false,
            "label"=> false,
            "attr"=> array(
              "class" => "vich-type form-display"
            )
          ))
        ->add('filename', TextType::class, array(
            "label"=> false,
            "required" => false,
            "attr"=> array(
              "placeholder" => "URL",
              "class" => "url-type form-hide"
            )
          ))
          ->add('isDistant', CheckboxType::class, array(
              "label" => "Distant",
              "required" => false,
              "attr"=> array(
                "class"=>"distant-type"
              )
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Ono\MapBundle\Entity\Resource'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'ono_mapbundle_resource';
    }


}
