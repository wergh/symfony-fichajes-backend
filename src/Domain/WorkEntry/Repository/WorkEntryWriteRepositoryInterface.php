<?php

declare(strict_types=1);


namespace App\Domain\WorkEntry\Repository;

use App\Domain\WorkEntry\Entity\WorkEntry;

interface WorkEntryWriteRepositoryInterface
{

    public function save(WorkEntry $workEntry, $flush = true): void;

}
