<?php

declare(strict_types=1);


namespace App\Infrastructure\EventListener;


use App\Application\Command\WorkEntryLog\CreateWorkEntryLogCommand;
use App\Application\UseCase\WorkEntryLog\CreateWorkEntryLogUseCase;
use App\Domain\WorkEntry\Events\WorkEntryUpdatedEvent;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class WorkEntryUpdatedListener
{
    public function __construct(private readonly CreateWorkEntryLogUseCase $createWorkEntryLogUseCase) {}

    public function __invoke(WorkEntryUpdatedEvent $event): void
    {

        $command = new CreateWorkEntryLogCommand(
            $event->getWorkEntryId(),
            $event->getUserId(),
            $event->getStartDate(),
            $event->getEndDate(),
        );

        $this->createWorkEntryLogUseCase->execute($command);
    }

}
