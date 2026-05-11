<?php
// src/Form/ProfileType.php
namespace App\Form;

use App\Entity\User;
use App\Entity\Conseiller;
use App\Enum\ConseillerType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $options['data'];

        $builder
            ->add('nom', TextType::class, ['required' => false])
            ->add('prenom', TextType::class, ['required' => false])
            ->add('avatarFile', FileType::class, [
                'label' => 'Photo de profil',
                'mapped' => false, // On gère l'upload manuellement dans le controller
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '2M',
                        'mimeTypes' => ['image/jpeg', 'image/png'],
                        'mimeTypesMessage' => 'Veuillez uploader une image valide (PNG, JPG)',
                    ])
                ],
            ]);

        if ($user instanceof Conseiller) {
            $builder
                ->add('telephone', TextType::class, ['required' => false])
                ->add('type', EnumType::class, [
                    'class' => ConseillerType::class,
                    'choice_label' => fn (ConseillerType $choice) => $choice->value, // Utilise les labels (Ancien élève, etc.)
                ])
                ->add('biographie', TextareaType::class, ['required' => false]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => User::class]);
    }
}
