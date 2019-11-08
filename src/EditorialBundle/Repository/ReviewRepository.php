<?php

namespace EditorialBundle\Repository;

use Doctrine\ORM\EntityRepository;
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
}
