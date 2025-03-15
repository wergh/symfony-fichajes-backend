<?php

declare(strict_types=1);

namespace App\Domain\Shared\ValueObject;

abstract class UuidValueObject
{

    protected string $value;

    protected function __construct(string $value)
    {
        // Aquí podrías agregar validación para asegurar que es un UUID válido
        $this->value = $value;
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(UuidValueObject $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

}
