<?php

namespace EditorialBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use EditorialBundle\Entity\Article;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;

class UserRepository extends EntityRepository implements UserLoaderInterface
{
    public function loadUserByUsername($username)
    {
        $query = $this->createQueryBuilder('u')
            ->where('u.username = :username OR u.email = :email')
            ->setParameter('username', $username)
            ->setParameter('email', $username)
            ->getQuery()
        ;

        try {
            return $query->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            return null;
        }
    }

    public function findOneByUsername($username)
    {
        $query = $this->createQueryBuilder('u')
            ->where('u.username = :username')
            ->setParameter('username', $username)
            ->setMaxResults(1)
            ->getQuery()
        ;

        try {
            return $query->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            return null;
        }
    }

    public function findOneByEmail($email)
    {
        $query = $this->createQueryBuilder('u')
            ->where('u.email = :email')
            ->setParameter('email', $email)
            ->setMaxResults(1)
            ->getQuery()
        ;

        try {
            return $query->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            return null;
        }
    }

    public function findEditors($chiefOnly = false)
    {
        $qb = $this->createQueryBuilder('u')
            ->join('u.roles', 'r')
        ;

        if ($chiefOnly) {
            $qb->where('r.role = :roleChiefEditor')
                ->setParameter('roleChiefEditor', 'ROLE_CHIEF_EDITOR');
        } else {
            $qb->where('r.role = :roleEditor')
                ->orWhere('r.role = :roleChiefEditor')
                ->setParameter('roleEditor', 'ROLE_EDITOR')
                ->setParameter('roleChiefEditor', 'ROLE_CHIEF_EDITOR');
        }

        return $qb->getQuery()->getResult();
    }

    public function findReviewersByArticle(Article $article)
    {
        return $this->createQueryBuilder('u')
            ->join('u.reviews', 'r')
            ->where('r.article = :article')
            ->setParameter('article', $article)
            ->getQuery()
            ->getResult()
        ;
    }
}
