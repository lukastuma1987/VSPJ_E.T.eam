<?php

namespace EditorialBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * HelpDeskMessage
 *
 * @ORM\Table(name="helpdesk_messages")
 * @ORM\Entity(repositoryClass="EditorialBundle\Repository\HelpDeskMessageRepository")
 */
class HelpDeskMessage
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
     * @ORM\Column(name="message", type="text")
     * @Assert\NotBlank(message="Zadejte prosím zprávu")
     */
    private $message;

    /**
     * @var string|null
     *
     * @ORM\Column(name="answer", type="text", nullable=true)
     * @Assert\NotBlank(message="Zadejte odpověď", groups={"Answer"})
     */
    private $answer;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="answered", type="datetime", nullable=true)
     */
    private $answered;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=100)
     * @Assert\NotBlank(message="Zadejte prosím e-mail")
     * @Assert\Email(message="E-mail není ve správném tvaru")
     */
    private $email;

    /**
     * @var User|null
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="helpDeskMessages")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=true)
     */
    private $manager;

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
     * Set message.
     *
     * @param string $message
     *
     * @return HelpDeskMessage
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message.
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set answer.
     *
     * @param string|null $answer
     *
     * @return HelpDeskMessage
     */
    public function setAnswer($answer = null)
    {
        $this->answer = $answer;

        return $this;
    }

    /**
     * Get answer.
     *
     * @return string|null
     */
    public function getAnswer()
    {
        return $this->answer;
    }

    /**
     * Set created.
     *
     * @param \DateTime $created
     *
     * @return HelpDeskMessage
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
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
     * Set answered.
     *
     * @param \DateTime|null $answered
     *
     * @return HelpDeskMessage
     */
    public function setAnswered($answered = null)
    {
        $this->answered = $answered;

        return $this;
    }

    /**
     * Get answered.
     *
     * @return \DateTime|null
     */
    public function getAnswered()
    {
        return $this->answered;
    }

    /**
     * Set email.
     *
     * @param string $email
     *
     * @return HelpDeskMessage
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
     * Set Manager
     *
     * @param User|null $manager
     * @return HelpDeskMessage
     */
    public function setManager(User $manager = null)
    {
        $this->manager = $manager;

        return $this;
    }

    /**
     * Get Manager
     *
     * @return User|null
     */
    public function getManager()
    {
        return $this->manager;
    }

    public function getManagerInfo()
    {
        if ($manager = $this->getManager()) {
            return $manager->getDisplayName();
        }

        return '';
    }
}
