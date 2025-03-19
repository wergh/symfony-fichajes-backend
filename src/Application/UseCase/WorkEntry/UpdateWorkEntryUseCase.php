<?php

declare(strict_types=1);

namespace App\Application\UseCase\WorkEntry;

use App\Application\Command\WorkEntry\UpdateWorkEntryCommand;
use App\Application\Validator\WorkEntry\UpdateWorkEntryValidatorInterface;
use App\Domain\Shared\Exceptions\EntityNotFoundException;
use App\Domain\User\Exception\EndDateInTheFutureNotAllowed;
use App\Domain\WorkEntry\Entity\WorkEntry;
use App\Domain\WorkEntry\Exception\NotOverlapException;
use App\Domain\WorkEntry\Exception\UnauthorizedAccessToWorkEntry;
use App\Domain\WorkEntry\Exception\WorkEntryIsAlreadyOpenException;
use App\Domain\WorkEntry\Repository\WorkEntryReadRepositoryInterface;
use App\Domain\WorkEntry\Repository\WorkEntryWriteRepositoryInterface;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final readonly class UpdateWorkEntryUseCase
{
    public function __construct(
        private GetWorkEntryByIdUseCase           $getWorkEntryByIdUseCase,
        private WorkEntryWriteRepositoryInterface $workEntryWriteRepository,
        private WorkEntryReadRepositoryInterface  $workEntryReadRepository,
        private UpdateWorkEntryValidatorInterface $validator,
        private MessageBusInterface               $eventBus
    )
    {
    }

    /**
     * @throws WorkEntryIsAlreadyOpenException
     * @throws UnauthorizedAccessToWorkEntry
     * @throws ExceptionInterface
     * @throws EntityNotFoundException
     * @throws NotOverlapException
     * @throws EndDateInTheFutureNotAllowed
     */
    public function execute(UpdateWorkEntryCommand $command): WorkEntry
    {
        $this->validator->validate($command);

        $workEntry = $this->getWorkEntryByIdUseCase->execute($command->getUserId(), $command->getWorkEntryId());

        if ($workEntry->isOpen()) {
            throw new WorkEntryIsAlreadyOpenException('Work entry is already open');
        }

        $previousEntry = $this->workEntryReadRepository->findPreviousWorkEntry($command->getUserId(), $workEntry->getStartDate());
        $nextEntry = $this->workEntryReadRepository->findNextWorkEntry($command->getUserId(), $workEntry->getEndDate());


        $workEntry->update($command->getUserId(), $command->getStartDate(), $command->getEndDate(), $previousEntry, $nextEntry);

        $this->workEntryWriteRepository->save($workEntry);

        foreach ($workEntry->releaseEvents() as $event) {
            $this->eventBus->dispatch($event);
        }

        return $workEntry;
    }

}
