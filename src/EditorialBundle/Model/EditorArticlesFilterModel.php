<?php

namespace EditorialBundle\Model;

use EditorialBundle\Entity\Magazine;
use EditorialBundle\Entity\User;

class EditorArticlesFilterModel
{
    /** @var string|null */
    private $name;
    /** @var int|null */
    private $status;
    /** @var User|null */
    private $author;
    /** @var Magazine|null */
    private $magazine;

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     * @return EditorArticlesFilterModel
     */
    public function setName(?string $name): EditorArticlesFilterModel
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
     * @return EditorArticlesFilterModel
     */
    public function setStatus(?int $status): EditorArticlesFilterModel
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
     * @return EditorArticlesFilterModel
     */
    public function setAuthor(?User $author): EditorArticlesFilterModel
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @return Magazine|null
     */
    public function getMagazine(): ?Magazine
    {
        return $this->magazine;
    }

    /**
     * @param Magazine|null $magazine
     * @return EditorArticlesFilterModel
     */
    public function setMagazine(?Magazine $magazine): EditorArticlesFilterModel
    {
        $this->magazine = $magazine;
        
        return $this;
    }
}
