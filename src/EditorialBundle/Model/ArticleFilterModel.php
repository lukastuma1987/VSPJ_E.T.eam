<?php

namespace EditorialBundle\Model;

use EditorialBundle\Entity\Magazine;
use EditorialBundle\Entity\User;

class ArticleFilterModel
{
    /** @var string|null */
    private $name;
    /** @var User|null */
    private $owner;
    /** @var User|null */
    private $editor;
    /** @var Magazine|null */
    private $magazine;
    /** @var int|null */
    private $status;
    /** @var \DateTime|null */
    private $createdFrom;
    /** @var \DateTime|null */
    private $createdTill;

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     * @return ArticleFilterModel
     */
    public function setName(?string $name): ArticleFilterModel
    {
        $this->name = $name;
        
        return $this;
    }

    /**
     * @return User|null
     */
    public function getOwner(): ?User
    {
        return $this->owner;
    }

    /**
     * @param User|null $owner
     * @return ArticleFilterModel
     */
    public function setOwner(?User $owner): ArticleFilterModel
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * @return User|null
     */
    public function getEditor(): ?User
    {
        return $this->editor;
    }

    /**
     * @param User|null $editor
     * @return ArticleFilterModel
     */
    public function setEditor(?User $editor): ArticleFilterModel
    {
        $this->editor = $editor;

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
     * @return ArticleFilterModel
     */
    public function setMagazine(?Magazine $magazine): ArticleFilterModel
    {
        $this->magazine = $magazine;

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
     * @return ArticleFilterModel
     */
    public function setStatus(?int $status): ArticleFilterModel
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getCreatedFrom(): ?\DateTime
    {
        return $this->createdFrom;
    }

    /**
     * @param \DateTime|null $createdFrom
     * @return ArticleFilterModel
     */
    public function setCreatedFrom(?\DateTime $createdFrom): ArticleFilterModel
    {
        $this->createdFrom = $createdFrom;

        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getCreatedTill(): ?\DateTime
    {
        return $this->createdTill;
    }

    /**
     * @param \DateTime|null $createdTill
     * @return ArticleFilterModel
     */
    public function setCreatedTill(?\DateTime $createdTill): ArticleFilterModel
    {
        $this->createdTill = $createdTill;

        return $this;
    }
}
