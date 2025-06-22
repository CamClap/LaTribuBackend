<?php

namespace App\Entity;

use App\Repository\GroupRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\Post as ApiPost;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\ApiResource;
use App\State\GroupProcessor;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;

#[ORM\Entity(repositoryClass: GroupRepository::class)]
#[ORM\Table(name: '`group`')]
#[ApiResource(
    operations: [
        new ApiPost(processor: GroupProcessor::class),
        new GetCollection(), // GET /api/groups
    ]
)]

#[ApiFilter(SearchFilter::class, properties: ['users.id' => 'exact'])]
class Group
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['user:read', 'group:read'])]
    private ?string $name = null;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'family')]
    #[Groups(['group:read', 'group:write'])]
    private Collection $users;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->posts = new ArrayCollection();
    }

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['group:read', 'group:write'])]
    private ?User $creator = null;

     #[ORM\OneToMany(mappedBy: 'groupOfPost', targetEntity: Post::class, cascade: ['persist', 'remove'])]
        private Collection $posts;


        /**
         * @return Collection|Post[]
         */
        public function getPosts(): Collection
        {
            return $this->posts;
        }

        public function addPost(Post $post): self
        {
            if (!$this->posts->contains($post)) {
                $this->posts->add($post);
                $post->setGroupOfPost($this);
            }

            return $this;
        }

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

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        $this->users->removeElement($user);

        return $this;
    }

    public function getCreator(): ?User
    {
        return $this->creator;
    }

    public function setCreator(User $creator): self
    {
        $this->creator = $creator;

        return $this;
    }
}
