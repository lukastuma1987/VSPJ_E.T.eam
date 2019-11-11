<?php

namespace EditorialBundle\Security;

use EditorialBundle\Entity\Review;
use EditorialBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class ReviewVoter extends Voter
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    const ADD = 'ADD';

    protected function supports($attribute, $subject)
    {
        if (!in_array($attribute, [self::ADD])) {
            return false;
        }

        if (!$subject instanceof Review) {
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
            case self::ADD:
                return $this->canAdd($subject, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    // private
    private function canAdd(Review $review, User $user)
    {
        return $user === $review->getReviewer();
    }
}
