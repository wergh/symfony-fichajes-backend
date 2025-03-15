<?php

declare(strict_types=1);


namespace App\Domain\WorkEntry\Repository;

use App\Domain\WorkEntry\Entity\WorkEntry;
use DateTimeImmutable;

interface WorkEntryReadRepositoryInterface
{

    public function all(string $userId): array;

    public function findById(string $id): ?WorkEntry;

    public function findOpenWorkEntryByUserId(string $userId): ?WorkEntry;

    public function findPreviousWorkEntry(string $userId, DateTimeImmutable $startDate): ?WorkEntry;

    public function findNextWorkEntry(string $userId, DateTimeImmutable $endDate): ?WorkEntry;

    public function getWorkEntriesForToday(string $userId): array;
}
