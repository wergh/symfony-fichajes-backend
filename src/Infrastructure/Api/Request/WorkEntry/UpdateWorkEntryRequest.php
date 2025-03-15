<?php

declare(strict_types=1);


namespace App\Infrastructure\Api\Request\WorkEntry;

use App\Infrastructure\Validator\Constraints\DateTimeImmutableType;
use DateTimeImmutable;
use DateTimeInterface;
use Symfony\Component\Validator\Constraints as Assert;

class UpdateWorkEntryRequest
{

    #[Assert\NotBlank]
    #[Assert\Type('string')]
    private string $userId;

    #[Assert\NotBlank]
    #[Assert\Type('string')]
    #[DateTimeImmutableType]
    private string $startDate;

    #[Assert\NotBlank]
    #[Assert\Type('string')]
    #[DateTimeImmutableType]
    private string $endDate;

    public function __construct(string $userId = '', string $startDate = '', string $endDate = '')
    {
        $this->userId = $userId;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function setUserId(string $userId): void
    {
        $this->userId = $userId;
    }

    public function getStartDate(): string
    {
        return $this->startDate;
    }

    public function getEndDate(): string
    {
        return $this->endDate;
    }

    public function getStartDateAsDateTimeImmutable(): ?DateTimeImmutable
    {
        return DateTimeImmutable::createFromFormat(DateTimeInterface::ATOM, $this->startDate) ?: null;
    }

    public function getEndDateAsDateTimeImmutable(): ?DateTimeImmutable
    {
        return DateTimeImmutable::createFromFormat(DateTimeInterface::ATOM, $this->endDate) ?: null;
    }

}
