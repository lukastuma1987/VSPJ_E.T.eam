<?php

namespace EditorialBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use EditorialBundle\Entity\User;

class ReviewRepository extends EntityRepository
{
    public function findEmptyByReviewer(User $reviewer)
    {
        return $this->createQueryBuilder('r')
            ->where('r.reviewer = :reviewer')
            ->andWhere('r.review IS NULL')
            ->setParameter('reviewer', $reviewer)
            ->getQuery()
            ->getResult()
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
        } catch (NonUniqueResultException $e) {
            return 0;
        }
    }
}
