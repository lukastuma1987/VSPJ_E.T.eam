<?php

namespace EditorialBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Magazine
 *
 * @ORM\Table(name="magazines")
 * @ORM\Entity(repositoryClass="EditorialBundle\Repository\MagazineRepository")
 */
class Magazine
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
     * @var \DateTime
     *
     * @ORM\Column(name="publishDate", type="datetime")
     */
    private $publishDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="deadlineDate", type="datetime")
     */
    private $deadlineDate;

    /**
     * @var int
     *
     * @ORM\Column(name="year", type="integer")
     */
    private $year;

    /**
     * @var int
     *
     * @ORM\Column(name="number", type="integer")
     */
    private $number;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

    /**
     * @var ArrayCollection|MagazineTopic[]
     *
     * @ORM\OneToMany(targetEntity="MagazineTopic", mappedBy="magazine", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $topics;

    /**
     * @var ArrayCollection|Article[]
     *
     * @ORM\OneToMany(targetEntity="Article", mappedBy="magazine", cascade={"persist"})
     */
    private $articles;


    public function __construct()
    {
        $this->topics = new ArrayCollection();
        $this->articles = new ArrayCollection();
        $this->created = new \DateTime();
    }

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
     * Set publishDate.
     *
     * @param \DateTime $publishDate
     *
     * @return Magazine
     */
    public function setPublishDate(\DateTime $publishDate)
    {
        $this->publishDate = $publishDate;

        return $this;
    }

    /**
     * Get publishDate.
     *
     * @return \DateTime
     */
    public function getPublishDate()
    {
        return $this->publishDate;
    }

    /**
     * Set deadlineDate.
     *
     * @param \DateTime $deadlineDate
     *
     * @return Magazine
     */
    public function setDeadlineDate(\DateTime $deadlineDate)
    {
        $this->deadlineDate = $deadlineDate;

        return $this;
    }

    /**
     * Get deadlineDate.
     *
     * @return \DateTime
     */
    public function getDeadlineDate()
    {
        return $this->deadlineDate;
    }

    /**
     * Set year.
     *
     * @param int $year
     *
     * @return Magazine
     */
    public function setYear($year)
    {
        $this->year = $year;

        return $this;
    }

    /**
     * Get year.
     *
     * @return int
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * Set number.
     *
     * @param int $number
     *
     * @return Magazine
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * Get number.
     *
     * @return int
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Add magazine topic
     *
     * @param MagazineTopic $topic
     * @return Magazine
     */
    public function addTopic(MagazineTopic $topic)
    {
        if (!$this->topics-contains($topic)) {
            $this->topics[] = $topic;
            $topic->setMagazine($this);
        }

        return $this;
    }

    /**
     * Remove magazine topic
     *
     * @param MagazineTopic $topic
     * @return Magazine
     */
    public function removeTopic(MagazineTopic $topic)
    {
        if ($this->topics-contains($topic)) {
            $this->topics->removeElement($topic);

            if ($topic->setMagazine() === $this) {
                $topic->setMagazine(null);
            }
        }

        return $this;
    }

    /**
     * Get magazine topics
     *
     * @return ArrayCollection|MagazineTopic[]
     */
    public function getTopics()
    {
        return $this->topics;
    }

    /**
     * @param Article $article
     * @return Magazine
     */
    public function addArticle(Article $article)
    {
        if (!$this->articles->contains($article)) {
            $this->articles[] = $article;
            $article->setMagazine($this);
        }

        return $this;
    }

    /**
     * @param Article $article
     * @return Magazine
     */
    public function removeArticle(Article $article)
    {
        if ($this->articles->contains($article)) {
            $this->articles->removeElement($article);

            if ($article->getMagazine() === $this) {
                $article->setMagazine(null);
            }
        }

        return $this;
    }

    /**
     * @return ArrayCollection|Article[]
     */
    public function getArticles()
    {
        return $this->articles;
    }
}
