<?php

declare(strict_types=1);


namespace App\Domain\WorkEntry\Service;

use App\Domain\User\Entity\User;
use App\Domain\User\Exception\WorkEntryAlreadyOpenException;
use App\Domain\WorkEntry\Entity\WorkEntry;
use App\Domain\WorkEntry\Exception\UnauthorizedAccessToWorkEntry;
use App\Domain\WorkEntry\Repository\WorkEntryReadRepositoryInterface;

final class WorkEntryDomainService
{

    public function __construct(
        private readonly WorkEntryReadRepositoryInterface $workEntryRepository,
    )
    {
    }

    public function validateUserCanCreateWorkEntry(User $user): void
    {

        if ($this->workEntryRepository->findOpenWorkEntryByUserId((string) $user->getId())) {
            throw new WorkEntryAlreadyOpenException('User already has an open work entry.');
        }
    }

    public function canAccessToWorkEntry(User $user, WorkEntry $workEntry): bool
    {

        //Aquí se deberían ejecutar la lógica de acceso al WorkEntry, por ejemplo
        //Solo podría recuperar los datos de una work entry el propio usuario
        //y aquellos usuarios que tuvieran el permiso para poder acceder a work entries ajenas a
        //las suyas.
        if ($user->getId() !== $workEntry->getUser()->getId()) {
            throw new UnauthorizedAccessToWorkEntry('Unauthorized access to WorkEntry');
        }

        return true;
    }
}
