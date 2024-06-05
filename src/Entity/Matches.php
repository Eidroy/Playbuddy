<?php

namespace App\Entity;

use App\Repository\MatchesRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MatchesRepository::class)]
class Matches
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $user_1_id = null;

    #[ORM\Column]
    private ?int $user_2_id = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTimeInterface $time = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser1Id(): ?int
    {
        return $this->user_1_id;
    }

    public function setUser1Id(int $user_1_id): static
    {
        $this->user_1_id = $user_1_id;

        return $this;
    }

    public function getUser2Id(): ?int
    {
        return $this->user_2_id;
    }

    public function setUser2Id(int $user_2_id): static
    {
        $this->user_2_id = $user_2_id;

        return $this;
    }

    public function getTime(): ?\DateTimeInterface
    {
        return $this->time;
    }

    public function setTime(\DateTimeInterface $time): static
    {
        $this->time = $time;

        return $this;
    }
}
