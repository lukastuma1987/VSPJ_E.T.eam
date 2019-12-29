<?php

namespace EditorialBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\UnexpectedResultException;
use EditorialBundle\Entity\Magazine;
use EditorialBundle\Entity\User;
use EditorialBundle\Enum\ArticleStatus;
use EditorialBundle\Model\ArticleFilterModel;
use EditorialBundle\Model\EditorArticlesFilterModel;
use EditorialBundle\Model\OwnerArticlesFilterModel;
use EditorialBundle\Model\ReviewerFilterModel;

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
        } catch (UnexpectedResultException $e) {
            return 0;
        }
    }

    public function countInReviewByMagazine(Magazine $magazine)
    {
        $query = $this->createQueryBuilder('a')
            ->select('count(a)')
            ->where('a.magazine = :magazine')
            ->andWhere('a.status >= :assigned')
            ->setParameter('magazine', $magazine)
            ->setParameter('assigned', ArticleStatus::STATUS_ASSIGNED)
            ->getQuery()
        ;

        try {
            return $query->getSingleScalarResult();
        } catch (UnexpectedResultException $e) {
            return 0;
        }
    }

    public function countByAuthor(User $author)
    {
        $query =  $this->createQueryBuilder('a')
            ->select('COUNT (a.id)')
            ->where('a.owner = :owner')
            ->setParameter('owner', $author)
            ->getQuery()
        ;

        try {
            return $query->getSingleScalarResult();
        } catch (UnexpectedResultException $e) {
            return 0;
        }
    }

    public function countByEditor(User $editor)
    {
        $query =  $this->createQueryBuilder('a')
        ->select('COUNT (a.id)')
        ->where('a.editor = :editor')
        ->setParameter('editor', $editor)
        ->getQuery()
    ;

        try {
            return $query->getSingleScalarResult();
        } catch (UnexpectedResultException $e) {
            return 0;
        }
    }

    public function createOwnerFilteredQuery(User $owner, OwnerArticlesFilterModel $model)
    {
        $qb = $this->createQueryBuilder('a')
            ->join('a.magazine', 'm')
            ->where('a.owner = :owner')
            ->setParameter('owner', $owner)
        ;

        if ($name = $model->getName()) {
            $qb->andWhere('a.name LIKE :name')->setParameter('name', $this->likeify($name));
        }

        $status = $model->getStatus();
        if (is_numeric($status)) {
            $qb->andWhere('a.status = :status')->setParameter('status', $status);
        }

        if ($magazine = $model->getMagazine()) {
            $qb->andWhere('a.magazine = :magazine')->setParameter('magazine', $magazine);
        }

        if ($createdFrom = $model->getCreatedFrom()) {
            $qb->andWhere('a.created > :createdFrom')->setParameter('createdFrom', $createdFrom);
        }

        if ($createdTill = $model->getCreatedTill()) {
            $qb->andWhere('a.created < :createdTill')->setParameter('createdTill', $createdTill);
        }

        return $qb->getQuery();
    }

    public function createReviewerFilteredQuery(User $reviewer, ReviewerFilterModel $model)
    {
        $qb = $this->createQueryBuilder('a')
            ->join('a.reviews', 'r')
            ->join('a.owner', 'o')
            ->where('r.reviewer = :reviewer')
            ->setParameter('reviewer', $reviewer)
        ;

        if ($name = $model->getName()) {
            $qb->andWhere('a.name LIKE :name')->setParameter('name', $this->likeify($name));
        }

        $status = $model->getStatus();
        if (is_numeric($status)) {
            $qb->andWhere('a.status = :status')->setParameter('status', $status);
        }

        if ($owner = $model->getAuthor()) {
            $qb->andWhere('a.owner = :owner')->setParameter('owner', $owner);
        }

        return $qb->getQuery();
    }

    public function createFilteredQuery(ArticleFilterModel $model)
    {
        $qb = $this->createQueryBuilder('a')
            ->join('a.owner', 'o')
            ->leftJoin('a.editor', 'e')
            ->join('a.magazine', 'm')
        ;

        if ($name = $model->getName()) {
            $qb->andWhere('a.name LIKE :name')->setParameter('name', $this->likeify($name));
        }

        if ($owner = $model->getOwner()) {
            $qb->andWhere('a.owner = :owner')->setParameter('owner', $owner);
        }

        if ($editor = $model->getEditor()) {
            $qb->andWhere('a.editor = :editor')->setParameter('editor', $editor);
        }

        if ($magazine = $model->getMagazine()) {
            $qb->andWhere('a.magazine = :magazine')->setParameter('magazine', $magazine);
        }

        $status = $model->getStatus();
        if (is_numeric($status)) {
            $qb->andWhere('a.status = :status')->setParameter('status', $status);
        }

        if ($createdFrom = $model->getCreatedFrom()) {
            $qb->andWhere('a.created > :createdFrom')->setParameter('createdFrom', $createdFrom);
        }

        if ($createdTill = $model->getCreatedTill()) {
            $qb->andWhere('a.created < :createdTill')->setParameter('createdTill', $createdTill);
        }

        return $qb->getQuery();
    }

    public function createEditorFilteredQuery(EditorArticlesFilterModel $model, User $editor = null)
    {
        $qb = $this->createQueryBuilder('a')
            ->join('a.owner', 'o')
            ->join('a.magazine', 'm')
        ;

        if ($editor) {
            $qb->where('a.editor = :editor')->setParameter('editor', $editor);
        } else {
            $qb->where('a.editor IS NULL')
                ->andWhere('a.status IN (:allowedStatuses)')
                ->setParameter('allowedStatuses', [ArticleStatus::STATUS_NEW, ArticleStatus::STATUS_NEW_VERSION])
            ;
        }

        if ($name = $model->getName()) {
            $qb->andWhere('a.name LIKE :name')->setParameter('name', $this->likeify($name));
        }

        if ($owner = $model->getAuthor()) {
            $qb->andWhere('a.owner = :owner')->setParameter('owner', $owner);
        }

        if ($magazine = $model->getMagazine()) {
            $qb->andWhere('a.magazine = :magazine')->setParameter('magazine', $magazine);
        }

        $status = $model->getStatus();
        if (is_numeric($status)) {
            $qb->andWhere('a.status = :status')->setParameter('status', $status);
        }

        return $qb->getQuery();
    }

    // private

    private function likeify($str)
    {
        return '%' . str_replace(' ', '%', $str) . '%';
    }
}
