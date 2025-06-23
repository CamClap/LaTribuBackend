<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiSubresource;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Link;
use App\Repository\UserRepository;
use App\State\UserPasswordHasher;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use App\Controller\UserGroupsController;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'Il existe déjà un compte avec cet email.')]
#[ApiResource(
    operations: [
        new GetCollection(),
        new Get(
                    name: 'groups_for_user',
                    uriTemplate: '/users/{id}/groups',
                    controller: UserGroupsController::class,
                    read: false
                ),
        new GetCollection(
            uriTemplate: '/users/{id}/groups',
            uriVariables: [
                'id' => new Link(
                    fromClass: User::class,
                    fromProperty: 'family'
                )
            ],
            normalizationContext: ['groups' => ['group:read']],
            security: "is_granted('ROLE_USER')"
        ),
        new Get(),
        new Post(processor: UserPasswordHasher::class),
        new Put(processor: UserPasswordHasher::class),
        new Delete(security: "is_granted('ROLE_ADMIN')")
    ],
      normalizationContext: ['groups' => ['group:read', 'user:read']],
      denormalizationContext: ['groups' => ['group:write', 'user:create', 'user:write']]
)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['user:read', 'group:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: "Le nom est obligatoire.")]
    #[Assert\Length(min: 3, minMessage: "Le nom doit contenir au moins 3 caractères.")]
    #[Groups(['user:read', 'user:create','user:write'])]
    private ?string $name = null;

    #[ORM\Column(length: 100, nullable: true)]
    #[Groups(['user:read'])]
    private ?string $nickname = null;

    #[ORM\Column(length: 180)]
    #[Assert\NotBlank(message: "L'email est obligatoire.")]
    #[Assert\Email(message: "Le format de l'email est invalide.")]
    #[Groups(['user:read', 'user:create'])]
    private ?string $email = null;

    #[ORM\Column]
    #[Groups(['user:read'])]
    private array $roles = [];

    #[ORM\Column]
    #[Assert\NotBlank(message: "Le mot de passe est obligatoire.")]
    #[Groups(['user:create'])]
    private ?string $password = null;

    /**
     * @var Collection<int, Group>
     */
    #[ORM\ManyToMany(targetEntity: Group::class, mappedBy: 'users')]
    #[ApiSubresource]
    #[Groups(['user:read', 'group:read'])]
    private Collection $family;

    public function __construct()
    {
        $this->family = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }

    public function getName(): ?string { return $this->name; }
    public function setName(string $name): static { $this->name = $name; return $this; }

    public function getNickname(): ?string { return $this->nickname; }
    public function setNickname(?string $nickname): static { $this->nickname = $nickname; return $this; }

    public function getEmail(): ?string { return $this->email; }
    public function setEmail(string $email): static { $this->email = $email; return $this; }

    public function getUserIdentifier(): string { return (string) $this->email; }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

    public function setRoles(array $roles): static { $this->roles = $roles; return $this; }

    public function getPassword(): ?string { return $this->password; }
    public function setPassword(string $password): static { $this->password = $password; return $this; }

    public function eraseCredentials(): void { }

    public function getFamily(): Collection { return $this->family; }

    public function addFamily(Group $family): static
    {
        if (!$this->family->contains($family)) {
            $this->family->add($family);
            $family->addUser($this);
        }
        return $this;
    }

    public function removeFamily(Group $family): static
    {
        if ($this->family->removeElement($family)) {
            $family->removeUser($this);
        }
        return $this;
    }
}
