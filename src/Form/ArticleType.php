<?php

namespace App\Form;

use App\Entity\Article;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
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
            ->add('userIduser')
            ->add('categIdcateg')

        ;
    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([

            'data_class' => Article::class,

        ]);
        dump("fuck");

    }
}
