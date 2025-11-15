<?php

namespace App\Form;

use App\Entity\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Form\Type\VichImageType;

class ContactFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('fullName', TextType::class, [
                'label' => 'Nom et prénom',
                'attr' => [
                    'placeholder' => 'Votre nom et prénom',
                    'minlength'   => 2,
                    'maxlength'   => 100,
                ],
                'row_attr' => ['class' => 'mb-3'],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Merci d’indiquer vos nom et prénom.']),
                    new Assert\Length(['min' => 2, 'max' => 100]),
                ],
            ])

            ->add('email', EmailType::class, [
                'label' => 'Adresse e-mail',
                'attr' => [
                    'placeholder' => 'nom@exemple.fr',
                ],
                'row_attr' => ['class' => 'mb-3'],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Merci d’indiquer votre adresse e-mail.']),
                    new Assert\Email(['message' => 'Merci de saisir une adresse e-mail valide.']),
                ],
            ])

            ->add('subject', TextType::class, [
                'label' => 'Objet de votre demande',
                'attr' => [
                    'placeholder' => 'Affûtage de couteaux, rénovation de volets, dépannage...',
                    'minlength'   => 2,
                    'maxlength'   => 150,
                ],
                'row_attr' => ['class' => 'mb-3'],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Merci d’indiquer l’objet de votre demande.']),
                    new Assert\Length(['min' => 2, 'max' => 150]),
                ],
            ])

            // 👇 ADAPTE ICI LE NOM DU CHAMP SELON TON ENTITÉ (featuredImageFile…)
            ->add('featuredImageFile', VichImageType::class, [
                'label'         => 'Joindre une photo (optionnel)',
                'required'      => false,
                'allow_delete'  => false,
                'download_uri'  => false,
                'image_uri'     => false,
                'row_attr'      => ['class' => 'mb-3'],
                'attr'          => [
                    'class'       => 'form-control',
                ],
                'constraints'   => [
                    new Assert\Image([
                        'maxSize' => '8M',
                        'maxSizeMessage' => 'L’image ne peut pas dépasser {{ limit }}.',
                    ]),
                ],
            ])

            ->add('message', TextareaType::class, [
                'label' => 'Votre message',
                'attr' => [
                    'rows'        => 6,
                    'placeholder' => 'Expliquez votre besoin, le type de travaux souhaité, vos disponibilités...',
                ],
                'row_attr' => ['class' => 'mb-3'],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Merci de décrire votre demande.']),
                    new Assert\Length([
                        'min' => 10,
                        'minMessage' => 'Votre message doit contenir au moins {{ limit }} caractères.',
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
        ]);
    }
}