<?php

declare(strict_types=1);


namespace App\Application\Dto\WorkEntry;

use DateTimeImmutable;

class WorkEntryDto
{
    public function __construct(
        public readonly string $id,
        public readonly string $startDate,
        public readonly ?string $endDate,
    ) {}
}
