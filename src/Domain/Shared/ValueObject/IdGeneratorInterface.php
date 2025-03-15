<?php

namespace App\Domain\Shared\ValueObject;

interface IdGeneratorInterface
{
    public function generate(): string;
}
