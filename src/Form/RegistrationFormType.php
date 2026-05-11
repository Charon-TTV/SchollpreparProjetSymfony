<?php

namespace App\Form;

use App\Entity\User;
use App\Enum\ConseillerType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType; // Importé pour une meilleure validation HTML5
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, [
                'label' => 'Nom d\'utilisateur',
                // Pas besoin de contraintes ici, Symfony utilise celles de l'entité User
            ])
            ->add('email', EmailType::class, [
                'label' => 'Adresse Email',
                // Pas besoin de contraintes ici, Symfony utilise celles de l'entité User
            ])
            ->add('userType', ChoiceType::class, [
                'mapped' => false,
                'label' => 'Je m\'inscris en tant que :',
                'choices'  => [
                    'Élève / Étudiant' => 'eleve',
                    'Conseiller d\'orientation' => 'conseiller',
                ],
                'expanded' => true,
                'multiple' => false,
                'data' => 'eleve',
            ])

            // --- CHAMPS CONSEILLER (mapped => false donc on ajoute les contraintes ici) ---
            ->add('telephone', TextType::class, [
                'label' => 'Numéro de téléphone',
                'mapped' => false,
                'required' => false,
                // On peut ajouter une validation si le JS montre ce champ
            ])
            ->add('specialite', ChoiceType::class, [
                'label' => 'JE SUIS UN...',
                'mapped' => false,
                'required' => false,
                'choices' => ConseillerType::cases(),
                'choice_value' => fn (?ConseillerType $choice) => $choice ? $choice->value : '',
                'choice_label' => fn (?ConseillerType $choice) => $choice ? $choice->value : '',
                'placeholder' => 'Choisissez votre profil...',
            ])
            ->add('biographie', TextareaType::class, [
                'label' => 'Courte biographie',
                'mapped' => false,
                'required' => false,
            ])

            // --- MOT DE PASSE ---
            ->add('plainPassword', PasswordType::class, [
                'mapped' => false,
                'label' => 'Mot de passe',
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le mot de passe est obligatoire.',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Votre mot de passe doit faire au moins {{ limit }} caractères.',
                        'max' => 4096,
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            // Permet de valider aussi les contraintes d'unicité de l'entité
            'allow_extra_fields' => true,
        ]);
    }
}
