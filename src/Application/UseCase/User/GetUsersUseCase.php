<?php

declare(strict_types=1);

namespace App\Application\UseCase\User;

use App\Domain\User\Repository\UserReadRepositoryInterface;

final readonly class GetUsersUseCase
{
    public function __construct(private UserReadRepositoryInterface $userReadRepository)
    {
    }

    public function execute(): array
    {
        return $this->userReadRepository->all();
    }
}

