<?php

namespace EditorialBundle\Repository;

use Doctrine\ORM\EntityRepository;

class MagazineRepository extends EntityRepository
{
    public function findUpcoming()
    {
        return $this->createQueryBuilder('m')
            ->where('m.publishDate > :now')
            ->setParameter('now', new \DateTime())
            ->getQuery()
            ->getResult()
        ;
    }

    public function findWithFile()
    {
        return $this->createQueryBuilder('m')
            ->where('m.suffix IS NOT NULL')
            ->getQuery()
            ->getResult()
        ;
    }
}
