<?php

namespace EditorialBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\UnexpectedResultException;
use EditorialBundle\Entity\User;

class ArticleCommentRepository extends EntityRepository
{

    public function countByUser(User $user)
    {
        $query =  $this->createQueryBuilder('ac')
            ->select('COUNT (ac.id)')
            ->where('ac.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
        ;

        try {
            return $query->getSingleScalarResult();
        } catch (UnexpectedResultException $e) {
            return 0;
        }
    }
}
