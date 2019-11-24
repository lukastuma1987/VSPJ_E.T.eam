<?php

namespace EditorialBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\Role\Role as BaseRole;

/**
 * UserRole
 *
 * @ORM\Table(name="roles")
 * @ORM\Entity(repositoryClass="EditorialBundle\Repository\RoleRepository")
 */
class Role extends BaseRole
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
     * @ORM\Column(name="role", type="string", length=30, unique=true)
     */
    private $role;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=30, unique=true)
     */
    private $name;

    /**
     * @var ArrayCollection|User[]
     *
     * @ORM\ManyToMany(targetEntity="User", mappedBy="roles")
     */
    private $users;

    public function __construct()
    {
        parent::__construct($this->role ?: 'ROLE_UNKNOWN');

        $this->users = new ArrayCollection();
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
     * Get role if exists, otherwise return "ROLE_UNKNOWN"
     *
     * @return string
     */
    public function getRole()
    {
        return $this->role ?: 'ROLE_UNKNOWN';
    }

    /**
     * Get name role name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Add User
     *
     * @param User $user
     * @return Role
     */
    public function addUser(User $user)
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->addRole($this);
        }

        return $this;
    }

    /**
     * Remove User
     *
     * @param User $user
     * @return Role
     */
    public function removeUser(User $user)
    {
        if ($this->users->contains($user)) {
            $this->users->removeElement($user);
            $user->removeRole($this);
        }

        return $this;
    }

    /**
     * Get Users
     *
     * @return ArrayCollection|User[]
     */
    public function getUsers()
    {
        return $this->users;
    }
}
