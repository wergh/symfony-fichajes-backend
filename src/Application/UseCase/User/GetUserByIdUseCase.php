<?php

declare(strict_types=1);

namespace App\Application\UseCase\User;

use App\Domain\Shared\Exceptions\EntityNotFoundException;
use App\Domain\User\Entity\User;
use App\Domain\User\Repository\UserReadRepositoryInterface;

final readonly class GetUserByIdUseCase
{
    public function __construct(private UserReadRepositoryInterface $userReadRepository)
    {
    }

    public function execute(string $id): User
    {
        $user = $this->userReadRepository->findById($id);
        if (!$user) {
            throw new EntityNotFoundException("User not found");
        }
        return $user;
    }
}
