<?php

namespace App\Form\Sygesca;

use App\Entity\Sygesca\District;
use App\Entity\Sygesca\Region;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DistrictType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class,['attr'=>['class'=>'form-control', 'placeholder'=>"Nom du district"]])
            //->add('doyenne')
            //->add('slug')
            //->add('publiePar')
            //->add('modifiePar')
            //->add('publieLe')
            //->add('modifieLe')
            ->add('region', EntityType::class,[
	            'attr'=>['class' => 'form-control select2'],
	            'class' => Region::class,
	            'query_builder' => function(EntityRepository $er){
		            return $er->listeActive();
	            },
	            'choice_label' => 'nom',
	            'label' => 'Region'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => District::class,
        ]);
    }
}
