<?php

namespace App\Infrastructure\Symfony\Uuid;

use App\Domain\Shared\ValueObject\IdGeneratorInterface;
use Symfony\Component\Uid\Uuid;

class UuidGenerator implements IdGeneratorInterface
{

    public function generate(): string
    {
        return Uuid::v4()->toRfc4122();
    }
}
