<?php

declare(strict_types=1);

namespace App\Domain\User\Repository;

use App\Domain\User\Entity\User;

interface UserReadRepositoryInterface
{

    public function all(): array;
    public function findById(string $id): ?User;

}
