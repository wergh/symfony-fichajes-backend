<?php

declare(strict_types=1);


namespace App\Domain\User\Repository;

use App\Domain\User\Entity\User;

interface UserWriteRepositoryInterface
{

    public function save(User $user, $flush = true): void;
}
