<?php

namespace EditorialBundle\Model;

use Symfony\Component\Validator\Constraints as Assert;

class PasswordChangeModel
{
    /**
     * @var string
     *
     * @Assert\NotBlank(message="Zadejte aktuální heslo")
     */
    private $current;

    /**
     * @var string
     *
     * @Assert\NotBlank(message="Zadejte nové heslo")
     * @Assert\Length(min="8", minMessage="Heslo musí mít alespoň {{ limit }} znaků.")
     */
    private $new;

    /**
     * @return string
     */
    public function getCurrent()
    {
        return $this->current;
    }

    /**
     * @param string $current
     * @return PasswordChangeModel
     */
    public function setCurrent($current)
    {
        $this->current = $current;

        return $this;
    }

    /**
     * @return string
     */
    public function getNew()
    {
        return $this->new;
    }

    /**
     * @param string $new
     * @return PasswordChangeModel
     */
    public function setNew($new)
    {
        $this->new = $new;

        return $this;
    }
}
