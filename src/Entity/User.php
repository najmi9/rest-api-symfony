<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity("username")
 * @UniqueEntity("email")
 * 
 * @ApiResource(
 *      collectionOperations={
 *          "post"={
 *              "path"="/auth/register",
 *              "denormalization_context"={"groups"={"new-user"}},
 *          },
 *          "get"={
 *              "access_control"="is_granted('ROLE_ADMIN')",
 *              "normalization_context"={"groups"={"get-user"}},
 *          },
 *      },
 *     itemOperations={
 *          "get"={
 *              "normalization_context"={"groups"={"get-user"}},
 *              "access_control"="(is_granted('ROLE_USER') and object == user) or is_granted('ROLE_ADMIN')"
 *          },
 *          "delete"={
 *              "access_control"="(is_granted('ROLE_USER') and object == user) or is_granted('ROLE_ADMIN')"
 *          },
 *          "put"={
 *              "denormalization_context"={"groups"={"new-user"}},
 *              "access_control"="(is_granted('ROLE_USER') and object == user) or is_granted('ROLE_ADMIN')"
 *          }  
 *     }
 * )
 * @ApiFilter(SearchFilter::class, properties={"id": "exact", "username": "exact"})
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"get-user"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"new-user", "get-user"})
     * @Assert\NotNull
     * @Assert\NotBlank
     * @Assert\Length(min=2)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"new-user"})
     * @Assert\NotNull
     * @Assert\NotBlank
     * @Assert\Length(min=6)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"new-user", "reset-password"})
     * @Assert\NotNull
     * @Assert\NotBlank
     * @Assert\Email
     */
    private $email;

    /**
     * @ORM\Column(type="array")
     * @Groups({"get-user"})
     */
    private $roles = [];

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"get-user"})
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"get-user"})
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $confirmationToken;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $enabled;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $confirmationCode;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $codeDate;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getRoles(): ?array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        
        $this->roles = $roles;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * @ORM\PrePersist
     */
    public function setCreatedAt(): self
    {
        $this->createdAt = new \DateTime();

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */ 
    public function setUpdatedAt(): self
    {
        $this->updatedAt = new \DateTime();

        return $this;
    }

    public function getSalt()
    {

    }

    /**
     * Returns the username used to authenticate the user.
     *
     * @return string The username
     */

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {
        
    }

    public function getConfirmationToken(): ?string
    {
        return $this->confirmationToken;
    }

    public function setConfirmationToken(?string $confirmationToken): self
    {
        $this->confirmationToken = $confirmationToken;

        return $this;
    }

    public function getEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function setEnabled(?bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function getConfirmationCode(): ?int
    {
        return $this->confirmationCode;
    }

    public function setConfirmationCode(?int $confirmationCode): self
    {
        $this->confirmationCode = $confirmationCode;

        return $this;
    }

    public function getCodeDate(): ?\DateTimeInterface
    {
        return $this->codeDate;
    }

    public function setCodeDate(?\DateTimeInterface $codeDate): self
    {
        $this->codeDate = $codeDate;

        return $this;
    }
}
