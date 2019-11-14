<?php

namespace EditorialBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

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
     * @var int
     *
     * @ORM\Column(name="benefit_level", type="integer", nullable=true)
     * @Assert\NotBlank(message="Zadejte prosím úroveň aktuálnosti, zajímavosti a přínosnosti")
     * @Assert\Range(
     *     min="1",
     *     max="5",
     *     minMessage="Minimální povolená hodnota je {{ limit }}",
     *     maxMessage="Maximální povolená hodnota je {{ limit }}"
     * )
     */
    private $benefitLevel;

    /**
     * @var int
     *
     * @ORM\Column(name="originality_level", type="integer", nullable=true)
     * @Assert\NotBlank(message="Zadejte prosím úroveň originality")
     * @Assert\Range(
     *     min="1",
     *     max="5",
     *     minMessage="Minimální povolená hodnota je {{ limit }}",
     *     maxMessage="Maximální povolená hodnota je {{ limit }}"
     * )
     */
    private $originalityLevel;

    /**
     * @var int
     *
     * @ORM\Column(name="professional_level", type="integer", nullable=true)
     * @Assert\NotBlank(message="Zadejte prosím úroveň odbornosti")
     * @Assert\Range(
     *     min="1",
     *     max="5",
     *     minMessage="Minimální povolená hodnota je {{ limit }}",
     *     maxMessage="Maximální povolená hodnota je {{ limit }}"
     * )
     */
    private $professionalLevel;

    /**
     * @var int
     *
     * @ORM\Column(name="language_level", type="integer", nullable=true)
     * @Assert\NotBlank(message="Zadejte prosím jazykovou a stylistickou úroveň")
     * @Assert\Range(
     *     min="1",
     *     max="5",
     *     minMessage="Minimální povolená hodnota je {{ limit }}",
     *     maxMessage="Maximální povolená hodnota je {{ limit }}"
     * )
     */
    private $languageLevel;


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
     * @param int $benefitLevel
     * @return Review
     */
    public function setBenefitLevel($benefitLevel)
    {
        $this->benefitLevel = $benefitLevel;

        return $this;
    }

    /**
     * @return int
     */
    public function getBenefitLevel()
    {
        return $this->benefitLevel;
    }

    /**
     * @param int $originalityLevel
     * @return Review
     */
    public function setOriginalityLevel($originalityLevel)
    {
        $this->originalityLevel = $originalityLevel;

        return $this;
    }

    /**
     * @return int
     */
    public function getOriginalityLevel()
    {
        return $this->originalityLevel;
    }

    /**
     * @param int $professionalLevel
     * @return Review
     */
    public function setProfessionalLevel($professionalLevel)
    {
        $this->professionalLevel = $professionalLevel;

        return $this;
    }

    /**
     * @return int
     */
    public function getProfessionalLevel()
    {
        return $this->professionalLevel;
    }

    /**
     * @param int $languageLevel
     * @return Review
     */
    public function setLanguageLevel($languageLevel)
    {
        $this->languageLevel = $languageLevel;

        return $this;
    }

    /**
     * @return int
     */
    public function getLanguageLevel()
    {
        return $this->languageLevel;
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

    /**
     * @return bool
     */
    public function isFilled()
    {
        return $this->review ? true : false;
    }

    /**
     * @return string
     */
    public function getArticleOwnerInfo()
    {
        if ($article = $this->getArticle()) {
            return $article->getOwnerInfo();
        }

        return '';
    }
}
