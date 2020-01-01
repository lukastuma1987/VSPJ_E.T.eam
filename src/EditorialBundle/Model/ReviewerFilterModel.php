<?php

namespace EditorialBundle\Model;


use EditorialBundle\Entity\User;

class ReviewerFilterModel
{
    /** @var string|null */
    private $name;
    /** @var User|null */
    private $author;
    /** @var int|null */
    private $status;

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     * @return ReviewerFilterModel
     */
    public function setName(?string $name): ReviewerFilterModel
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getStatus(): ?int
    {
        return $this->status;
    }

    /**
     * @param int|null $status
     * @return ReviewerFilterModel
     */
    public function setStatus(?int $status): ReviewerFilterModel
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return User|null
     */
    public function getAuthor(): ?User
    {
        return $this->author;
    }

    /**
     * @param User|null $author
     * @return ReviewerFilterModel
     */
    public function setAuthor(?User $author): ReviewerFilterModel
    {
        $this->author = $author;

        return $this;
    }
}
