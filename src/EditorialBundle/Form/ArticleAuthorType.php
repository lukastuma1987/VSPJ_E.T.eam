<?php

namespace EditorialBundle\Form;

use EditorialBundle\Entity\ArticleAuthor;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleAuthorType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fullName', TextType::class, [
                'label' => 'Celé jméno',
            ])
            ->add('email', EmailType::class, [
                'label' => 'E-mail',
            ])
            ->add('workplace', TextType::class, [
                'label' => 'Pracoviště',
            ])
            ->add('address', TextType::class, [
                'label' => 'Kontaktní adresa',
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ArticleAuthor::class
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'editorialbundle_articleauthor';
    }
}
