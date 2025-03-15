<?php

declare(strict_types=1);


namespace App\Infrastructure\Api\Request\WorkEntry;

use Symfony\Component\Validator\Constraints as Assert;

class CreateWorkEntryRequest
{

    #[Assert\NotBlank]
    #[Assert\Type('string')]
    private string $userId;

    public function __construct(string $userId = '')
    {
        $this->userId = $userId;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function setUserId(string $userId): void
    {
        $this->userId = $userId;
    }
}
