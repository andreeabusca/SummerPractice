<?php

namespace App\Entity;

use App\Repository\UserDetailsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserDetailsRepository::class)]
class UserDetails
{

    #[ORM\Column(length: 100)]
    private ?string $name = null;

    #[ORM\Column]
    private ?int $age = null;

    #[ORM\Column]
    private ?bool $isStudent = null;

    #[ORM\Id]
    #[ORM\OneToOne(inversedBy: 'userDetails', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $userId = null;


    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getAge(): ?int
    {
        return $this->age;
    }

    public function setAge(int $age): static
    {
        $this->age = $age;

        return $this;
    }

    public function isStudent(): ?bool
    {
        return $this->isStudent;
    }

    public function setIsStudent(bool $isStudent): static
    {
        $this->isStudent = $isStudent;

        return $this;
    }

    public function getUserId(): ?User
    {
        return $this->userId;
    }

    public function setUserId(User $userId): static
    {
        $this->userId = $userId;

        return $this;
    }
}
