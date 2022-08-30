<?php

namespace App\Entity;

use App\Repository\ShopRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ShopRepository::class)
 */
class Shop
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=150)
     */
    private $Name;

    /**
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    private $Address1;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $Address2;

    /**
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    private $City;

    /**
     * @ORM\Column(type="string", length=30, nullable=true)
     */
    private $State;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $ZipCode;

    /**
     * @ORM\Column(type="string", length=200, nullable=true)
     */
    private $Country;

    /**
     * @ORM\Column(type="boolean")
     */
    private $Active;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $Description;

    /**
     * @ORM\OneToMany(targetEntity=ShopService::class, mappedBy="Shop")
     */
    private $Service;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $start_time;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $end_time;

    /**
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    private $Phone;

    /**
     * @ORM\OneToMany(targetEntity=SpecialDate::class, mappedBy="shop")
     */
    private $specialDates;

    public function __construct()
    {
        $this->Service = new ArrayCollection();
        $this->specialDates = new ArrayCollection();
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

    public function getAddress1(): ?string
    {
        return $this->Address1;
    }

    public function setAddress1(?string $Address1): self
    {
        $this->Address1 = $Address1;

        return $this;
    }

    public function getAddress2(): ?string
    {
        return $this->Address2;
    }

    public function setAddress2(?string $Address2): self
    {
        $this->Address2 = $Address2;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->City;
    }

    public function setCity(?string $City): self
    {
        $this->City = $City;

        return $this;
    }

    public function getState(): ?string
    {
        return $this->State;
    }

    public function setState(?string $State): self
    {
        $this->State = $State;

        return $this;
    }

    public function getZipCode(): ?string
    {
        return $this->ZipCode;
    }

    public function setZipCode(?string $ZipCode): self
    {
        $this->ZipCode = $ZipCode;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->Country;
    }

    public function setCountry(?string $Country): self
    {
        $this->Country = $Country;

        return $this;
    }

    public function getActive()
    {
        return $this->Active;
    }

    public function setActive($Active): self
    {
        $this->Active = $Active;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->Description;
    }

    public function setDescription(?string $Description): self
    {
        $this->Description = $Description;

        return $this;
    }

    /**
     * @return Collection|ShopService[]
     */
    public function getService(): Collection
    {
        return $this->Service;
    }

    public function addService(ShopService $service): self
    {
        if (!$this->Service->contains($service)) {
            $this->Service[] = $service;
            $service->setShop($this);
        }

        return $this;
    }

    public function removeService(ShopService $service): self
    {
        if ($this->Service->removeElement($service)) {
            // set the owning side to null (unless already changed)
            if ($service->getShop() === $this) {
                $service->setShop(null);
            }
        }

        return $this;
    }

    public function getStartTime(): ?\DateTimeInterface
    {
        return $this->start_time;
    }

    public function setStartTime(?\DateTimeInterface $start_time): self
    {
        $this->start_time = $start_time;

        return $this;
    }

    public function getEndTime(): ?\DateTimeInterface
    {
        return $this->end_time;
    }

    public function setEndTime(?\DateTimeInterface $end_time): self
    {
        $this->end_time = $end_time;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->Phone;
    }

    public function setPhone(?string $Phone): self
    {
        $this->Phone = $Phone;

        return $this;
    }

    /**
     * @return Collection|SpecialDate[]
     */
    public function getSpecialDates(): Collection
    {
        return $this->specialDates;
    }

    public function addSpecialDate(SpecialDate $specialDate): self
    {
        if (!$this->specialDates->contains($specialDate)) {
            $this->specialDates[] = $specialDate;
            $specialDate->setShopId($this);
        }

        return $this;
    }

    public function removeSpecialDate(SpecialDate $specialDate): self
    {
        if ($this->specialDates->removeElement($specialDate)) {
            // set the owning side to null (unless already changed)
            if ($specialDate->getShopId() === $this) {
                $specialDate->setShopId(null);
            }
        }

        return $this;
    }
}
