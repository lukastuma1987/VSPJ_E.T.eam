<?php

namespace EditorialBundle\Form\Filter;

use EditorialBundle\Entity\Magazine;
use EditorialBundle\Enum\ArticleStatus;
use EditorialBundle\Model\OwnerArticlesFilterModel;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OwnerArticlesFilterType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Název článku',
            ])
            ->add('status', ChoiceType::class, [
                'label' => 'Status',
                'choices' => array_flip(ArticleStatus::getAllAvailableStatuses()),
                'placeholder' => '',
            ])
            ->add('magazine', EntityType::class, [
                'label' => 'Číslo časopisu',
                'class' => Magazine::class,
                'choice_label' => 'choiceName',
                'placeholder' => '',
            ])
            ->add('createdFrom', DateType::class, [
                'label' => 'Vytvořeno od',
                'widget' => 'single_text',
            ])
            ->add('createdTill', DateType::class, [
                'label' => 'Vytvořeno do',
                'widget' => 'single_text',
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => OwnerArticlesFilterModel::class,
            'required' => false,
            'csrf_protection' => false,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return '';
    }
}
