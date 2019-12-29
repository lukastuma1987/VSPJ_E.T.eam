<?php

namespace EditorialBundle\Form;

use EditorialBundle\Model\PasswordChangeModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PasswordChangeType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('current', PasswordType::class, [
                'label' => 'Aktuální heslo',
            ])
            ->add('new', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Hesla se musí shodovat.',
                'first_options'  => ['label' => 'Nové heslo'],
                'second_options' => ['label' => 'Nové heslo znovu'],
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PasswordChangeModel::class,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'editorialbundle_passwordchange';
    }
}
