<?php

namespace EditorialBundle\Twig;

use Doctrine\Common\Persistence\ManagerRegistry;
use EditorialBundle\Entity\Article;
use EditorialBundle\Entity\Magazine;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class EditorialExtension extends AbstractExtension
{
    /** @var ManagerRegistry */
    private $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function getFilters()
    {
        return [
            new TwigFilter('operation', [$this, 'getOperation']),
            new TwigFilter('countArticles', [$this, 'getArticlesCount']),
        ];
    }

    public function getOperation($entity)
    {
        $exists = $entity && is_object($entity) && method_exists($entity, 'getId') && $entity->getId();

        return $exists ? 'Upravit' : 'Vytvořit';
    }

    public function getArticlesCount(Magazine $magazine)
    {
        $repository = $this->doctrine->getRepository(Article::class);

        return $repository->countByMagazine($magazine);
    }
}
