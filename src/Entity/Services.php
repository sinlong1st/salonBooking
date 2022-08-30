<?php

namespace App\Entity;

use App\Repository\ServicesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @ORM\Entity(repositoryClass=ServicesRepository::class)
 * @ORM\HasLifecycleCallbacks()
 */
class Services {

    const SERVER_PATH_TO_IMAGE_FOLDER = DIRECTORY_SEPARATOR . 'upload' . DIRECTORY_SEPARATOR . 'images';

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
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $Thumbnail;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $Description;

    /**
     * @ORM\Column(type="boolean")
     */
    private $Active;

    /**
     * @ORM\OneToMany(targetEntity=ShopService::class, mappedBy="Service")
     */
    private $ShopServices;
    private ?UploadedFile $Thumbnailfile = null;

    public function __construct() {
        $this->ShopServices = new ArrayCollection();
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getName(): ?string {
        return $this->Name;
    }

    public function setName(string $Name): self {
        $this->Name = $Name;

        return $this;
    }

    public function getThumbnail(): ?string {
        return $this->Thumbnail;
    }

    public function setThumbnail(?string $Thumbnail): self {
        $this->Thumbnail = $Thumbnail;

        return $this;
    }

    public function getDescription(): ?string {
        return $this->Description;
    }

    public function setDescription(?string $Description): self {
        $this->Description = $Description;

        return $this;
    }

    public function getPrice(): ?float {
        return $this->Price;
    }

    public function setPrice(float $Price): self {
        $this->Price = $Price;

        return $this;
    }

    public function getActive(): ?bool {
        return $this->Active;
    }

    public function setActive(bool $Active): self {
        $this->Active = $Active;

        return $this;
    }

    /**
     * @return Collection|ShopService[]
     */
    public function getServices(): Collection {
        return $this->ShopServices;
    }

    public function addService(ShopService $service): self {
        if (!$this->ShopServices->contains($service)) {
            $this->ShopServices[] = $service;
            $service->setService($this);
        }

        return $this;
    }

    public function removeService(ShopService $service): self {
        if ($this->ShopServices->removeElement($service)) {
            // set the owning side to null (unless already changed)
            if ($service->getService() === $this) {
                $service->setService(null);
            }
        }

        return $this;
    }

    public function setThumbnailfile(?UploadedFile $Thumbnailfile = null): void {
        $this->Thumbnailfile = $Thumbnailfile;
    }

    public function getThumbnailfile(): ?UploadedFile {
        return $this->Thumbnailfile;
    }

    public function getFullBaseThumnail() {
        return DIRECTORY_SEPARATOR . self::SERVER_PATH_TO_IMAGE_FOLDER . $this->getThumbnail();
    }

}
