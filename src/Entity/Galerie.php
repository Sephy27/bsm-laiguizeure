<?php

namespace App\Entity;

use App\Repository\GalerieRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: GalerieRepository::class)]
#[Vich\Uploadable()]
class Galerie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $featuredImage = null;

    #[Vich\UploadableField(mapping: 'uploads', fileNameProperty: 'featuredImage')]
    #[Assert\Image(minWidth: 200, maxWidth: 6000, minHeight: 200, maxHeight: 6000)]
    private ?File $featuredImageFile = null;

    #[ORM\Column]
    #[Assert\NotNull()]
    private ?\DateTimeImmutable $createdAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getFeaturedImage(): ?string
    {
        return $this->featuredImage;
    }

    public function setFeaturedImage(?string $featuredImage): self
    {
        $this->featuredImage = $featuredImage;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

     /**
     * Get the value of featuredImageFile
     */ 
    public function getFeaturedImageFile() : ?File
    {
        return $this->featuredImageFile;
    }

    /**
     * Set the value of featuredImageFile
     *
     * @return  self
     */ 
    public function setFeaturedImageFile($featuredImageFile): static
    {
        $this->featuredImageFile = $featuredImageFile;

        return $this;
    }
}

