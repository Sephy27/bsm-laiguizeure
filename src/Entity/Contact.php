<?php

namespace App\Entity;

use App\Repository\ContactRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: ContactRepository::class)]


#[Vich\Uploadable()]
class Contact
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id;

    #[ORM\Column(length: 50, nullable: true)]
    #[Assert\Length(min: 2, max: 50)]
    private ?string $fullName = null;

    #[ORM\Column(length: 180)]
    #[Assert\Email()]
    #[Assert\Length(min: 2, max: 180)]
    private string $email;

    #[ORM\Column(length: 100, nullable: true)]
     #[Assert\Length(min: 2, max: 100)]
    private ?string $subject = null;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank()]
    private string $message;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $featuredImage = null;

    #[Vich\UploadableField(mapping: 'uploads', fileNameProperty: 'featuredImage')]
    #[Assert\Image(minWidth: 200, maxWidth: 6000, minHeight: 200, maxHeight: 6000)]
    private ?File $featuredImageFile = null;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Assert\NotNull()]
    private ?\DateTimeImmutable $createdAt;



    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function setFullName(string $fullName): static
    {
        $this->fullName = $fullName;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(string $subject): static
    {
        $this->subject = $subject;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): static
    {
        $this->message = $message;

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

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

  
}
