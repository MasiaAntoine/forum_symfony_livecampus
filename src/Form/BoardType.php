<?php

namespace App\Form;

use App\Entity\Board;
use App\Entity\Category;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class BoardType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('rolesAllowed', ChoiceType::class, [
                'label' => 'Visible by',
                'required' => true,
                'multiple' => true,
                'expanded' => true,
                'choices' => [
                    'User' => 'ROLE_USER',
                    'Insider' => 'ROLE_INSIDER',
                    'External' => 'ROLE_EXTERNAL',
                    'Collaborator' => 'ROLE_COLLABORATOR',
                ],
                'data' => ['ROLE_USER'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de choisir un rôle',
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Board::class,
        ]);
    }
}
