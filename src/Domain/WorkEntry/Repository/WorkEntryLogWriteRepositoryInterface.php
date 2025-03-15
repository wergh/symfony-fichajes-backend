<?php

declare(strict_types=1);


namespace App\Domain\WorkEntry\Repository;

use App\Domain\WorkEntry\Entity\WorkEntryLog;

interface WorkEntryLogWriteRepositoryInterface
{

    public function save(WorkEntryLog $workEntryLog, $flush = true): void;
}
