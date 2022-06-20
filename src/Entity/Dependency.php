<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;

#[ApiResource(
    collectionOperations: ['get'], itemOperations: ['get'], paginationEnabled: false
)]
class Dependency
{
    public function __construct(
        #[ApiProperty(identifier: true)]
        private readonly string $uuid,
        #[ApiProperty(description: 'Nom de la dépendance')]
        private readonly string $name,
        #[ApiProperty(description: 'Version de la dépendance', example: "5.2.*")]
        private readonly string $version
    ) {}

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


}