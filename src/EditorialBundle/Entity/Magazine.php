<?php

namespace EditorialBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

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
     * @Assert\NotBlank(message="Zadejte datum vydání")
     */
    private $publishDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="deadlineDate", type="datetime")
     * @Assert\NotBlank(message="Zadejte datum uzávěrky")
     */
    private $deadlineDate;

    /**
     * @var int
     *
     * @ORM\Column(name="year", type="integer")
     * @Assert\NotBlank(message="Zadejte ročník časopisu")
     * @Assert\Range(min="1", minMessage="Minimální hodnota je {{ limit }}")
     */
    private $year;

    /**
     * @var int
     *
     * @ORM\Column(name="number", type="integer")
     * @Assert\NotBlank(message="Zadejte číslo časopisu")
     * @Assert\Range(
     *     min="1",
     *     max="4",
     *     minMessage="Minimální hodnota je {{ limit }}",
     *     maxMessage="Maximální hodnota je {{ limit }}"
     * )
     */
    private $number;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

    /**
     * @var string
     *
     * @ORM\Column(name="suffix", type="string", length=20, nullable=true)
     */
    private $suffix;

    /**
     * @var ArrayCollection|MagazineTopic[]
     *
     * @ORM\OneToMany(targetEntity="MagazineTopic", mappedBy="magazine", cascade={"persist", "remove"}, orphanRemoval=true)
     * @Assert\Valid()
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
        $this->publishDate = new \DateTime();
        $this->deadlineDate = new \DateTime();
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
        if (!$this->topics->contains($topic)) {
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
        if ($this->topics->contains($topic)) {
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

    /**
     * @return string
     */
    public function getTopicsString()
    {
        $topics = [];

        foreach ($this->topics as $topic) {
            $topics[] = $topic->getTopic();
        }

        return implode(', ', $topics);
    }

    /**
     * @return string
     */
    public function getChoiceName()
    {
        return sprintf("Ročník %d, číslo %d", $this->year, $this->number);
    }

    /**
     * @param string $suffix
     * @return Magazine
     */
    public function setSuffix($suffix)
    {
        $this->suffix = $suffix;

        return $this;
    }

    /**
     * @return string
     */
    public function getSuffix()
    {
        return $this->suffix;
    }
}
