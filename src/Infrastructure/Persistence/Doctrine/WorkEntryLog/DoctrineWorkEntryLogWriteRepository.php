<?php

declare(strict_types=1);


namespace App\Infrastructure\Persistence\Doctrine\WorkEntryLog;

use App\Domain\WorkEntry\Entity\WorkEntryLog;
use App\Domain\WorkEntry\Repository\WorkEntryLogWriteRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

readonly class DoctrineWorkEntryLogWriteRepository implements WorkEntryLogWriteRepositoryInterface
{

    public function __construct(
        private EntityManagerInterface $entityManager,
    )
    {
    }


    public function save(WorkEntryLog $workEntryLog, $flush = true): void
    {
        $this->entityManager->persist($workEntryLog);
        if ($flush) {
            $this->entityManager->flush();
        }
    }

}
