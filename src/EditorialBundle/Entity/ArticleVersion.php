<?php

namespace EditorialBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ArticleVersion
 *
 * @ORM\Table(name="article_versions")
 * @ORM\Entity(repositoryClass="EditorialBundle\Repository\ArticleVersionRepository")
 */
class ArticleVersion
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
     * @ORM\Column(name="file", type="blob")
     */
    private $file;

    /**
     * @var string
     *
     * @ORM\Column(name="suffix", type="string", length=20)
     */
    private $suffix;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

    /**
     * @var Article
     *
     * @ORM\ManyToOne(targetEntity="Article", inversedBy="versions")
     * @ORM\JoinColumn(name="article_id", referencedColumnName="id")
     */
    private $article;

    public function __construct()
    {
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
     * Set file.
     *
     * @param string $file
     *
     * @return ArticleVersion
     */
    public function setFile($file)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * Get file.
     *
     * @return string
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param Article $article
     * @return ArticleVersion
     */
    public function setArticle(Article $article = null)
    {
        $this->article = $article;

        return $this;
    }

    /**
     * @return Article
     */
    public function getArticle()
    {
        return $this->article;
    }

    /**
     * @param string $suffix
     * @return ArticleVersion
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
