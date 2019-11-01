<?php

namespace EditorialBundle\Form;

use EditorialBundle\Entity\Magazine;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MagazineType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('publishDate', DateType::class, [
                'label' => 'Datum vydání',
            ])
            ->add('deadlineDate', DateType::class, [
                'label' => 'Uzávěrka',
            ])
            ->add('year', IntegerType::class, [
                'label' => 'Ročník'
            ])
            ->add('number', IntegerType::class, [
                'label' => 'Číslo',
            ])
            ->add('topics', CollectionType::class, [
                'label' => false,
                'entry_type' => MagazineTopicType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'error_bubbling' => true,
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Magazine::class,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'editorialbundle_magazine';
    }
}
