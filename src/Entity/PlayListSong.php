<?php

namespace App\Entity;

use App\Repository\PlayListSongRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PlayListSongRepository::class)
 */
class PlayListSong
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Song::class, inversedBy="playListSongs")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $Song;

    /**
     * @ORM\ManyToOne(targetEntity=Playlist::class, inversedBy="playListSongs")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $PlayList;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $NumberInList;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSong(): ?Song
    {
        return $this->Song;
    }

    public function setSong(?Song $Song): self
    {
        $this->Song = $Song;

        return $this;
    }

    public function getPlayList(): ?Playlist
    {
        return $this->PlayList;
    }

    public function setPlayList(?Playlist $PlayList): self
    {
        $this->PlayList = $PlayList;

        return $this;
    }

    public function getNumberInList(): ?int
    {
        return $this->NumberInList;
    }

    public function setNumberInList(?int $NumberInList): self
    {
        $this->NumberInList = $NumberInList;

        return $this;
    }
}
