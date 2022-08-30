<?php

namespace App\Entity;

use App\Repository\SpecialDateRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SpecialDateRepository::class)
 */
class SpecialDate {

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

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
     * @ORM\Column(type="boolean")
     */
    private $active;

    /**
     * @ORM\ManyToOne(targetEntity=Shop::class, inversedBy="specialDates", cascade={"persist"})
     */
    private $shop;

    public function getId(): ?int {
        return $this->id;
    }

    public function getStartTime(): ?\DateTimeInterface {
        return $this->start_time;
    }

    public function setStartTime(?\DateTimeInterface $start_time): self {
        $this->start_time = $start_time;

        return $this;
    }

    public function getEndTime(): ?\DateTimeInterface {
        return $this->end_time;
    }

    public function setEndTime(?\DateTimeInterface $end_time): self {
        $this->end_time = $end_time;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self {
        $this->date = $date;

        return $this;
    }

    public function getActive(): ?bool {
        return $this->active;
    }

    public function setActive(bool $active): self {
        $this->active = $active;

        return $this;
    }

    public function getShop(): ?Shop {
        return $this->shop;
    }

    public function setShop(?Shop $shop): self {
        $this->shop = $shop;

        return $this;
    }

}
