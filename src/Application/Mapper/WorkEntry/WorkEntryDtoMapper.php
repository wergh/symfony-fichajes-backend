<?php

declare(strict_types=1);


namespace App\Application\Mapper\WorkEntry;

use App\Application\Dto\WorkEntry\WorkEntryDto;
use App\Domain\WorkEntry\Entity\WorkEntry;

class WorkEntryDtoMapper
{

    public function toDTO(WorkEntry $workEntry): WorkEntryDto
    {
        return new WorkEntryDto(
            (string) $workEntry->getId(),
            $workEntry->getStartDate()->format('Y-m-d H:i:s'),
            ($workEntry->getEndDate()) ? $workEntry->getEndDate()->format('Y-m-d H:i:s') : null,
        );
    }
}
