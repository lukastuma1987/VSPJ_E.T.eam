<?php

namespace EditorialBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use EditorialBundle\Entity\Review;

class ArticleVersionRepository extends EntityRepository
{
    public function findByArticleAndVersionId($articleId, $versionId)
    {
        $query = $this->createQueryBuilder('v')
            ->join('v.article', 'a')
            ->where('a.id = :articleId')
            ->andWhere('v.id = :versionId')
            ->setParameter('articleId', $articleId)
            ->setParameter('versionId', $versionId)
            ->getQuery()
        ;

        try {
            return $query->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            return null;
        }
    }


    public function findArticleVersionByReview(Review $review)
    {
        $query = $this->createQueryBuilder('v')
            ->select('COUNT(v.id)')
            ->join('v.article', 'a')
            ->join('a.reviews', 'r')
            ->where('r = :review')
            ->setParameter('review', $review)
            ->getQuery()
        ;

        try {
            return $query->getSingleScalarResult();
        } catch (NonUniqueResultException $e) {
            return 1;
        }
    }
}
