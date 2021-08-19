<?php

namespace App\Entity;

use App\Repository\EquidRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=EquidRepository::class)
 */
class Equid
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=155)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=35, nullable=true)
     */
    private $sex;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $size;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $birthDate;

    /**
     * @ORM\Column(type="string", length=155, nullable=true)
     */
    private $city;

    /**
     * @ORM\Column(type="string", length=55, nullable=true)
     */
    private $cp;

    /**
     * @ORM\Column(type="string", length=155, nullable=true)
     */
    private $departement;

    /**
     * @ORM\OneToOne(targetEntity=Post::class, mappedBy="equid", cascade={"persist", "remove"})
     */
    private $post;

    /**
     * @ORM\OneToOne(targetEntity=User::class, mappedBy="equid", cascade={"persist", "remove"})
     */
    private $user;

    /**
     * @ORM\Column(type="string", length=55, nullable=true)
     */
    private $breed;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getSex(): ?string
    {
        return $this->sex;
    }

    public function setSex(?string $sex): self
    {
        $this->sex = $sex;

        return $this;
    }

    public function getSize(): ?float
    {
        return $this->size;
    }

    public function setSize(?float $size): self
    {
        $this->size = $size;

        return $this;
    }

    public function getBirthDate(): ?\DateTimeInterface
    {
        return $this->birthDate;
    }

    public function setBirthDate(?\DateTimeInterface $birthDate): self
    {
        $this->birthDate = $birthDate;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getCp(): ?string
    {
        return $this->cp;
    }

    public function setCp(?string $cp): self
    {
        $this->cp = $cp;

        return $this;
    }

    public function getDepartement(): ?string
    {
        return $this->departement;
    }

    public function setDepartement(?string $departement): self
    {
        $this->departement = $departement;

        return $this;
    }

    public function getPost(): ?Post
    {
        return $this->post;
    }

    public function setPost(?Post $post): self
    {
        // unset the owning side of the relation if necessary
        if ($post === null && $this->post !== null) {
            $this->post->setEquid(null);
        }

        // set the owning side of the relation if necessary
        if ($post !== null && $post->getEquid() !== $this) {
            $post->setEquid($this);
        }

        $this->post = $post;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        // unset the owning side of the relation if necessary
        if ($user === null && $this->user !== null) {
            $this->user->setEquid(null);
        }

        // set the owning side of the relation if necessary
        if ($user !== null && $user->getEquid() !== $this) {
            $user->setEquid($this);
        }

        $this->user = $user;

        return $this;
    }

    public function getBreed(): ?string
    {
        return $this->breed;
    }

    public function setBreed(?string $breed): self
    {
        $this->breed = $breed;

        return $this;
    }

    // to string function (in order to get names of categories instead of objects)
    public function __toString()
    {
        return $this->getName();
    }
}
