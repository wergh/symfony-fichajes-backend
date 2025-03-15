<?php

namespace App\Domain\WorkEntry\ValueObject;

use App\Domain\Shared\ValueObject\IdGeneratorInterface;
use App\Domain\Shared\ValueObject\UuidValueObject;

final class WorkEntryId extends UuidValueObject
{
    public static function create(IdGeneratorInterface $generator): WorkEntryId
    {
        return new self($generator->generate());
    }

    public static function fromString(string $id): WorkEntryId
    {
        return new self($id);
    }
}
