<?php

namespace EditorialBundle\Security;

use EditorialBundle\Entity\Article;
use EditorialBundle\Entity\User;
use EditorialBundle\Enum\ArticleStatus;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class ArticleVoter extends Voter
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    const DOWNLOAD = 'DOWNLOAD';
    const DETAIL = 'DETAIL';
    const COMMENT = 'COMMENT';
    const CHANGE_STATUS = 'CHANGE_STATUS';
    const UPDATE = 'UPDATE';
    const ADD_REVIEWER = 'ADD_REVIEWER';

    protected static $supportedAttributes = [
        self::DOWNLOAD,
        self::COMMENT,
        self::DETAIL,
        self::CHANGE_STATUS,
        self::UPDATE,
        self::ADD_REVIEWER,
    ];

    protected function supports($attribute, $subject)
    {
        if (!in_array($attribute, self::$supportedAttributes)) {
            return false;
        }

        if (!$subject instanceof Article) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        switch ($attribute) {
            case self::DOWNLOAD:
            case self::DETAIL:
            case self::COMMENT:
                return $this->isEditorOrRelatedToArticle($subject, $user);
            case self::CHANGE_STATUS:
                return $this->canChangeStatus($subject, $user);
            case self::UPDATE:
                return $this->canUpdate($subject, $user);
            case self::ADD_REVIEWER:
                return $this->canAddReviewer($subject, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    // private
    private function canAssignReviewer(Article $article, User $user)
    {
        if ($this->security->isGranted('ROLE_CHIEF_EDITOR')) {
            return true;
        }

        return $user === $article->getEditor();
    }

    private function isEditorOrRelatedToArticle(Article $article, User $user)
    {
        if ($this->security->isGranted('ROLE_EDITOR')) {
            return true;
        }

        if ($user === $article->getOwner()) {
            return true;
        }

        foreach ($article->getReviews() as $review) {
            if ($user === $review->getReviewer()) {
                return true;
            }
        }

        return false;
    }

    private function canChangeStatus(Article $article, User $user)
    {
        $allowedStatuses = [
            ArticleStatus::STATUS_NEW,
            ArticleStatus::STATUS_NEED_INFO,
            ArticleStatus::STATUS_REVIEWS_FILLED,
            ArticleStatus::STATUS_NEW_VERSION,
        ];

        if (!in_array($article->getStatus(), $allowedStatuses)) {
            return false;
        }

        if ($this->security->isGranted('ROLE_CHIEF_EDITOR')) {
            return true;
        }

        $articleEditor = $article->getEditor();

        return $articleEditor === null || $user === $articleEditor;
    }

    private function canUpdate(Article $article, User $user)
    {
        $allowedStatuses = [
            ArticleStatus::STATUS_NEED_INFO,
            ArticleStatus::STATUS_RETURNED,
        ];

        return $user === $article->getOwner() && in_array($article->getStatus(), $allowedStatuses);
    }

    private function canAddReviewer(Article $article, User $user)
    {
        $allowedStatuses = [
            ArticleStatus::STATUS_REVIEWS_FILLED,
            ArticleStatus::STATUS_NEW_VERSION,
            ArticleStatus::STATUS_ASSIGNED,
        ];

        if (!in_array($article->getStatus(), $allowedStatuses)) {
            return false;
        }

        if ($this->security->isGranted('ROLE_CHIEF_EDITOR')) {
            return true;
        }

        return $user === $article->getEditor();
    }
}
