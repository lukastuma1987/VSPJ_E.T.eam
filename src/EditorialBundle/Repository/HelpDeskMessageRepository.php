<?php

namespace EditorialBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\UnexpectedResultException;
use EditorialBundle\Entity\User;

class HelpDeskMessageRepository extends EntityRepository
{
    public function findUnanswered()
    {
        return $this->createQueryBuilder('h')
            ->where('h.answer IS NULL')
            ->andWhere('h.answered IS NULL')
            ->andWhere('h.manager IS NULL')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findAnswered()
    {
        return $this->createQueryBuilder('h')
            ->where('h.answer IS NOT NULL')
            ->andWhere('h.answered IS NOT NULL')
            ->andWhere('h.manager IS NOT NULL')
            ->getQuery()
            ->getResult()
        ;
    }

    public function countByManager(User $manager)
    {
        $query =  $this->createQueryBuilder('h')
            ->select('COUNT (h.id)')
            ->where('h.manager = :manager')
            ->setParameter('manager', $manager)
            ->getQuery()
        ;

        try {
            return $query->getSingleScalarResult();
        } catch (UnexpectedResultException $e) {
            return 0;
        }
    }
}
