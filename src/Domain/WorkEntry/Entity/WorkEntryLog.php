<?php

declare(strict_types=1);


namespace App\Domain\WorkEntry\Entity;

use App\Domain\User\Entity\User;
use DateTimeImmutable;

class WorkEntryLog
{
    private int $id;
    private DateTimeImmutable $startTime;
    private DateTimeImmutable $endTime;
    private WorkEntry $workEntry;
    private User $updatedBy;
    private DateTimeImmutable $createdAt;

    public function __construct(
        User              $user,
        DateTimeImmutable $startTime,
        DateTimeImmutable $endTime,
        WorkEntry         $workEntry
    )
    {
        $this->setUser($user);
        $this->setWorkEntry($workEntry);
        $this->startTime = $startTime;
        $this->endTime = $endTime;
        $this->createdAt = new DateTimeImmutable();
    }

    public function setUser(User $user): self
    {
        $this->updatedBy = $user;
        return $this;

    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getStartTime(): DateTimeImmutable
    {
        return $this->startTime;
    }

    public function setStartTime(DateTimeImmutable $startTime): self
    {
        $this->startTime = $startTime;
        return $this;
    }

    public function getEndTime(): DateTimeImmutable
    {
        return $this->endTime;
    }

    public function setEndTime(DateTimeImmutable $endTime): self
    {
        $this->endTime = $endTime;
        return $this;
    }

    public function getWorkEntry(): WorkEntry
    {
        return $this->workEntry;
    }

    public function setWorkEntry(WorkEntry $workEntry): self
    {
        $this->workEntry = $workEntry;
        return $this;
    }

    public function getUser(): User
    {
        return $this->updatedBy;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }


}
