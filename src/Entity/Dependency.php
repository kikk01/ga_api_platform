<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

#[ApiResource(
    collectionOperations: ['get', 'post'],
    itemOperations: [
        'get',
        'delete',
        'put' =>  [
            'denormalization_context' => [
                'groups' => ['put:Dependency']
            ]
        ]
    ],
    paginationEnabled: false
)]
class Dependency
{
    #[ApiProperty(identifier: true)]
    private string $uuid;

    public function __construct(
        #[
            ApiProperty(description: 'Nom de la dépendance', ),
            Length(min:2),
            NotBlank
        ]
        private readonly string $name,

        #[
            ApiProperty(description: 'Version de la dépendance', example: "5.2.*"),
            Length(min:2),
            NotBlank,
            Groups(['put:Dependency'])
        ]
        private string $version
    ) {
        $this->uuid = Uuid::uuid5(Uuid::NAMESPACE_URL, $this->name)->toString();
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function setVersion(string $version): void
    {
        $this->version = $version;
    }
}