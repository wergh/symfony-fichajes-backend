<?php

declare(strict_types=1);


namespace App\Domain\User\Service;

use App\Domain\User\Entity\User;
use App\Domain\User\Exception\WorkEntryAlreadyOpenException;
use App\Domain\WorkEntry\Repository\WorkEntryReadRepositoryInterface;

class UserDomainService
{
    public function __construct(
        private readonly WorkEntryReadRepositoryInterface $workEntryRepository,
    )
    {
    }

    public function validateUserCanCreateWorkEntry(User $user): void
    {
        if($this->workEntryRepository->findOpenWorkEntryByUserId((string) $user->getId())) {
            throw new WorkEntryAlreadyOpenException('User already has an open work entry.');
        }
    }
}
