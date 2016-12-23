<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('reference', TextType::class, array('label' => 'Référence :'))
            ->add('name', TextType::class, array('label' => 'Nom :'))
            ->add('description', TextareaType::class, array('label' => 'Description :'))
            ->add('categoryId', ChoiceType::class, array('choices' => array('Papeterie' => '1', 'Plastique' => '2'), 'label' => 'Catégorie :'))
            ->add('quantityStock', IntegerType::class, array('label' => 'Quantité en stock :'))
            ->add('quantityMin', IntegerType::class, array('label' => 'Quantité minimale :'))
            ->add('price', IntegerType::class, array('label' => 'Prix :'))
            ->add('expirationDate', DateType::class, array('label' => 'Date de péremption :'))
            ->add('photo', FileType::class, array('label' => 'Photo :'))
            ->add('add', SubmitType::class, array('label' => 'Ajouter'))
        ;
    }
}