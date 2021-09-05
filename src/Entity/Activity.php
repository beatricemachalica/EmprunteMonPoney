<?php

namespace App\Entity;

use App\Repository\ActivityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ActivityRepository::class)
 */
class Activity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity=Equid::class, inversedBy="activities")
     */
    private $Equid;

    public function __construct()
    {
        $this->Equid = new ArrayCollection();
    }

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

    /**
     * @return Collection|Equid[]
     */
    public function getEquid(): Collection
    {
        return $this->Equid;
    }

    public function addEquid(Equid $equid): self
    {
        if (!$this->Equid->contains($equid)) {
            $this->Equid[] = $equid;
        }

        return $this;
    }

    public function removeEquid(Equid $equid): self
    {
        $this->Equid->removeElement($equid);

        return $this;
    }

    // to string function
    public function __toString()
    {
        return $this->getName();
    }

}
