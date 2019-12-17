<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class ArticleType extends AbstractType
{



    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('titre')
            ->add('slug')
            ->add('texte')
            ->add('thedate',DateTimeType::class,[
                'date_widget'=>'choice',
                'required'=>true,
                'years' => range((int) date('Y') - 50, (int) date('Y') + 50),

            ])
            ->add('userIduser',null,['required' => true])

            /* by_reference => false permet à la relation many2many de fonctionner
                pour l'ajout , modification / suppression de catégories sur l'article
            */
            ->add('categIdcateg',null,['multiple'=>true,'expanded'=>true,'by_reference' => false,
            ])

        ;
    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([

            'data_class' => Article::class,

        ]);

    }
}
