<?php

declare(strict_types=1);

namespace App\Entity;

use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class ResetPassword
{
    /**
     * @Assert\NotNull
     * @Assert\NotBlank
     * @Assert\Type("integer")
     * @Assert\Length(min=6, max=6)
     */
    private $confirmationCode;

    /**
     * @Assert\NotNull
     * @Assert\NotBlank
     * @Assert\Length(min=6)
     */
    private $password;

    /**
     * @Groups("forget-password")
     * @Assert\NotNull(groups="forget-password")
     * @Assert\NotBlank(groups="forget-password")
     * @Assert\Email(groups="forget-password")
     */
    private $email;

    public function getPassword()
    {
        return $this->password;
    }

    public function getConfirmationCode()
    {
        return $this->confirmationCode;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setConfirmationCode($confirmationCode): self
    {
        $this->confirmationCode = $confirmationCode;

        return $this;
    }

    public function setEmail($email): self
    {
        $this->email = $email;

        return $this;
    }

    public function setPassword($password): self
    {
        $this->password = $password;

        return $this;
    }
}