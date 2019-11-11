<?php

namespace EditorialBundle\Form;

use EditorialBundle\Entity\Review;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReviewType extends AbstractType
{
    private static $levelChoices = [
        '1' => '1',
        '2' => '2',
        '3' => '3',
        '4' => '4',
        '5' => '5',
    ];

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('benefitLevel', ChoiceType::class, [
                'choices' => self::$levelChoices,
                'label' => 'Aktuálnost, zajímavost a přínosnost (1 - nejlepší, 5 - nejhorší)',
            ])
            ->add('originalityLevel', ChoiceType::class, [
                'choices' => self::$levelChoices,
                'label' => 'Originalita (1 - nejlepší, 5 - nejhorší)',
            ])
            ->add('professionalLevel', ChoiceType::class, [
                'choices' => self::$levelChoices,
                'label' => 'Odborná úroveň (1 - nejlepší, 5 - nejhorší)',
            ])
            ->add('languageLevel', ChoiceType::class, [
                'choices' => self::$levelChoices,
                'label' => 'Jazyková a stylistická úroveň (1 - nejlepší, 5 - nejhorší)',
            ])
            ->add('review', TextareaType::class, [
                'label' => 'Hodnocení',
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Review::class,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'editorialbundle_review';
    }
}
