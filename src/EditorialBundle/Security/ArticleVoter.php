<?php

namespace EditorialBundle\Security;

use EditorialBundle\Entity\Article;
use EditorialBundle\Entity\User;
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

    const ASSIGN_REVIEWER = 'ASSIGN_REVIEWER';
    const DOWNLOAD = 'DOWNLOAD';

    protected function supports($attribute, $subject)
    {
        if (!in_array($attribute, [self::ASSIGN_REVIEWER, self::DOWNLOAD])) {
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
            case self::ASSIGN_REVIEWER:
                return $this->canAssignReviewer($subject, $user);
            case self::DOWNLOAD:
                return $this->canDownload($subject, $user);
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

    private function canDownload(Article $article, User $user)
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
}
