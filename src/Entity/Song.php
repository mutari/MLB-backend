<?php

namespace App\Entity;

use App\Repository\SongRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SongRepository::class)
 */
class Song
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
    private $lyrics_text;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $lyrics_image_path;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $title;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLyricsText(): ?string
    {
        return $this->lyrics_text;
    }

    public function setLyricsText(?string $lyrics_text): self
    {
        $this->lyrics_text = $lyrics_text;

        return $this;
    }

    public function getLyricsImagePath(): ?string
    {
        return $this->lyrics_image_path;
    }

    public function setLyricsImagePath(?string $lyrics_image_path): self
    {
        $this->lyrics_image_path = $lyrics_image_path;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }
}
