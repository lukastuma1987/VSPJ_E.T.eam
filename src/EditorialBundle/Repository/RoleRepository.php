<?php

namespace EditorialBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;

class RoleRepository extends EntityRepository
{
    public function findOneByRole($role)
    {
        $query = $this->createQueryBuilder('r')
            ->where('r.role = :role')
            ->setParameter('role', $role)
            ->setMaxResults(1)
            ->getQuery()
        ;

        try {
            return $query->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            return null;
        }
    }
}
