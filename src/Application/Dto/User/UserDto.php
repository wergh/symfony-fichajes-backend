<?php

declare(strict_types=1);


namespace App\Application\Dto\User;

class UserDto
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly array  $workEntries,
        public readonly int    $totalTime,
    )
    {
    }
}
