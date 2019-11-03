<?php

namespace EditorialBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use EditorialBundle\Entity\Magazine;

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
}
