<?php

declare(strict_types=1);


namespace App\Application\UseCase\User;

use App\Domain\User\Entity\User;
use App\Domain\WorkEntry\Repository\WorkEntryReadRepositoryInterface;
use DateTimeImmutable;

final readonly class GetTodayWorkHoursForUserUseCase
{

    public function __construct(
        private WorkEntryReadRepositoryInterface $workEntryReadRepository,
    )
    {
    }

    public function execute(User $user): string
    {
        $workEntries = $this->workEntryReadRepository->getWorkEntriesForToday((string) $user->getId());

        return $this->calculateWorkHoursForToday($workEntries);

    }

    private function calculateWorkHoursForToday(array $workEntries): string
    {
        $now = new DateTimeImmutable('now');
        $startOfDay = (new DateTimeImmutable('today'))->setTime(0, 0);
        $endOfDay = (new DateTimeImmutable('tomorrow'))->setTime(0, 0)->modify('-1 second');

        $totalSeconds = 0;

        foreach ($workEntries as $entry) {
            $startDate = $entry->getStartDate();
            $endDate = $entry->getEndDate() ?? $now;

            if ($startDate < $startOfDay) {
                $startDate = $startOfDay;
            }

            if ($endDate > $endOfDay) {
                $endDate = $endOfDay;
            }

            $totalSeconds += $endDate->getTimestamp() - $startDate->getTimestamp();
        }


        $hours = str_pad((string)floor($totalSeconds / 3600), 2, '0', STR_PAD_LEFT);
        $minutes = str_pad((string)floor(($totalSeconds % 3600) / 60), 2, '0', STR_PAD_LEFT);
        $seconds = str_pad((string)($totalSeconds % 60), 2, '0', STR_PAD_LEFT);

        return "{$hours}:{$minutes}:{$seconds}";

    }
}
