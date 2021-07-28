<?php

namespace App\Entity;

use App\Repository\ClubRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ClubRepository::class)
 */
class Club
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $presentation;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPresentation()
    {
        return $this->presentation;
    }

    public function setPresentation($presentation): self
    {
        $this->presentation = $presentation;

        return $this;
    }
}
