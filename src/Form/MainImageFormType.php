<?php

namespace App\Form;

use App\Entity\MainImage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints\Image;

class MainImageFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', FileType::class, [
                'label' => 'Image principale du site :',
                'constraints' => [
                    new Image([
                        'maxSize' => '5M'
                    ])
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label'=>'Enregistrer'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => MainImage::class,
        ]);
    }
}
