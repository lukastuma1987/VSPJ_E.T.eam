<?php

namespace EditorialBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;

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
}
