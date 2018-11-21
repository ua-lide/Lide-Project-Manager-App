<?php

namespace APIProjectBundle\Form;

use APIProjectBundle\Entity\Projet;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProjetType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('project_name', TextType::class, array('required'=>false))
            ->add('user_id', IntegerType::class, array('required'=>false))
            ->add('environnement_id', IntegerType::class, array('required'=>false))
            ->add('is_public',CheckboxType::class, array('required'=>false))
            ->add('is_archived', CheckboxType::class, array('required'=>false))
            ->add('created_at', DateTimeType::class, array('required'=>false))
            ->add('updated_at', DateTimeType::class, array('required'=>false))
        ;
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array('data_class' => Projet::class));
    }

    public function getName() {
        return '';
    }
}