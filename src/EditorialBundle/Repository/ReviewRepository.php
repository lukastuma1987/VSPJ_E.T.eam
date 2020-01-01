<?php

namespace EditorialBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\UnexpectedResultException;
use EditorialBundle\Entity\User;

class ReviewRepository extends EntityRepository
{
    public function createEmptyByReviewerQuery(User $reviewer)
    {
        return $this->createQueryBuilder('r')
            ->join('r.article', 'a')
            ->join('a.owner', 'o')
            ->where('r.reviewer = :reviewer')
            ->andWhere('r.review IS NULL')
            ->setParameter('reviewer', $reviewer)
            ->getQuery()
        ;
    }

    public function countByReviewer(User $reviewer)
    {
        $query =  $this->createQueryBuilder('r')
            ->select('COUNT (r.id)')
            ->where('r.reviewer = :reviewer')
            ->setParameter('reviewer', $reviewer)
            ->getQuery()
        ;

        try {
            return $query->getSingleScalarResult();
        } catch (UnexpectedResultException $e) {
            return 0;
        }
    }
}
