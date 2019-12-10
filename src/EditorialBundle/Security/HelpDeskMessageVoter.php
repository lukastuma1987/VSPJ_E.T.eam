<?php

namespace EditorialBundle\Security;

use EditorialBundle\Entity\HelpDeskMessage;
use EditorialBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class HelpDeskMessageVoter extends Voter
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    const ANSWER = 'ANSWER';
    const IGNORE = 'IGNORE';

    protected function supports($attribute, $subject)
    {
        if (!in_array($attribute, [self::ANSWER, self::IGNORE])) {
            return false;
        }

        if (!$subject instanceof HelpDeskMessage) {
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
            case self::ANSWER:
            case self::IGNORE:
                return $this->canAnswerOrIgnore($subject, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    // private

    private function canAnswerOrIgnore(HelpDeskMessage $message, User $user)
    {
        if (!$this->security->isGranted('ROLE_HELP_DESK')) {
            return false;
        }

        return $message->getManager() === null;
    }
}
