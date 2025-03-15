<?php

namespace App\Domain\WorkEntry\Entity;

use App\Domain\User\Entity\User;
use App\Domain\User\Exception\EndDateInTheFutureNotAllowed;
use App\Domain\WorkEntry\Events\WorkEntryUpdatedEvent;
use App\Domain\WorkEntry\Exception\NotOverlapException;
use App\Domain\WorkEntry\ValueObject\WorkEntryId;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class WorkEntry
{

    private WorkEntryId $id;
    private User $user;
    private DateTimeImmutable $startDate;
    private ?DateTimeImmutable $endDate = null;
    private DateTimeImmutable $createdAt;
    private DateTimeImmutable $updatedAt;
    private ?DateTimeImmutable $deletedAt = null;

    private Collection $logs;

    private array $domainEvents = [];


    public function __construct(
        WorkEntryId       $id,
        User              $user,
        DateTimeImmutable $startDate
    )
    {
        $this->id = $id;
        $this->setUser($user);
        $this->startDate = $startDate;
        $this->createdAt = new DateTimeImmutable();
        $this->updatedAt = new DateTimeImmutable();
        $this->logs = new ArrayCollection();
    }

    public function getId(): WorkEntryId
    {
        return $this->id;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function getDeletedAt(): ?DateTimeImmutable
    {
        return $this->deletedAt;
    }

    public function delete(): self
    {
        $this->deletedAt = new DateTimeImmutable();
        return $this;
    }

    public function isDeleted(): bool
    {
        return $this->deletedAt !== null;
    }

    public function markAsUpdated(): void
    {
        $this->updatedAt = new DateTimeImmutable();
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): void
    {
        $this->user = $user;
    }

    public function isOpen(): bool
    {
        return $this->endDate === null;
    }

    public function close(): self
    {
        $this->setEndDate(new DateTimeImmutable());
        return $this;
    }

    /**
     * @throws NotOverlapException
     * @throws EndDateInTheFutureNotAllowed
     */
    public function update(string $userId, DateTimeImmutable $startDate, DateTimeImmutable $endDate, ?WorkEntry $previousEntry, ?WorkEntry $nextEntry): self
    {
        if (
            $startDate->format(DATE_ATOM) === $this->getStartDate()->format(DATE_ATOM) &&
            $endDate->format(DATE_ATOM) === $this->getEndDate()->format(DATE_ATOM)
        ) {
            return $this;
        }

        $this->ensureEndDateIsNotInFuture($endDate);
        $this->ensureNoOverlapWithPreviousEntry($startDate, $previousEntry);
        $this->ensureNoOverlapWithNextEntry($endDate, $nextEntry);

        $this->recordDomainEvent(new WorkEntryUpdatedEvent($userId, $this->id, $this->getStartDate(), $this->getEndDate()));
        $this->setStartDate($startDate);
        $this->setEndDate($endDate);


        return $this;
    }

    public function getStartDate(): DateTimeImmutable
    {
        return $this->startDate;
    }

    public function setStartDate(DateTimeImmutable $startDate): self
    {
        $this->startDate = $startDate;
        return $this;
    }

    public function getEndDate(): ?DateTimeImmutable
    {
        return $this->endDate;
    }

    public function setEndDate(DateTimeImmutable $endDate): self
    {
        $this->endDate = $endDate;
        return $this;
    }

    /**
     * @throws EndDateInTheFutureNotAllowed
     */
    private function ensureEndDateIsNotInFuture(DateTimeImmutable $endDate): void
    {
        if ($endDate > new DateTimeImmutable()) {
            throw new EndDateInTheFutureNotAllowed('End date cannot be in the future.');
        }
    }

    /**
     * @throws NotOverlapException
     */
    private function ensureNoOverlapWithPreviousEntry(DateTimeImmutable $startDate, ?WorkEntry $previousEntry): void
    {

        if ($previousEntry !== null && $startDate < $previousEntry->getEndDate()) {
            throw new NotOverlapException('Start date cannot be before the end date of the previous entry.');
        }
    }

    /**
     * @throws NotOverlapException
     */
    private function ensureNoOverlapWithNextEntry(DateTimeImmutable $endDate, ?WorkEntry $nextEntry): void
    {
        if ($nextEntry !== null && $endDate > $nextEntry->getStartDate()) {
            throw new NotOverlapException('End date cannot be after the start date of the next entry.');
        }
    }

    private function recordDomainEvent(object $event): void
    {
        $this->domainEvents[] = $event;
    }

    public function releaseEvents(): array
    {
        $events = $this->domainEvents;
        $this->domainEvents = [];

        return $events;
    }

    public function getLogs(): Collection
    {
        return $this->logs;
    }


}
