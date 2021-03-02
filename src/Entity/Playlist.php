<?php

namespace App\Entity;

use App\Repository\PlaylistRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PlaylistRepository::class)
 */
class Playlist
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $Name;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="CreatedDate")
     * @ORM\JoinColumn(nullable=false)
     */
    private $Author;

    /**
     * @ORM\Column(type="datetime")
     */
    private $CreatedDate;

    /**
     * @ORM\OneToMany(targetEntity=PlayListSong::class, mappedBy="PlayList", orphanRemoval=true)
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

    public function getName(): ?string
    {
        return $this->Name;
    }

    public function setName(string $Name): self
    {
        $this->Name = $Name;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->Author;
    }

    public function setAuthor(?User $Author): self
    {
        $this->Author = $Author;

        return $this;
    }

    public function getCreatedDate(): ?\DateTimeInterface
    {
        return $this->CreatedDate;
    }

    public function setCreatedDate(\DateTimeInterface $CreatedDate): self
    {
        $this->CreatedDate = $CreatedDate;

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
            $playListSong->setPlayList($this);
        }

        return $this;
    }

    public function removePlayListSong(PlayListSong $playListSong): self
    {
        if ($this->playListSongs->removeElement($playListSong)) {
            // set the owning side to null (unless already changed)
            if ($playListSong->getPlayList() === $this) {
                $playListSong->setPlayList(null);
            }
        }

        return $this;
    }
}
