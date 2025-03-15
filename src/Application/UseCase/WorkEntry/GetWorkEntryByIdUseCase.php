<?php

declare(strict_types=1);


namespace App\Application\UseCase\WorkEntry;

use App\Application\UseCase\User\GetUserByIdUseCase;
use App\Domain\Shared\Exceptions\EntityNotFoundException;
use App\Domain\WorkEntry\Entity\WorkEntry;
use App\Domain\WorkEntry\Exception\UnauthorizedAccessToWorkEntry;
use App\Domain\WorkEntry\Repository\WorkEntryReadRepositoryInterface;
use App\Domain\WorkEntry\Service\WorkEntryDomainService;

final readonly class GetWorkEntryByIdUseCase
{

    public function __construct(
        private GetUserByIdUseCase               $getUserByIdUseCase,
        private WorkEntryReadRepositoryInterface $workEntryReadRepository,
        private WorkEntryDomainService           $workEntryDomainService,
    )
    {
    }

    /**
     * @throws UnauthorizedAccessToWorkEntry
     * @throws EntityNotFoundException
     */
    public function execute($userId, $workEntryId): WorkEntry
    {
        $user = $this->getUserByIdUseCase->execute($userId);

        $workEntry = $this->workEntryReadRepository->findById($workEntryId);
        if (!$workEntry) {
            throw new EntityNotFoundException('Work entry not found');
        }

        $this->workEntryDomainService->canAccessToWorkEntry($user, $workEntry);

        return $workEntry;


    }
}
