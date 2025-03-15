<?php

declare(strict_types=1);

namespace App\Application\Command\WorkEntry;

class CreateWorkEntryCommand
{

    public function __construct(
        private readonly string $userId,
    ) {}

    public function getUserId(): string
    {
        return $this->userId;
    }

}
