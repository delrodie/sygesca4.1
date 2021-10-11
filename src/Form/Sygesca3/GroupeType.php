<?php

namespace App\Form\Sygesca3;

use App\Entity\Sygesca3\District;
use App\Entity\Sygesca3\Groupe;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GroupeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('paroisse', TextType::class,['attr'=>['class'=>'form-control', 'autocomplete'=>'off']])
            //->add('localite')
            //->add('slug')
            //->add('publiePar')
            //->add('modifiePar')
            //->add('publieLe')
            //->add('modifieLe')
            ->add('district', EntityType::class,[
	            'attr'=>['class' => 'form-control select2'],
	            'class' => District::class,
	            'query_builder' => function(EntityRepository $er){
		            return $er->liste();
	            },
	            'choice_label' => 'nom',
	            'label' => 'District'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Groupe::class,
        ]);
    }
}
