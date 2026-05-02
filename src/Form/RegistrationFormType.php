<?php

namespace App\Form;

use App\Entity\User;
use App\Enum\ConseillerType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, [
                'label' => 'Nom d\'utilisateur',
                'attr' => ['placeholder' => 'ex: Jean228']
            ])
            ->add('email', TextType::class, [
                'label' => 'Adresse Email',
                'attr' => ['placeholder' => 'exemple@gmail.com']
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
                'data' => 'eleve', // Par défaut
            ])
            // --- CHAMPS CONSEILLER (Masqués par défaut en JS) ---
            ->add('telephone', TextType::class, [
                'label' => 'Numéro de téléphone',
                'mapped' => false,
                'required' => false,
            ])
            ->add('specialite', ChoiceType::class, [
                'label' => 'JE SUIS UN...',
                'mapped' => false,
                'required' => false,
                'choices' => ConseillerType::cases(), // Récupère tous les cas de l'enum
                'choice_value' => fn (?ConseillerType $choice) => $choice ? $choice->value : '',
                'choice_label' => fn (?ConseillerType $choice) => $choice ? $choice->value : '',
                'placeholder' => 'Choisissez votre profil...',
                'attr' => ['class' => 'form-control custom-input select-style']
            ])
            ->add('biographie', TextareaType::class, [
                'label' => 'Courte biographie',
                'mapped' => false,
                'required' => false,
                'attr' => ['rows' => 3]
            ])
            // ----------------------------------------------------
            ->add('plainPassword', PasswordType::class, [
                'mapped' => false,
                'label' => 'Mot de passe',
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank(['message' => 'Entrez un mot de passe']),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Au moins {{ limit }} caractères',
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
        ]);
    }
}
