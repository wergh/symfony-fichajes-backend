<?php

declare(strict_types=1);


namespace App\Infrastructure\Persistence\Doctrine\WorkEntry;

use App\Domain\WorkEntry\Entity\WorkEntry;
use App\Domain\WorkEntry\Repository\WorkEntryWriteRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

readonly class DoctrineWorkEntryWriteRepository implements WorkEntryWriteRepositoryInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    )
    {
    }

    public function save(WorkEntry $workEntry, $flush = true): void
    {
        $this->entityManager->persist($workEntry);
        if ($flush) {
            $this->entityManager->flush();
        }
    }
}
