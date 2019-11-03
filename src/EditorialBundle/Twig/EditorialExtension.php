<?php

namespace EditorialBundle\Twig;

use Doctrine\Common\Persistence\ManagerRegistry;
use EditorialBundle\Entity\Article;
use EditorialBundle\Entity\Magazine;
use EditorialBundle\Enum\ArticleStatus;
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
            new TwigFilter('countArticlesInReview', [$this, 'getArticlesInReviewCount']),
            new TwigFilter('statusName', [$this, 'getStatusName']),
            new TwigFilter('statusClass', [$this, 'getStatusClass']),
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

    public function getArticlesInReviewCount(Magazine $magazine)
    {
        $repository = $this->doctrine->getRepository(Article::class);

        return $repository->countInReviewByMagazine($magazine);
    }

    public function getStatusName(Article $article)
    {
        return ArticleStatus::getStatusName($article->getStatus());
    }

    public function getStatusClass(Article $article)
    {
        return ArticleStatus::getStatusClass($article->getStatus());
    }
}
