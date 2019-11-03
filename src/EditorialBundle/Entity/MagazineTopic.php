<?php

namespace EditorialBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * MagazineTopic
 *
 * @ORM\Table(name="magazine_topics")
 * @ORM\Entity(repositoryClass="EditorialBundle\Repository\MagazineTopicRepository")
 */
class MagazineTopic
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="topic", type="string", length=255)
     * @Assert\NotBlank(message="Zadejte téma")
     * @Assert\Length(max="255", maxMessage="Téma může mít maximálně {{ limit }} znaků")
     */
    private $topic;

    /**
     * @var Magazine
     *
     * @ORM\ManyToOne(targetEntity="Magazine", inversedBy="topics")
     * @ORM\JoinColumn(name="magazine_id", referencedColumnName="id")
     */
    private $magazine;

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set topic.
     *
     * @param string $topic
     *
     * @return MagazineTopic
     */
    public function setTopic($topic)
    {
        $this->topic = $topic;

        return $this;
    }

    /**
     * Get topic.
     *
     * @return string
     */
    public function getTopic()
    {
        return $this->topic;
    }

    /**
     * @param Magazine $magazine
     * @return MagazineTopic
     */
    public function setMagazine(Magazine $magazine = null)
    {
        $this->magazine = $magazine;

        return $this;
    }

    /**
     * @return Magazine
     */
    public function getMagazine()
    {
        return $this->magazine;
    }
}
