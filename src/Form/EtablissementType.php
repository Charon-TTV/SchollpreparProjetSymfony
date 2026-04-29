<?php

namespace App\Form;

use App\Entity\Etablissement;
use App\Entity\Filiere;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class EtablissementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom de l\'établissement'
            ])
            ->add('ville', TextType::class, [
                'label' => 'Ville'
            ])
            ->add('localisation', TextType::class, [
                'label' => 'Adresse / Quartier'
            ])
            ->add('type', TextType::class, [
                'label' => 'Type d\'établissement'
            ])
            // Modification du champ image pour l'upload de fichier
            ->add('image', FileType::class, [
                'label' => 'Photo / Logo (Fichier image)',
                'mapped' => false, // On ne lie pas directement à l'entité pour gérer l'upload manuellement
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '2M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/webp',
                        ],
                        'mimeTypesMessage' => 'Veuillez uploader une image valide (JPG, PNG, WEBP)',
                    ])
                ],
            ])
            ->add('filieres', EntityType::class, [
                'class' => Filiere::class,
                'choice_label' => 'nom', // Utilisation du 'nom' au lieu de l'id pour plus de clarté
                'multiple' => true,
                'expanded' => true, // Garde le format liste déroulante (mieux pour le design actuel)
                'required' => false,
                'by_reference' => false,
                'label' => 'Sélectionner les filières'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Etablissement::class,
        ]);
    }
}
