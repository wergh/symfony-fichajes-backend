<?php

declare(strict_types=1);


namespace App\Application\Command\WorkEntryLog;

use DateTimeImmutable;

class CreateWorkEntryLogCommand
{

    public function __construct(
        private readonly string $workEntryId,
        private readonly string $updatedById,
        private readonly DateTimeImmutable $startTime,
        private readonly DateTimeImmutable $endTime,
    ) {}

    public function getWorkEntryId(): string
    {
        return $this->workEntryId;
    }

    public function getUpdatedById(): string
    {
        return $this->updatedById;
    }

    public function getStartTime(): DateTimeImmutable
    {
        return $this->startTime;
    }

    public function getEndTime(): DateTimeImmutable
    {
        return $this->endTime;
    }

}
