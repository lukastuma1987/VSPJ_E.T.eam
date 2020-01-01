<?php

namespace EditorialBundle\Form;

use Doctrine\ORM\EntityRepository;
use EditorialBundle\Entity\Article;
use EditorialBundle\Entity\User;
use EditorialBundle\Enum\ArticleStatus;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChiefEditorEditArticleType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('status', ChoiceType::class, [
                'label' => 'Status',
                'choices' => array_flip(ArticleStatus::getAllAvailableStatuses()),
            ])
            ->add('editor', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'displayName',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->join('u.roles', 'r')
                        ->where('r.role = :roleEditor')
                        ->orWhere('r.role = :roleChiefEditor')
                        ->setParameter('roleEditor','ROLE_EDITOR')
                        ->setParameter('roleChiefEditor','ROLE_CHIEF_EDITOR')
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
            'data_class' => Article::class
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'editorialbundle_chiefeditorarticlestatus';
    }
}
