<?php

namespace App\Entity;

use App\Repository\BookingRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=BookingRepository::class)
 */
class Booking
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $customer_id;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $start_time;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $end_time;

    /**
     * @ORM\Column(type="date")
     */
    private $date;

    /**
     * @ORM\ManyToOne(targetEntity=ShopService::class, inversedBy="bookings")
     */
    private $ShopService;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $bookingStatus;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $Address;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $Address2;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $City;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $State;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $ZipCode;

    /**
     * @ORM\Column(type="integer")
     */
    private $ServiceType;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $CustomerName;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $Phone;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCustomerId(): ?int
    {
        return $this->customer_id;
    }

    public function setCustomerId(int $customer_id): self
    {
        $this->customer_id = $customer_id;

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

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getShopService(): ?ShopService
    {
        return $this->ShopService;
    }

    public function setShopService(?ShopService $ShopService): self
    {
        $this->ShopService = $ShopService;

        return $this;
    }

    public function getBookingStatus(): ?int
    {
        return $this->bookingStatus;
    }

    public function setBookingStatus(?int $bookingStatus): self
    {
        $this->bookingStatus = $bookingStatus;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->Address;
    }

    public function setAddress(?string $Address): self
    {
        $this->Address = $Address;

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

    public function setState(string $State): self
    {
        $this->State = $State;

        return $this;
    }

    public function getZipCode(): ?string
    {
        return $this->ZipCode;
    }

    public function setZipCode(string $ZipCode): self
    {
        $this->ZipCode = $ZipCode;

        return $this;
    }

    public function getServiceType(): ?int
    {
        return $this->ServiceType;
    }

    public function setServiceType(int $ServiceType): self
    {
        $this->ServiceType = $ServiceType;

        return $this;
    }

    public function getCustomerName(): ?string
    {
        return $this->CustomerName;
    }

    public function setCustomerName(string $CustomerName): self
    {
        $this->CustomerName = $CustomerName;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->Phone;
    }

    public function setPhone(string $Phone): self
    {
        $this->Phone = $Phone;

        return $this;
    }
}
