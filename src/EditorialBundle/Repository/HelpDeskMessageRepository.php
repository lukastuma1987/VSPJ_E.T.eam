<?php

namespace EditorialBundle\Repository;

use Doctrine\ORM\EntityRepository;

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
}
