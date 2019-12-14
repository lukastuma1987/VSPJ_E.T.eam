<?php

namespace EditorialBundle\Model;

use EditorialBundle\Entity\User;

class UserRelations
{
    /** @var User */
    private $user;
    /** @var int */
    private $articlesCount;
    /** @var int */
    private $reviewsCount;
    /** @var int */
    private $assignedCount;
    /** @var int */
    private $commentCount;
    /** @var int */
    private $helpDeskMessagesCount;

    public function __construct(User $user)
    {
        $this->user = $user;
        $this->articlesCount = 0;
        $this->reviewsCount = 0;
        $this->assignedCount = 0;
        $this->commentCount = 0;
        $this->helpDeskMessagesCount = 0;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return int
     */
    public function getArticlesCount()
    {
        return $this->articlesCount;
    }

    /**
     * @param int $articlesCount
     * @return UserRelations
     */
    public function setArticlesCount($articlesCount)
    {
        $this->articlesCount = $articlesCount;

        return $this;
    }

    /**
     * @return int
     */
    public function getReviewsCount()
    {
        return $this->reviewsCount;
    }

    /**
     * @param int $reviewsCount
     * @return UserRelations
     */
    public function setReviewsCount($reviewsCount)
    {
        $this->reviewsCount = $reviewsCount;

        return $this;
    }

    /**
     * @return int
     */
    public function getAssignedCount()
    {
        return $this->assignedCount;
    }

    /**
     * @param int $assignedCount
     * @return UserRelations
     */
    public function setAssignedCount($assignedCount)
    {
        $this->assignedCount = $assignedCount;

        return $this;
    }

    /**
     * @return int
     */
    public function getCommentCount()
    {
        return $this->commentCount;
    }

    /**
     * @param int $commentCount
     * @return UserRelations
     */
    public function setCommentCount($commentCount)
    {
        $this->commentCount = $commentCount;

        return $this;
    }

    /**
     * @return int
     */
    public function getHelpDeskMessagesCount(): int
    {
        return $this->helpDeskMessagesCount;
    }

    /**
     * @param int $helpDeskMessagesCount
     * @return UserRelations
     */
    public function setHelpDeskMessagesCount(int $helpDeskMessagesCount): UserRelations
    {
        $this->helpDeskMessagesCount = $helpDeskMessagesCount;
        return $this;
    }

    /**
     * @return bool
     */
    public function hasRelations()
    {
        $relationsCounts = [
            $this->getArticlesCount(),
            $this->getAssignedCount(),
            $this->getReviewsCount(),
            $this->getCommentCount(),
            $this->getHelpDeskMessagesCount(),
        ];

        return array_sum($relationsCounts) > 0;
    }

    public function getRelationMessage()
    {
        return sprintf('Uživatel má %d článků, %d hodnocení, %d komentářů, %d odpovědí na helpdesku a spravuje recenzní řízení pro %d článků.',
            $this->getArticlesCount(),
            $this->getReviewsCount(),
            $this->getCommentCount(),
            $this->getHelpDeskMessagesCount(),
            $this->getAssignedCount()
        );
    }
}
