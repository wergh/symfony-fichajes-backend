<?php

declare(strict_types=1);


namespace App\Application\UseCase\WorkEntry;

use App\Domain\Shared\Exceptions\EntityNotFoundException;
use App\Domain\WorkEntry\Exception\UnauthorizedAccessToWorkEntry;
use App\Domain\WorkEntry\Repository\WorkEntryWriteRepositoryInterface;

final readonly class DeleteWorkEntryUseCase
{

    public function __construct(
        private GetWorkEntryByIdUseCase           $getWorkEntryByIdUseCase,
        private WorkEntryWriteRepositoryInterface $workEntryWriteRepository,
    )
    {
    }

    /**
     * @throws UnauthorizedAccessToWorkEntry
     * @throws EntityNotFoundException
     */
    public function execute($userId, $workEntryId): void
    {

        $workEntry = $this->getWorkEntryByIdUseCase->execute($userId, $workEntryId);

        $workEntry->delete();

        $this->workEntryWriteRepository->save($workEntry);

    }
}
