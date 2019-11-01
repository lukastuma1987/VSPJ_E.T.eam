<?php

namespace EditorialBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * ArticleAuthor
 *
 * @ORM\Table(name="article_authors")
 * @ORM\Entity(repositoryClass="EditorialBundle\Repository\ArticleAuthorRepository")
 */
class ArticleAuthor
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
     * @ORM\Column(name="fullName", type="string", length=255)
     * @Assert\NotBlank(message="Zadejte celé jméno")
     * @Assert\Length(max="255", maxMessage="Maximální délka jména je {{ limit }} znaků")
     */
    private $fullName;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=100)
     * @Assert\NotBlank(message="Zadejte e-mail")
     * @Assert\Length(max="100", maxMessage="Maximální délka emailu je {{ limit }} znaků")
     * @Assert\Email(message="Zadaná e-mailová adresa má neplatný tvar")
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="workplace", type="string", length=255)
     * @Assert\NotBlank(message="Zadejte pracoviště")
     * @Assert\Length(max="255", maxMessage="Maximální délka pracoviště je {{ limit }} znaků")
     */
    private $workplace;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="string", length=255)
     * @Assert\NotBlank(message="Zadejte kontaktní adresu")
     * @Assert\Length(max="255", maxMessage="Maximální délka adresy je {{ limit }} znaků")
     */
    private $address;

    /**
     * @var Article
     *
     * @ORM\ManyToOne(targetEntity="Article", inversedBy="authors")
     * @ORM\JoinColumn(name="article_id", referencedColumnName="id")
     */
    private $article;

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
     * Set fullName.
     *
     * @param string $fullName
     *
     * @return ArticleAuthor
     */
    public function setFullName($fullName)
    {
        $this->fullName = $fullName;

        return $this;
    }

    /**
     * Get fullName.
     *
     * @return string
     */
    public function getFullName()
    {
        return $this->fullName;
    }

    /**
     * Set email.
     *
     * @param string $email
     *
     * @return ArticleAuthor
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email.
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set workplace.
     *
     * @param string $workplace
     *
     * @return ArticleAuthor
     */
    public function setWorkplace($workplace)
    {
        $this->workplace = $workplace;

        return $this;
    }

    /**
     * Get workplace.
     *
     * @return string
     */
    public function getWorkplace()
    {
        return $this->workplace;
    }

    /**
     * Set address.
     *
     * @param string $address
     *
     * @return ArticleAuthor
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address.
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param Article $article
     * @return ArticleAuthor
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
}
