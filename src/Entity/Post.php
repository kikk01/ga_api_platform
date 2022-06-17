<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Controller\PostCountController;
use App\Controller\PostPublishedController;
use App\Repository\PostRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Valid;

#[ORM\Entity(repositoryClass: PostRepository::class)]
#[
    ApiFilter(
        SearchFilter::class, properties: ['id' => 'exact', 'title' => 'partial']
    ),
    ApiResource(
        collectionOperations: [
            'post',
            'get',
            'count' => [
                'method' => 'GET',
                'path' => 'posts/count',
                'controller' => PostCountController::class,
                'read' => false,
                'filters' => [],
                'pagination_enabled' => false,
                'openapi_context' => [
                    'summary' => 'récupère le nombre total d\'article',
                    'parameters' => [
                        [
                            'in' => 'query',
                            'name' => 'online',
                            'schema' => [
                                'type' => 'integer',
                                'maximum' => 1,
                                'minimum' => 0
                            ],
                            'description' => 'Filtre les articles en ligne'
                        ]
                    ],
                    'responses' => [
                        '200' => [
                            'description' => 'OK',
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        'type' => 'integer',
                                        'example' => 3
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ],
        itemOperations: [
            'put',
            'delete',
            'get' => [
                'normalization_context' => [
                    'groups' => ['read:collection', 'read:item', 'read:Post'],
                    'openapi_definition_name' => 'Detail'
                ]
            ],
            'publish' => [
                'method' => 'POST',
                'path' => '/posts/{id}/publish',
                'controller' => PostPublishedController::class,
                'openapi_context' => [
                    'summary' => 'Permet de publier un article',
                ]
            ]
        ],
        denormalizationContext: ['groups' => ['write:Post']],
        normalizationContext: [
            'groups' => ['read:collection'],
            'openapi_definition_name' => 'Collection'
        ],
        paginationClientItemsPerPage: true,
        paginationItemsPerPage: 2,
        paginationMaximumItemsPerPage: 2
    )
]
class Post
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups('read:collection')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[
        Groups(['read:collection', 'write:Post']),
        Length(min: 5, groups: ['create:Post'])
    ]
    private $title;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['read:collection', 'write:Post'])]
    private $slug;

    #[ORM\Column(type: 'text')]
    #[Groups(['read:item', 'write:Post'])]
    private $content;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups('read:item')]
    private $createdAt;

    #[ORM\Column(type: 'datetime')]
    private $updatedAt;

    #[ORM\ManyToOne(targetEntity: Category::class, cascade: ['persist'], inversedBy: 'posts')]
    #[
        Groups(['read:item', 'write:Post']),
        Valid
    ]
    private $category;

    #[ORM\Column(type: 'boolean', options: ["default" => 0])]
    #[
        Groups('read:collection'),
        ApiProperty(openapiContext: ['type' => 'boolean', 'description' => 'en ligne ou pas ?'])
    ]
    private bool $online = false;

    public static function validationGroups(self $post): array
    {
        return ['create:Post'];
    }

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTime();
    }

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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

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

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function isOnline(): ?bool
    {
        return $this->online;
    }

    public function setOnline(bool $online): self
    {
        $this->online = $online;

        return $this;
    }
}
