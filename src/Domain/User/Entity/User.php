<?php

declare(strict_types=1);

namespace App\Domain\User\Entity;

use App\Domain\User\ValueObject\UserId;
use App\Domain\WorkEntry\Entity\WorkEntry;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use App\Domain\WorkEntry\Entity\WorkEntryLog;

class User
{

    private UserId $id;

    private string $name;

    private DateTimeImmutable $createdAt;

    private DateTimeImmutable $updatedAt;

    private ?DateTimeImmutable $deletedAt;

    private Collection $workEntries;

    private Collection $updatedWorkEntryLogs;


    public function __construct(UserId $id, string $name)
    {
        $this->id = $id;
        $this->name = $name;
        $this->workEntries = new ArrayCollection();
        $this->updatedWorkEntryLogs = new ArrayCollection();
        $this->createdAt = new DateTimeImmutable();
        $this->updatedAt = new DateTimeImmutable();
    }

    public function getId(): UserId
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
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

    public function getWorkEntries(bool $withDeleted = false): Collection
    {

        $criteria = Criteria::create();

        if (!$withDeleted) {
            $criteria->where(Criteria::expr()->isNull('deletedAt'));
        }

        return $this->workEntries->matching($criteria);
    }

    public function addWorkEntry(WorkEntry $workEntry): self
    {
        foreach ($this->workEntries as $entry) {
            if ($entry->getId()->equals($workEntry->getId())) {
                return $this;
            }
        }

        $this->workEntries->add($workEntry);
        return $this;
    }

    public function getUpdatedWorkEntryLogs(): Collection
    {
        return $this->updatedWorkEntryLogs;
    }


}
