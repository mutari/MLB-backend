<?php

namespace App\Entity;

use App\Repository\SongRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $mp3_path;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="songs")
     * @ORM\JoinColumn(nullable=false)
     */
    private $User;

    /**
     * @ORM\OneToMany(targetEntity=PlayListSong::class, mappedBy="Song")
     */
    private $playListSongs;

    public function __construct()
    {
        $this->playListSongs = new ArrayCollection();
    }

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

    public function getMp3Path(): ?string
    {
        return $this->mp3_path;
    }

    public function setMp3Path(?string $mp3_path): self
    {
        $this->mp3_path = $mp3_path;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->User;
    }

    public function setUser(?User $User): self
    {
        $this->User = $User;

        return $this;
    }

    /**
     * @return Collection|PlayListSong[]
     */
    public function getPlayListSongs(): Collection
    {
        return $this->playListSongs;
    }

    public function addPlayListSong(PlayListSong $playListSong): self
    {
        if (!$this->playListSongs->contains($playListSong)) {
            $this->playListSongs[] = $playListSong;
            $playListSong->setSong($this);
        }

        return $this;
    }

    public function removePlayListSong(PlayListSong $playListSong): self
    {
        if ($this->playListSongs->removeElement($playListSong)) {
            // set the owning side to null (unless already changed)
            if ($playListSong->getSong() === $this) {
                $playListSong->setSong(null);
            }
        }

        return $this;
    }
}
