<?php

namespace EditorialBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use EditorialBundle\Entity\Article;
use EditorialBundle\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;

class AssignReviewersModel
{
    /**
     * @var ArrayCollection|User[]
     *
     * @Assert\Count(
     *     min="2",
     *     max="2",
     *     minMessage="Vyberte alespoň {{ limit }} recenzenty",
     *     maxMessage="Vyberte maximálně {{ limit }} recenzenty",
     *     exactMessage="Vyberte přesně {{ limit }} recenzenty",
     *     groups={"New"}
     * )
     * @Assert\Count(
     *     min="1",
     *     max="2",
     *     minMessage="Vyberte alespoň jednoho recenzenta",
     *     maxMessage="Vyberte maximálně {{ limit }} recenzenty",
     *     groups={"Existing"}
     * )
     */
    private $reviewers;

    /**
     * @var \DateTime
     *
     * @Assert\NotBlank(message="Zadejte deadline")
     */
    private $deadline;

    /** @var User */
    private $owner;

    /** @var User */
    private $editor;

    public function __construct(Article $article)
    {
        $this->reviewers = new ArrayCollection();
        $this->owner = $article->getOwner();
        $this->editor = $article->getEditor();
        $this->deadline = new \DateTime();
    }

    /**
     * @param User $user
     * @return $this
     */
    public function addReviewer(User $user)
    {
        if (!$this->reviewers->contains($user)) {
            $this->reviewers[] = $user;
        }

        return $this;
    }

    /**
     * @param User $user
     * @return $this
     */
    public function removeReviewer(User $user)
    {
        if ($this->reviewers->contains($user)) {
            $this->reviewers->removeElement($user);
        }

        return $this;
    }

    /**
     * @return ArrayCollection|User[]
     */
    public function getReviewers()
    {
        return $this->reviewers;
    }

    /**
     * @return User
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * @return User
     */
    public function getEditor()
    {
        return $this->editor;
    }

    /**
     * @param \DateTime $deadline
     * @return AssignReviewersModel
     */
    public function setDeadline(\DateTime $deadline)
    {
        $this->deadline = $deadline;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDeadline()
    {
        return $this->deadline;
    }
}
