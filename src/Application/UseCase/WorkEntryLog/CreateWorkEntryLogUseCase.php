<?php

declare(strict_types=1);


namespace App\Application\UseCase\WorkEntryLog;

use App\Application\Command\WorkEntryLog\CreateWorkEntryLogCommand;
use App\Application\UseCase\User\GetUserByIdUseCase;
use App\Application\UseCase\WorkEntry\GetWorkEntryByIdUseCase;
use App\Domain\Shared\Exceptions\EntityNotFoundException;
use App\Domain\WorkEntry\Entity\WorkEntryLog;
use App\Domain\WorkEntry\Exception\UnauthorizedAccessToWorkEntry;
use App\Domain\WorkEntry\Repository\WorkEntryLogWriteRepositoryInterface;

readonly class CreateWorkEntryLogUseCase
{
    public function __construct(
        private WorkEntryLogWriteRepositoryInterface $workEntryLogWriteRepository,
        private GetUserByIdUseCase                   $getUserByIdUseCase,
        private GetWorkEntryByIdUseCase              $getWorkEntryByIdUseCase
    )
    {
    }

    /**
     * @throws UnauthorizedAccessToWorkEntry
     * @throws EntityNotFoundException
     */
    public function execute(CreateWorkEntryLogCommand $command): void
    {
        $user = $this->getUserByIdUseCase->execute($command->getUpdatedById());
        $workEntry = $this->getWorkEntryByIdUseCase->execute($command->getUpdatedById(), $command->getWorkEntryId());

        $workEntryLog = new WorkEntryLog(
            $user,
            $command->getStartTime(),
            $command->getEndTime(),
            $workEntry
        );

        $this->workEntryLogWriteRepository->save($workEntryLog);

    }


}
