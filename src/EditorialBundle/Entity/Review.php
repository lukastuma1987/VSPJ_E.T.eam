<?php

namespace EditorialBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Review
 *
 * @ORM\Table(name="reviews")
 * @ORM\Entity(repositoryClass="EditorialBundle\Repository\ReviewRepository")
 */
class Review
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
     * @var string|null
     *
     * @ORM\Column(name="review", type="text", nullable=true)
     */
    private $review;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="filled", type="datetime", nullable=true)
     */
    private $filled;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="deadline", type="datetime")
     */
    private $deadline;

    /**
     * @var Article
     *
     * @ORM\ManyToOne(targetEntity="Article", inversedBy="reviews")
     * @ORM\JoinColumn(name="article_id", referencedColumnName="id",)
     */
    private $article;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="reviews")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $reviewer;

    public function __construct()
    {
        $this->created = new \DateTime();
        $this->article = new \DateTime();
        $this->reviewer = new \DateTime();
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
     * Set review.
     *
     * @param string|null $review
     *
     * @return Review
     */
    public function setReview($review = null)
    {
        $this->review = $review;

        return $this;
    }

    /**
     * Get review.
     *
     * @return string|null
     */
    public function getReview()
    {
        return $this->review;
    }

    /**
     * Get created.
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set filled.
     *
     * @param \DateTime|null $filled
     *
     * @return Review
     */
    public function setFilled(\DateTime $filled = null)
    {
        $this->filled = $filled;

        return $this;
    }

    /**
     * Get filled.
     *
     * @return \DateTime|null
     */
    public function getFilled()
    {
        return $this->filled;
    }

    /**
     * Set deadline.
     *
     * @param \DateTime $deadline
     *
     * @return Review
     */
    public function setDeadline(\DateTime $deadline)
    {
        $this->deadline = $deadline;

        return $this;
    }

    /**
     * Get deadline.
     *
     * @return \DateTime
     */
    public function getDeadline()
    {
        return $this->deadline;
    }

    /**
     * Set Article
     *
     * @param Article $article
     * @return Review
     */
    public function setArticle(Article $article = null)
    {
        $this->article = $article;

        return $this;
    }

    /**
     * Get Article
     *
     * @return Article
     */
    public function getArticle()
    {
        return $this->article;
    }

    /**
     * Set Reviewer
     *
     * @param User $reviewer
     * @return Review
     */
    public function setReviewer(User $reviewer = null)
    {
        $this->reviewer = $reviewer;

        return $this;
    }

    /**
     * Get Reviewer
     *
     * @return User
     */
    public function getReviewer()
    {
        return $this->reviewer;
    }

    /**
     * @return string
     */
    public function getReviewerEmail()
    {
        if ($reviewer = $this->getReviewer()) {
            return $reviewer->getEmail();
        }

        return '';
    }

    /**
     * @return string
     */
    public function getArticleName()
    {
        if ($article = $this->getArticle()) {
            return $article->getName();
        }

        return '';
    }

    /**
     * @return int|null
     */
    public function getArticleId()
    {
        if ($article = $this->getArticle()) {
            return $article->getId();
        }

        return null;
    }
}
