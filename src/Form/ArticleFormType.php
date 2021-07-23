<?php

namespace App\Form;

use App\Entity\Article;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextAreaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;

class ArticleFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre',
                'attr' =>  ['class' => 'form-control', 'placeholder' => 'Saisir un titre pour l\'article...']
            ])
            ->add('image', TextType::class,
                [
                    'label' => 'Choisissez votre fichier'
                ]
            )
            ->add('content', CKEditorType::class, [
                'config' => array(
                    'language' => 'fr',
                ),
                'label' => 'Contenu'
            ])
            ->add('resume', TextAreaType::class, [
                'label' => 'Résumé',
                'attr' =>  ['class => form-control', 'placeholder => Résumé de l\'article']
            ])
            ->add('Enregistrer', SubmitType::class);
        
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
