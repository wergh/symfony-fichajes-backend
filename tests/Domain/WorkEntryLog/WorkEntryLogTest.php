<?php

declare(strict_types=1);


namespace App\Tests\Domain\WorkEntryLog;

use App\Domain\Shared\ValueObject\IdGeneratorInterface;
use App\Domain\User\Entity\User;
use App\Domain\User\ValueObject\UserId;
use App\Domain\WorkEntry\Entity\WorkEntry;
use App\Domain\WorkEntry\Entity\WorkEntryLog;
use App\Domain\WorkEntry\ValueObject\WorkEntryId;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

class WorkEntryLogTest extends TestCase
{
    private User $user;
    private WorkEntry $workEntry;
    private IdGeneratorInterface $idGenerator;

    protected function setUp(): void
    {
        $this->idGenerator = $this->createMock(IdGeneratorInterface::class);

        $matcher = $this->exactly(2);
        $this->idGenerator->expects($matcher)
            ->method('generate')
            ->willReturnCallback(function () use ($matcher) {
                if ($matcher->getInvocationCount() === 1) {
                    return Uuid::v4()->toRfc4122();
                }
                if ($matcher->getInvocationCount() === 2) {
                    return Uuid::v4()->toRfc4122();
                }
            });

        $this->user = new User(UserId::create($this->idGenerator), 'Test User');
        $workEntryId = WorkEntryId::create($this->idGenerator);
        $startDate = new DateTimeImmutable('2025-03-19 09:00:00');

        $this->workEntry = new WorkEntry(
            $workEntryId,
            $this->user,
            $startDate,
        );
    }

    public function testWorkEntryLogConstruction(): void
    {
        $previousStartDate = new DateTimeImmutable('2025-03-19 08:00:00');
        $previousEndDate = new DateTimeImmutable('2025-03-19 16:00:00');

        $workEntryLog = new WorkEntryLog(
            $this->user,
            $previousStartDate,
            $previousEndDate,
            $this->workEntry
        );

        $this->assertSame($this->workEntry, $workEntryLog->getWorkEntry());

    }

    public function testWorkEntryLogGetters(): void
    {
        $previousStartDate = new DateTimeImmutable('2025-03-19 08:00:00');
        $previousEndDate = new DateTimeImmutable('2025-03-19 16:00:00');

        $workEntryLog = new WorkEntryLog(
            $this->user,
            $previousStartDate,
            $previousEndDate,
            $this->workEntry
        );
        $this->assertSame($this->user, $workEntryLog->getUser());
        $this->assertSame($this->workEntry, $workEntryLog->getWorkEntry());
        $this->assertSame($previousStartDate, $workEntryLog->getStartTime());
        $this->assertSame($previousEndDate, $workEntryLog->getEndTime());
    }
}
