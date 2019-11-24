<?php

namespace EditorialBundle\Form;

use Doctrine\ORM\EntityRepository;
use EditorialBundle\Entity\Article;
use EditorialBundle\Entity\Magazine;
use EditorialBundle\Enum\ArticleStatus;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;

class ArticleStatusType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $choices = [
            ArticleStatus::STATUS_RETURNED,
            ArticleStatus::STATUS_ACCEPTED,
            ArticleStatus::STATUS_DECLINED,
        ];

        $builder
            ->add('status', ChoiceType::class, [
                'label' => 'Status',
                'choices' => $this->getStatusChoices($choices),
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Article::class
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'editorialbundle_articlestatus';
    }

    // private

    public function getStatusChoices(array $choices)
    {
        $statusChoices = [];

        foreach ($choices as $choice) {
            $statusChoices[ArticleStatus::getStatusName($choice)] = $choice;
        }

        return $statusChoices;
    }
}
