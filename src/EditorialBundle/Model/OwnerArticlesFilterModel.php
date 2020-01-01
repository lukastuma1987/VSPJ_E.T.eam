<?php

namespace EditorialBundle\Model;

use EditorialBundle\Entity\Magazine;

class OwnerArticlesFilterModel
{
    /** @var string|null */
    private $name;
    /** @var int|null */
    private $status;
    /** @var Magazine|null */
    private $magazine;
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
     * @return OwnerArticlesFilterModel
     */
    public function setName(?string $name): OwnerArticlesFilterModel
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
     * @return OwnerArticlesFilterModel
     */
    public function setStatus(?int $status): OwnerArticlesFilterModel
    {
        $this->status = $status;

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
     * @return OwnerArticlesFilterModel
     */
    public function setMagazine(?Magazine $magazine): OwnerArticlesFilterModel
    {
        $this->magazine = $magazine;

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
     * @return OwnerArticlesFilterModel
     */
    public function setCreatedFrom(?\DateTime $createdFrom): OwnerArticlesFilterModel
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
     * @return OwnerArticlesFilterModel
     */
    public function setCreatedTill(?\DateTime $createdTill): OwnerArticlesFilterModel
    {
        $this->createdTill = $createdTill;

        return $this;
    }
}
