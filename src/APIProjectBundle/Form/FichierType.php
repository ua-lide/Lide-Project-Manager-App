<?php

namespace APIProjectBundle\Form;

use APIProjectBundle\Entity\Fichier;
use APIProjectBundle\Entity\Projet;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class FichierType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('name', TextType::class, array('required'=>false))
            ->add('path', TextType::class, array('required'=>false))
        ;
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array('data_class' => Fichier::class));
    }

    public function getName() {
        return '';
    }

}