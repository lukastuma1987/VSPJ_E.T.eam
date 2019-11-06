<?php

namespace EditorialBundle\Form;

use Doctrine\ORM\EntityRepository;
use EditorialBundle\Entity\User;
use EditorialBundle\Model\AssignReviewersModel;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AssignReviewersType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var AssignReviewersModel|null $model */
        $model = $builder->getData();
        $owner = $model ? $model->getOwner() : null;
        $editor = $model ? $model->getEditor() : null;

        $builder
            ->add('deadline', DateType::class, [
                'label' => 'Deadline pro odevzdání hodnocení',
            ])
            ->add('reviewers', EntityType::class, [
                'label' => 'Recenzenti (podržte CTRL pro výběr více možností)',
                'multiple' => true,
                'class' => User::class,
                'choice_label' => 'displayName',
                'query_builder' => function (EntityRepository $er) use ($owner, $editor) {
                    return $er->createQueryBuilder('u')
                        ->join('u.roles', 'r')
                        ->where('r.role = :roleReviewer')
                        ->andWhere('u != :owner')
                        ->andWhere('u != :editor')
                        ->setParameter('roleReviewer', 'ROLE_REVIEWER')
                        ->setParameter('owner', $owner)
                        ->setParameter('editor', $editor)
                    ;
                },
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => AssignReviewersModel::class
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'editorialbundle_assignreviewers';
    }
}
