<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Repository\UserRepository;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="`user`")
 */
class User implements UserInterface
{
    const ROLE_ADMIN = 'ROLE_ADMIN';
    const ROLE_MEMBER = 'ROLE_MEMBER';
    const ROLE_USER = 'ROLE_USER';

    static $allRoles = [
        self::ROLE_ADMIN => 'admin',
        self::ROLE_MEMBER => 'member',
        self::ROLE_USER => 'user',
    ];

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
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $lastName;

    /**
     * @ORM\OneToMany(targetEntity=Activity::class, mappedBy="createdBy", orphanRemoval=true)
     */
    private $createdActivities;

    /**
     * @ORM\OneToMany(targetEntity=Activity::class, mappedBy="assignedTo")
     */
    private $activities;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [self::ROLE_USER];

    public function __construct()
    {
        $this->createdActivities = new ArrayCollection();
        $this->activities = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * @return Collection|Activity[]
     */
    public function getCreatedActivities(): Collection
    {
        return $this->createdActivities;
    }

    public function addCreatedActivity(Activity $createdActivity): self
    {
        if (!$this->createdActivities->contains($createdActivity)) {
            $this->createdActivities[] = $createdActivity;
            $createdActivity->setCreatedBy($this);
        }

        return $this;
    }

    public function removeCreatedActivity(Activity $createdActivity): self
    {
        if ($this->createdActivities->removeElement($createdActivity)) {
            // set the owning side to null (unless already changed)
            if ($createdActivity->getCreatedBy() === $this) {
                $createdActivity->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Activity[]
     */
    public function getActivities(): Collection
    {
        return $this->activities;
    }

    public function addActivity(Activity $activity): self
    {
        if (!$this->activities->contains($activity)) {
            $this->activities[] = $activity;
            $activity->setAssignedTo($this);
        }

        return $this;
    }

    public function removeActivity(Activity $activity): self
    {
        if ($this->activities->removeElement($activity)) {
            // set the owning side to null (unless already changed)
            if ($activity->getAssignedTo() === $this) {
                $activity->setAssignedTo(null);
            }
        }

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getUsername()
    {
        return $this->email;
    }

    public function getFullName()
    {
        if($this->firstName && $this->lastName) {
            return $this->firstName.' '.$this->lastName;
        }
        return $this->email;
    }
}
