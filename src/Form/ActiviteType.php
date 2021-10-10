<?php

namespace App\Form;

use App\Entity\Activite;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ActiviteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
	        ->add('code', TextType::class,['attr'=>['class'=>'form-control', 'autocomplete'=>"off"]])
            ->add('nom', TextType::class,['attr'=>['class'=>'form-control', 'autocomplete'=>"off"]])
            ->add('description', TextareaType::class,['attr'=>['class'=>"form-control"]])
            ->add('montant', IntegerType::class,['attr'=>['class'=>"form-control", 'autocomplete'=>"off"]])
            ->add('debutActivite', TextType::class,['attr'=>['class'=>'form-control date', 'autocomplete'=>"off", 'placeholder'=>"Debut activte"]])
            ->add('finActivite', TextType::class,['attr'=>['class'=>'form-control date', 'autocomplete'=>"off", 'placeholder'=>"Fin activité"]])
            ->add('debutPeriode', TextType::class,['attr'=>['class'=>'form-control date', 'autocomplete'=>"off", 'placeholder'=>"Début période"]])
            ->add('finPeriode', TextType::class,['attr'=>['class'=>'form-control date', 'autocomplete'=>"off", 'placeholder'=>"Fin période"]])
            //->add('slug')
            //->add('createdAt')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Activite::class,
        ]);
    }
}
