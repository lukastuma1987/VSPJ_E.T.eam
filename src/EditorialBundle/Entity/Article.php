<?php

namespace EditorialBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use EditorialBundle\Enum\ArticleStatus;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Article
 *
 * @ORM\Table(name="articles")
 * @ORM\Entity(repositoryClass="EditorialBundle\Repository\ArticleRepository")
 */
class Article
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
     * @ORM\Column(name="name", type="string", length=255)
     * @Assert\NotBlank(message="Zadejte název článku")
     * @Assert\Length(max="255", maxMessage="Maximální délka názvu je {{ limit }} znaků")
     */
    private $name;

    /**
     * @var int
     *
     * @ORM\Column(name="status", type="integer")
     */
    private $status;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

    /**
     * @var ArrayCollection|ArticleVersion[]
     *
     * @ORM\OneToMany(targetEntity="ArticleVersion", mappedBy="article", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $versions;

    /**
     * @var ArrayCollection|ArticleAuthor[]
     *
     * @ORM\OneToMany(targetEntity="ArticleAuthor", mappedBy="article", cascade={"persist", "remove"}, orphanRemoval=true)
     * @Assert\Valid()
     */
    private $authors;

    /**
     * @var Magazine
     *
     * @ORM\ManyToOne(targetEntity="Magazine", inversedBy="articles")
     * @ORM\JoinColumn(name="magazine_id", referencedColumnName="id")
     */
    private $magazine;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="authorArticles")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $owner;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="editorArticles")
     * @ORM\JoinColumn(name="editor_id", referencedColumnName="id", nullable=true)
     */
    private $editor;

    /**
     * @var ArrayCollection|Review[]
     *
     * @ORM\OneToMany(targetEntity="Review", mappedBy="article", cascade={"persist"})
     */
    private $reviews;

    public function __construct()
    {
        $this->versions = new ArrayCollection();
        $this->authors = new ArrayCollection();
        $this->reviews = new ArrayCollection();
        $this->created = new \DateTime();
        $this->status = ArticleStatus::STATUS_NEW;
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
     * Set name.
     *
     * @param string $name
     *
     * @return Article
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set status.
     *
     * @param int $status
     *
     * @return Article
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status.
     *
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param ArticleVersion $version
     * @return Article
     */
    public function addVersion(ArticleVersion $version)
    {
        if (!$this->versions->contains($version)) {
            $this->versions[] = $version;
            $version->setArticle($this);
        }

        return $this;
    }

    /**
     * @param ArticleVersion $version
     * @return Article
     */
    public function removeVersion(ArticleVersion $version)
    {
        if ($this->versions->contains($version)) {
            $this->versions->removeElement($version);

            if ($version->getArticle() === $this) {
                $version->setArticle(null);
            }
        }

        return $this;
    }

    /**
     * @return ArrayCollection|ArticleVersion[]
     */
    public function getVersions()
    {
        return $this->versions;
    }

    /**
     * @param ArticleAuthor $author
     * @return Article
     */
    public function addAuthor(ArticleAuthor $author)
    {
        if (!$this->authors->contains($author)) {
            $this->authors[] = $author;
            $author->setArticle($this);
        }

        return $this;
    }

    /**
     * @param ArticleAuthor $author
     * @return Article
     */
    public function removeAuthor(ArticleAuthor $author)
    {
        if ($this->authors->contains($author)) {
            $this->authors->removeElement($author);

            if ($author->getArticle() === $this) {
                $author->setArticle(null);
            }
        }

        return $this;
    }

    /**
     * @return ArrayCollection|ArticleAuthor[]
     */
    public function getAuthors()
    {
        return $this->authors;
    }

    /**
     * @param Magazine $magazine
     * @return Article
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

    /**
     * @param User $owner
     * @return Article
     */
    public function setOwner(User $owner = null)
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * @return User
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * @param User $editor
     * @return Article
     */
    public function setEditor(User $editor = null)
    {
        $this->editor = $editor;

        return $this;
    }

    /**
     * @return User
     */
    public function getEditor()
    {
        return $this->editor;
    }

    /**
     * @param Review $review
     * @return Article
     */
    public function addReview(Review $review)
    {
        if (!$this->reviews->contains($review)) {
            $this->reviews[] = $review;
            $review->setArticle($this);
        }

        return $this;
    }

    /**
     * @param Review $review
     * @return Article
     */
    public function removeReview(Review $review)
    {
        if ($this->reviews->contains($review)) {
            $this->reviews->removeElement($review);

            if ($review->getArticle() === $this) {
                $review->setArticle(null);
            }
        }

        return $this;
    }

    /**
     * @return ArrayCollection|Review[]
     */
    public function getReviews()
    {
        return $this->reviews;
    }

    /**
     * @return string
     */
    public function getMagazineChoiceName()
    {
        if ($this->magazine) {
            return $this->magazine->getChoiceName();
        }

        return '';
    }

    /**
     * @return ArticleVersion|null
     */
    public function getLastVersion()
    {
        return $this->versions->isEmpty() ? null : $this->versions->last();
    }

    /**
     * @return string
     */
    public function getOwnerInfo()
    {
        if ($owner = $this->getOwner()) {
            return sprintf('%s (%s)', $owner->getUsername(), $owner->getEmail());
        }

        return '';
    }

    /**
     * @return string
     */
    public function getEditorInfo()
    {
        if ($editor = $this->getEditor()) {
            return sprintf('%s (%s)', $editor->getUsername(), $editor->getEmail());
        }

        return '';
    }

    /**
     * @return string
     */
    public function getEditorEmail()
    {
        if ($editor = $this->getEditor()) {
            return $editor->getEmail();
        }

        return '';
    }

    /**
     * @return string
     */
    public function getOwnerEmail()
    {
        if ($owner = $this->getOwner()) {
            return $owner->getEmail();
        }

        return '';
    }

    /**
     * @return int|string
     */
    public function getMagazineYear()
    {
        if ($magazine = $this->getMagazine()) {
            return $magazine->getYear();
        }

        return '';
    }

    /**
     * @return int|string
     */
    public function getMagazineNumber()
    {
        if ($magazine = $this->getMagazine()) {
            return $magazine->getNumber();
        }

        return '';
    }

    /**
     * @return bool
     */
    public function hasAllReviewsFilled()
    {
        foreach ($this->getReviews() as $review) {
            if (!$review->isFilled()) {
                return false;
            }
        }

        return true;
    }
}
