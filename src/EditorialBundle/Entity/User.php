<?php

namespace EditorialBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * User
 *
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="EditorialBundle\Repository\UserRepository")
 * @UniqueEntity("username", message="Uživatelské jméno {{ value }} již existuje")
 * @UniqueEntity("email", message="Uživatel s emailem {{ value }} již existuje")
 */
class User implements UserInterface, \Serializable
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
     * @ORM\Column(name="username", type="string", length=50, unique=true)
     * @Assert\NotBlank(message="Zadejte uživatelské jméno")
     * @Assert\Length(max="50", maxMessage="Uživatelské jméno může mít maximálně {{ limit }} znaků.")
     * @Assert\Regex("/[A-Za-z0-9_-]+/", message="Povoleny jsou pouze znaky A-Z, a-z, 0-9, '_' a '-'.")
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=100, unique=true)
     * @Assert\NotBlank(message="Zadejte email")
     * @Assert\Email(message="{{ value }} není platný email")
     * @Assert\Length(max="100", maxMessage="Email může mít maximálně {{ limit }} znaků.")
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=100)
     */
    private $password;

    /**
     * @var string
     *
     * @Assert\NotBlank(message="Zadejte heslo", groups={"Create"})
     * @Assert\Length(min="8", minMessage="Heslo musí mít alespoň {{ limit }} znaků.")
     */
    private $plaintextPassword;

    /**
     * @var ArrayCollection|Role[]
     *
     * @ORM\ManyToMany(targetEntity="Role", inversedBy="users")
     * @ORM\JoinTable(name="users_roles")
     */
    private $roles;

    /**
     * @var ArrayCollection|Article[]
     *
     * @ORM\OneToMany(targetEntity="Article", mappedBy="owner", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $authorArticles;

    /**
     * @var ArrayCollection|Article[]
     *
     * @ORM\OneToMany(targetEntity="Article", mappedBy="editor", cascade={"persist"})
     */
    private $editorArticles;

    /**
     * @var ArrayCollection|Review[]
     *
     * @ORM\OneToMany(targetEntity="Review", mappedBy="reviewer", cascade={"persist"})
     */
    private $reviews;

    /**
     * @var ArrayCollection|ArticleComment[]
     *
     * @ORM\OneToMany(targetEntity="ArticleComment", mappedBy="user", cascade={"persist"})
     */
    private $comments;

    /**
     * @var ArrayCollection|HelpDeskMessage[]
     *
     * @ORM\OneToMany(targetEntity="HelpDeskMessage", mappedBy="manager", cascade={"persist"})
     */
    private $helpDeskMessages;

    public function __construct()
    {
        $this->roles = new ArrayCollection();
        $this->authorArticles = new ArrayCollection();
        $this->editorArticles = new ArrayCollection();
        $this->reviews = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->helpDeskMessages = new ArrayCollection();
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
     * Set username.
     *
     * @param string $username
     *
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username.
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set email.
     *
     * @param string $email
     *
     * @return User
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
     * Set password.
     *
     * @param string $password
     *
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password.
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Add Role
     *
     * @param Role $role
     * @return User
     */
    public function addRole(Role $role)
    {
        if (!$this->roles->contains($role)) {
            $this->roles[] = $role;
            $role->addUser($this);
        }

        return $this;
    }

    /**
     * Remove Role
     *
     * @param Role $role
     * @return User
     */
    public function removeRole(Role $role)
    {
        if ($this->roles->contains($role)) {
            $this->roles->removeElement($role);
            $role->removeUser($this);
        }

        return $this;
    }

    /**
     * Get Roles
     *
     * {@inheritdoc}
     *
     * @return Role[]
     */
    public function getRoles()
    {
        return $this->roles->toArray();
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function serialize()
    {
        return serialize([
            $this->id,
            $this->username,
            $this->password,
        ]);
    }

    /**
     * {@inheritdoc}
     *
     * @param string $serialized
     */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->username,
            $this->password,
        ) = unserialize($serialized, ['allowed_classes' => false]);
    }

    /**
     * {@inheritdoc}
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function eraseCredentials()
    {
    }

    /**
     * @return string
     */
    public function getDisplayRole()
    {
        $roles = [];

        foreach ($this->roles as $role) {
            $roles[] = $role->getName();
        }

        return implode(', ', $roles);
    }

    /**
     * @param string $plaintextPassword
     *
     * @return User
     */
    public function setPlaintextPassword($plaintextPassword)
    {
        $this->plaintextPassword = $plaintextPassword;

        return $this;
    }

    /**
     * @return string
     */
    public function getPlaintextPassword()
    {
        return $this->plaintextPassword;
    }

    /**
     * @param Article $article
     * @return User
     */
    public function addAuthorArticle(Article $article)
    {
        if (!$this->authorArticles->contains($article)) {
            $this->authorArticles[] = $article;
            $article->setOwner($this);
        }

        return $this;
    }

    /**
     * @param Article $article
     * @return User
     */
    public function removeAuthorArticle(Article $article)
    {
        if ($this->authorArticles->contains($article)) {
            $this->authorArticles->removeElement($article);

            if ($article->getOwner() === $this) {
                $article->setOwner(null);
            }
        }

        return $this;
    }

    /**
     * @return ArrayCollection|Article[]
     */
    public function getAuthorArticles()
    {
        return $this->authorArticles;
    }

    /**
     * @param Article $article
     * @return User
     */
    public function addEditorArticle(Article $article)
    {
        if (!$this->editorArticles->contains($article)) {
            $this->editorArticles[] = $article;
            $article->setEditor($this);
        }

        return $this;
    }

    /**
     * @param Article $article
     * @return User
     */
    public function removeEditorArticle(Article $article)
    {
        if ($this->editorArticles->contains($article)) {
            $this->editorArticles->removeElement($article);

            if ($article->getEditor() === $this) {
                $article->setEditor(null);
            }
        }

        return $this;
    }

    /**
     * @return ArrayCollection|Article[]
     */
    public function getEditorArticles()
    {
        return $this->editorArticles;
    }

    /**
     * @param Review $review
     * @return User
     */
    public function addReview(Review $review)
    {
        if (!$this->reviews->contains($review)) {
            $this->reviews[] = $review;
            $review->setReviewer($this);
        }

        return $this;
    }

    /**
     * @param Review $review
     * @return User
     */
    public function removeReview(Review $review)
    {
        if ($this->reviews->contains($review)) {
            $this->reviews->removeElement($review);

            if ($review->getReviewer() === $this) {
                $review->setReviewer($this);
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
     * @param ArticleComment $comment
     * @return User
     */
    public function addComment(ArticleComment $comment)
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setUser($this);
        }

        return $this;
    }

    /**
     * @param ArticleComment $comment
     * @return User
     */
    public function removeComment(ArticleComment $comment)
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);

            if ($comment->getUser() === $this) {
                $comment->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return ArrayCollection|ArticleComment[]
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * @param HelpDeskMessage $message
     * @return User
     */
    public function addHelpDeskMessage(HelpDeskMessage $message)
    {
        if (!$this->helpDeskMessages->contains($message)) {
            $this->helpDeskMessages[] = $message;
            $message->setManager($this);
        }

        return $this;
    }

    /**
     * @param HelpDeskMessage $message
     * @return User
     */
    public function removeHelpDeskMessage(HelpDeskMessage $message)
    {
        if ($this->helpDeskMessages->contains($message)) {
            $this->helpDeskMessages->removeElement($message);

            if ($message->getManager() === $this) {
                $message->setManager(null);
            }
        }

        return $this;
    }

    /**
     * @return ArrayCollection|HelpDeskMessage[]
     */
    public function getHelpDeskMessages()
    {
        return $this->helpDeskMessages;
    }

    /**
     * @return string
     */
    public function getDisplayName()
    {
        return sprintf('%s (%s)', $this->username, $this->email);
    }
}
