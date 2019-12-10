<?php

namespace EditorialBundle\Form;

use Doctrine\ORM\EntityRepository;
use EditorialBundle\Entity\Article;
use EditorialBundle\Entity\Magazine;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;

class ArticleType extends AbstractType
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
            ->add('magazine', EntityType::class, [
                'class' => Magazine::class,
                'label' => 'Číslo časopisu',
                'choice_label' => 'choiceName',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('m')
                        ->where('m.deadlineDate > :now')
                        ->setParameter('now', new \DateTime())
                    ;
                }
            ])
            ->add('file', FileType::class, [
                'label' => 'Článek',
                'mapped' => false,
                'constraints' => [
                    new File(['maxSize' => '20M', 'maxSizeMessage' => 'Maximální velikost je 20MB']),
                    new NotBlank(['message' => 'Vyberte soubor se článkem']),
                ],
            ])
            ->add('authors', CollectionType::class, [
                'label' => false,
                'entry_type' => ArticleAuthorType::class,
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
            'data_class' => Article::class
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'editorialbundle_article';
    }
}
