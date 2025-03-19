<?php

declare(strict_types=1);


namespace App\Application\UseCase\WorkEntry;

use App\Application\UseCase\User\GetUserByIdUseCase;
use App\Domain\Shared\Exceptions\EntityNotFoundException;
use App\Domain\WorkEntry\Entity\WorkEntry;
use App\Domain\WorkEntry\Exception\NotWorkEntryOpenException;
use App\Domain\WorkEntry\Exception\UnauthorizedAccessToWorkEntry;
use App\Domain\WorkEntry\Repository\WorkEntryReadRepositoryInterface;
use App\Domain\WorkEntry\Repository\WorkEntryWriteRepositoryInterface;
use App\Domain\WorkEntry\Service\WorkEntryDomainService;

final readonly class CloseWorkEntryUseCase
{

    public function __construct(
        private GetUserByIdUseCase                $getUserByIdUseCase,
        private WorkEntryReadRepositoryInterface  $workEntryReadRepository,
        private WorkEntryWriteRepositoryInterface $workEntryWriteRepository,
    )
    {
    }

    /**
     * @throws NotWorkEntryOpenException
     * @throws EntityNotFoundException
     */
    public function execute($userId): WorkEntry
    {
        $user = $this->getUserByIdUseCase->execute($userId);

        $workEntry = $this->workEntryReadRepository->findOpenWorkEntryByUserId((string)$user->getId());

        if (!$workEntry) {
            throw new NotWorkEntryOpenException('No work entry open');
        }

        $workEntry->close();
        $this->workEntryWriteRepository->save($workEntry);

        return $workEntry;

    }
}
