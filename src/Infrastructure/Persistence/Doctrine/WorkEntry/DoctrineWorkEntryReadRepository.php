<?php

declare(strict_types=1);


namespace App\Infrastructure\Persistence\Doctrine\WorkEntry;

use App\Domain\WorkEntry\Entity\WorkEntry;
use App\Domain\WorkEntry\Repository\WorkEntryReadRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use DateTimeImmutable;

readonly class DoctrineWorkEntryReadRepository implements WorkEntryReadRepositoryInterface
{

    public function __construct(
        private EntityManagerInterface $entityManager,
    )
    {
    }


    public function all(string $userId): array
    {
        return $this->entityManager->createQueryBuilder()
            ->select('w')
            ->from(WorkEntry::class, 'w')
            ->where('w.user = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getResult();

    }

    public function findById(string $id): ?WorkEntry
    {
        return $this->entityManager->getRepository(WorkEntry::class)->find($id);
    }

    public function findOpenWorkEntryByUserId(string $userId): ?WorkEntry
    {
        $qb = $this->entityManager->createQueryBuilder();

        return $qb->select('w')
            ->from(WorkEntry::class, 'w')
            ->where('w.user = :userId')
            ->andWhere('w.endDate IS NULL')
            ->andWhere('w.deletedAt IS NULL')
            ->setParameter('userId', $userId)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findPreviousWorkEntry(string $userId, DateTimeImmutable $startDate): ?WorkEntry
    {
        return $this->entityManager->createQueryBuilder()
            ->select('w')
            ->from(WorkEntry::class, 'w')
            ->where('w.user = :userId')
            ->andWhere('w.endDate <= :startDate')
            ->andWhere('w.deletedAt IS NULL')
            ->orderBy('w.endDate', 'DESC')
            ->setMaxResults(1)
            ->setParameter('userId', $userId)
            ->setParameter('startDate', $startDate)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findNextWorkEntry(string $userId, DateTimeImmutable $endDate): ?WorkEntry
    {
        return $this->entityManager->createQueryBuilder()
            ->select('w')
            ->from(WorkEntry::class, 'w')
            ->where('w.user = :userId')
            ->andWhere('w.startDate >= :endDate')
            ->andWhere('w.deletedAt IS NULL')
            ->orderBy('w.startDate', 'ASC')
            ->setMaxResults(1)
            ->setParameter('userId', $userId)
            ->setParameter('endDate', $endDate)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getWorkEntriesForToday(string $userId): array
    {
        return $this->entityManager->createQueryBuilder()
            ->select('w')
            ->from(WorkEntry::class, 'w')
            ->where('w.user = :userId')
            ->andWhere('w.deletedAt IS NULL')
            ->andWhere('(
                (w.startDate >= :startOfDay AND w.startDate <= :endOfDay) OR
                (w.startDate <= :startOfDay AND (w.endDate IS NULL OR (w.endDate >= :startOfDay AND w.endDate <= :endOfDay)))
            )')
            ->setParameter('userId', $userId)
            ->setParameter('startOfDay', (new \DateTimeImmutable('today'))->setTime(0, 0))
            ->setParameter('endOfDay', (new \DateTimeImmutable('tomorrow'))->setTime(0, 0)->modify('-1 second'))
            ->getQuery()
            ->getResult();
    }
}
