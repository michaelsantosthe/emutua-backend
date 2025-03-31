<?php 

namespace App\Entities;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Entities\PersonalAccessToken;

#[ORM\Entity]
#[ORM\Table(name: "users")]
class User implements UserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\Column(type: "string", length: 255)]
    private string $name;

    #[ORM\Column(type: "string", length: 255, unique: true)]
    private string $email;

    #[ORM\Column(type: "string", length: 255)]
    private string $password;

    #[ORM\Column(type: "datetime", nullable: true)]
    private ?\DateTimeInterface $emailVerifiedAt = null;

    #[ORM\Column(type: "string", length: 100, nullable: true)]
    private ?string $rememberToken = null;

    #[ORM\Column(type: "datetime")]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    private \DateTimeInterface $createdAt;

    #[ORM\Column(type: "datetime")]
    private \DateTimeInterface $updatedAt;

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = password_hash($password, PASSWORD_BCRYPT);
        return $this;
    }

    public function getEmailVerifiedAt(): ?\DateTimeInterface
    {
        return $this->emailVerifiedAt;
    }

    public function setEmailVerifiedAt(?\DateTimeInterface $emailVerifiedAt): self
    {
        $this->emailVerifiedAt = $emailVerifiedAt;
        return $this;
    }

    public function getRememberToken(): ?string
    {
        return $this->rememberToken;
    }

    public function setRememberToken(?string $rememberToken): self
    {
        $this->rememberToken = $rememberToken;
        return $this;
    }

    /** Métodos do UserInterface */
    public function getRoles(): array
    {
        return ['ROLE_USER'];
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): \DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function getSalt(): ?string
    {
        return null;
    }

    public function eraseCredentials(): void
    {
        // Se você armazenar dados sensíveis temporariamente, limpe-os aqui
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function createToken(string $name, array $abilities = ['*']): PersonalAccessToken
    {
        $token = new PersonalAccessToken();
        $token->setToken(bin2hex(random_bytes(40)));
        $token->setUserId($this->getId());
        $token->setAbilities($abilities);
        $token->setCreatedAt(new \DateTime());
    
        $this->entityManager->persist($token);
        $this->entityManager->flush();
    
        return $token;
    }
    

    public function checkToken(string $providedToken): ?PersonalAccessToken
    {
        $tokenRepository = $this->entityManager->getRepository(PersonalAccessToken::class);
        $token = $tokenRepository->findOneBy(['token' => $providedToken]);

        return $token && $token->getUserId() === $this->getId() ? $token : null;
    }
}
