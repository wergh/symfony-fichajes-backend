<?php

declare(strict_types=1);


namespace App\Application\Mapper\User;

use App\Application\Dto\User\UserDto;
use App\Domain\User\Entity\User;
use App\Domain\WorkEntry\Entity\WorkEntry;

class UserDtoMapper
{

    public function toDTO(User $user, int $totalTime = 0): UserDto
    {
        return new UserDto(
            (string)$user->getId(),
            $user->getName(),
            $this->mapWorkEntries($user->getWorkEntries()->toArray()),
            $totalTime
        );
    }

    private function mapWorkEntries(array $workEntries): array
    {
        return array_map(fn(WorkEntry $entry) => [
            'id' => (string) $entry->getId(),
            'startDate' => $entry->getStartDate()->format('Y-m-d H:i:s'),
            'endDate' => ($entry->getEndDate()) ? $entry->getEndDate()->format('Y-m-d H:i:s') : null,
        ], $workEntries);
    }

}
