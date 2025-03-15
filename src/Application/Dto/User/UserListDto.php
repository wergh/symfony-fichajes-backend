<?php

declare(strict_types=1);

namespace App\Application\Dto\User;

use App\Domain\User\ValueObject\UserId;

class UserListDto
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
    ) {}
}
