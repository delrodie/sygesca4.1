<?php

namespace App\Form\Sygesca3;

use App\Entity\Sygesca3\Objectif;
use App\Entity\Sygesca3\Region;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ObjectifType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            //->add('annee')
            ->add('valeur', IntegerType::class,['attr'=>['class'=>"form-control", 'autocomplete'=>"off"]])
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
            'data_class' => Objectif::class,
        ]);
    }
}
