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
    private static $defaultChoices = [
        ArticleStatus::STATUS_RETURNED,
        ArticleStatus::STATUS_CHIEF_NEEDED,
        ArticleStatus::STATUS_ACCEPTED,
        ArticleStatus::STATUS_DECLINED,
    ];

    private static $needInfoChoices = [
        ArticleStatus::STATUS_NEED_INFO,
        ArticleStatus::STATUS_DECLINED,
    ];

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($this->isNewOrNeedInfo($builder)) {
            $choices = self::$needInfoChoices;
        } else {
            $choices = self::$defaultChoices;
        }

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

    private function getStatusChoices(array $choices)
    {
        $statusChoices = [];

        foreach ($choices as $choice) {
            if ($choice === ArticleStatus::STATUS_CHIEF_NEEDED) {
                $statusChoices['Vyžádat zásah šéfredaktora'] = $choice;
            } else {
                $statusChoices[ArticleStatus::getStatusName($choice)] = $choice;
            }
        }

        return $statusChoices;
    }

    private function isNewOrNeedInfo(FormBuilderInterface $builder)
    {
        $article = $builder->getData();

        if (!($article && $article instanceof Article)) {
            return false;
        }

        return $article->getEditor() === null;
    }
}
