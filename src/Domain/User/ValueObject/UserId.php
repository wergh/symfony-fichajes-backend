<?php

namespace App\Domain\User\ValueObject;

use App\Domain\Shared\ValueObject\IdGeneratorInterface;
use App\Domain\Shared\ValueObject\UuidValueObject;

final class UserId extends UuidValueObject
{

    public static function create(IdGeneratorInterface $generator): UserId
    {
        return new self($generator->generate());
    }

    public static function fromString(string $id): UserId
    {
        return new self($id);
    }


}
