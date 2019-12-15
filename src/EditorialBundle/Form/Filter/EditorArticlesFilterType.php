<?php

namespace EditorialBundle\Form\Filter;

use Doctrine\ORM\EntityRepository;
use EditorialBundle\Entity\Magazine;
use EditorialBundle\Entity\User;
use EditorialBundle\Enum\ArticleStatus;
use EditorialBundle\Model\EditorArticlesFilterModel;
use EditorialBundle\Model\OwnerArticlesFilterModel;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditorArticlesFilterType extends AbstractType
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
            ->add('author', EntityType::class, [
                'label' => 'Vlastník',
                'class' => User::class,
                'choice_label' => 'displayName',
                'placeholder' => '',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->join('u.roles', 'r')
                        ->where('r.role = :roleAuthor')
                        ->setParameter('roleAuthor','ROLE_AUTHOR')
                    ;
                }
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => EditorArticlesFilterModel::class,
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
