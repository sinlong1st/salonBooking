<?php

namespace App\Entity;

use App\Repository\CustomerRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=CustomerRepository::class)
 * @UniqueEntity(fields={"email"}, message="There is already an account with this email")
 */
class Customer implements UserInterface, PasswordAuthenticatedUserInterface {

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isVerified = false;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    private $address;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $address2;

    /**
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    private $city;

    /**
     * @ORM\Column(type="string", length=5, nullable=true)
     */
    private $state;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $zipcode;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $additionalInfo;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $phone;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $verifySignature;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $verifyExpireDate;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $resetPasswordToken;

    public function __construct() {
        $this->setVerifySignature('');
        $this->setVerifyExpireDate(new \DateTime());
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getEmail(): ?string {
        return $this->email;
    }

    public function setEmail(string $email): self {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string {
        return (string) $this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string {
        return $this->password;
    }

    public function setPassword(string $password): self {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials() {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function isVerified(): bool {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function getFirstName(): ?string {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): self {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): self {
        $this->lastName = $lastName;

        return $this;
    }

    public function getAddress(): ?string {
        return $this->address;
    }

    public function setAddress(?string $address): self {
        $this->address = $address;

        return $this;
    }

    public function getAddress2(): ?string {
        return $this->address2;
    }

    public function setAddress2(?string $address2): self {
        $this->address2 = $address2;

        return $this;
    }

    public function getCity(): ?string {
        return $this->city;
    }

    public function setCity(?string $city): self {
        $this->city = $city;

        return $this;
    }

    public function getState(): ?string {
        return $this->state;
    }

    public function setState(?string $state): self {
        $this->state = $state;

        return $this;
    }

    public function getZipcode(): ?string {
        return $this->zipcode;
    }

    public function setZipcode(?string $zipcode): self {
        $this->zipcode = $zipcode;

        return $this;
    }

    public function getAdditionalInfo(): ?string {
        return $this->additionalInfo;
    }

    public function setAdditionalInfo(?string $additionalInfo): self {
        $this->additionalInfo = $additionalInfo;

        return $this;
    }

    public function getPhone(): ?string {
        return $this->phone;
    }

    public function setPhone(?string $phone): self {
        $this->phone = $phone;

        return $this;
    }

    public function getVerifySignature(): ?string {
        return $this->verifySignature;
    }

    public function setVerifySignature(string $verifySignature): self {
        $this->verifySignature = $verifySignature;

        return $this;
    }

    public function getVerifyExpireDate(): ?\DateTimeInterface {
        return $this->verifyExpireDate;
    }

    public function setVerifyExpireDate(?\DateTimeInterface $verifyExpireDate): self {
        $this->verifyExpireDate = $verifyExpireDate;

        return $this;
    }

    public function getResetPasswordToken(): ?string
    {
        return $this->resetPasswordToken;
    }

    public function setResetPasswordToken(?string $resetPasswordToken): self
    {
        $this->resetPasswordToken = $resetPasswordToken;

        return $this;
    }

}
