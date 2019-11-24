<?php

namespace EditorialBundle\Security;

use EditorialBundle\Entity\ArticleVersion;
use EditorialBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class ArticleVersionVoter extends Voter
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    const DOWNLOAD = 'DOWNLOAD';

    protected function supports($attribute, $subject)
    {
        if (!in_array($attribute, [self::DOWNLOAD])) {
            return false;
        }

        if (!$subject instanceof ArticleVersion) {
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
                return $this->canDownload($subject, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    // private

    private function canDownload(ArticleVersion $version, User $user)
    {
        if ($this->security->isGranted('ROLE_EDITOR')) {
            return true;
        }

        $article = $version->getArticle();
        if (!$article) {
            return false;
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
