<?php

namespace App\Entity;

use App\Repository\OffersRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OffersRepository::class)
 */
class Offers
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
    private $title;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $address;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $zipCode;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $city;

    /**
     * @ORM\Column(type="datetime")
     */
    private $creationDate;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updateDate;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $endContract;

    /**
     * @ORM\ManyToOne(targetEntity=TypesContracts::class, inversedBy="offers")
     * @ORM\JoinColumn(nullable=false)
     */
    private $typeContract;

    /**
     * @ORM\ManyToOne(targetEntity=KindsContracts::class, inversedBy="offers")
     * @ORM\JoinColumn(nullable=false)
     */
    private $kindContract;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getZipCode(): ?string
    {
        return $this->zipCode;
    }

    public function setZipCode(string $zipCode): self
    {
        $this->zipCode = $zipCode;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getCreationDate(): ?\DateTimeInterface
    {
        return $this->creationDate;
    }

    public function setCreationDate(\DateTimeInterface $creationDate): self
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    public function getUpdateDate(): ?\DateTimeInterface
    {
        return $this->updateDate;
    }

    public function setUpdateDate(\DateTimeInterface $updateDate): self
    {
        $this->updateDate = $updateDate;

        return $this;
    }

    public function getEndContract(): ?\DateTimeInterface
    {
        return $this->endContract;
    }

    public function setEndContract(?\DateTimeInterface $endContract): self
    {
        $this->endContract = $endContract;

        return $this;
    }

    public function getTypesContracts(): ?TypesContracts
    {
        return $this->typeContract;
    }

    public function setTypesContracts(?TypesContracts $typeContract): self
    {
        $this->typeContract = $typeContract;

        return $this;
    }

    public function getKindContract(): ?KindsContracts
    {
        return $this->kindContract;
    }

    public function setKindContract(?KindsContracts $kindContract): self
    {
        $this->kindContract = $kindContract;

        return $this;
    }
}
