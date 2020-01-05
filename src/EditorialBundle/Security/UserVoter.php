<?php

namespace EditorialBundle\Security;

use EditorialBundle\Entity\ArticleVersion;
use EditorialBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class UserVoter extends Voter
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    const REMOVE = 'REMOVE';

    protected function supports($attribute, $subject)
    {
        if (!in_array($attribute, [self::REMOVE])) {
            return false;
        }

        if (!$subject instanceof User) {
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
            case self::REMOVE:
                return $this->canRemove($subject, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    // private

    private function canRemove(User $user, User $currentUser)
    {
        if (!$this->security->isGranted('ROLE_ADMIN')) {
            return false;
        }

        return $user !== $currentUser;
    }
}
