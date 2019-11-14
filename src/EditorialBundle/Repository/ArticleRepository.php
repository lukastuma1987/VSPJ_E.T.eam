<?php

namespace EditorialBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use EditorialBundle\Entity\Magazine;
use EditorialBundle\Entity\User;
use EditorialBundle\Enum\ArticleStatus;

class ArticleRepository extends EntityRepository
{
    public function countByMagazine(Magazine $magazine)
    {
        $query = $this->createQueryBuilder('a')
            ->select('count(a)')
            ->where('a.magazine = :magazine')
            ->setParameter('magazine', $magazine)
            ->getQuery()
        ;

        try {
            return $query->getSingleScalarResult();
        } catch (NonUniqueResultException $e) {
            return 0;
        }
    }

    public function findUnassigned()
    {
        return $this->createQueryBuilder('a')
            ->where('a.editor IS NULL')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findByEditor(User $editor)
    {
        return $this->createQueryBuilder('a')
            ->where('a.editor = :editor')
            ->setParameter('editor', $editor)
            ->getQuery()
            ->getResult()
        ;
    }

    public function countInReviewByMagazine(Magazine $magazine)
    {
        $query = $this->createQueryBuilder('a')
            ->select('count(a)')
            ->where('a.magazine = :magazine')
            ->andWhere('a.status >= :assigned')
            ->setParameter('magazine', $magazine)
            ->setParameter('assigned', ArticleStatus::STATUS_ASSIGNED)
            ->getQuery()
        ;

        try {
            return $query->getSingleScalarResult();
        } catch (NonUniqueResultException $e) {
            return 0;
        }
    }



    public function findReviewedByReviewer(User $reviewer)
    {
        return $this->createQueryBuilder('a')
            ->join('a.reviews', 'r')
            ->where('r.reviewer = :reviewer')
            ->andWhere('r.review IS NOT NULL AND r.review != :empty')
            ->setParameter('reviewer', $reviewer)
            ->setParameter('empty', '')
            ->getQuery()
            ->getResult()
        ;
    }
}
