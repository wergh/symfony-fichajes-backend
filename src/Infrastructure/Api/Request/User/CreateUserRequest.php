<?php

declare(strict_types=1);

namespace App\Infrastructure\Api\Request\User;

use Symfony\Component\Validator\Constraints as Assert;

class CreateUserRequest
{

    #[Assert\NotBlank]
    #[Assert\Type('string')]
    private string $name;

    public function __construct(string $name = '')
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

}
