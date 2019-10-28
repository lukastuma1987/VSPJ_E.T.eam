<?php

namespace EditorialBundle\Form;

use EditorialBundle\Entity\Role;
use EditorialBundle\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, [
                'label' => 'Uživatelské jméno',
            ])
            ->add('email', EmailType::class, [
                'label' => 'E-mail',
            ])
            ->add('plaintextPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'required' => $builder->getData() ? false : true,
                'invalid_message' => 'Hesla se musí shodovat.',
                'first_options'  => ['label' => 'Heslo' . ($builder->getData() ? ' (nepovinné)' : '')],
                'second_options' => ['label' => 'Heslo znovu' . ($builder->getData() ? ' (nepovinné)' : '')],
            ])
            ->add('roles', EntityType::class, [
                'class' => Role::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true,
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'editorialbundle_user';
    }
}
