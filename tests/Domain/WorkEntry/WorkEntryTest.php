<?php

declare(strict_types=1);

namespace App\Tests\Domain\WorkEntry;

use App\Domain\Shared\ValueObject\IdGeneratorInterface;
use App\Domain\User\Entity\User;
use App\Domain\User\ValueObject\UserId;
use App\Domain\WorkEntry\Entity\WorkEntry;
use App\Domain\WorkEntry\ValueObject\WorkEntryId;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

class WorkEntryTest extends TestCase
{
    private User $user;
    private WorkEntryId $workEntryId;
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
        $this->workEntryId =WorkEntryId::create($this->idGenerator);
    }

    public function testWorkEntryConstruction(): void
    {
        $startDate = new DateTimeImmutable('2025-03-19 09:00:00');

        $workEntry = new WorkEntry(
            $this->workEntryId,
            $this->user,
            $startDate,
        );

        $this->assertSame($this->workEntryId, $workEntry->getId());
        $this->assertSame($this->user, $workEntry->getUser());
        $this->assertSame($startDate, $workEntry->getStartDate());
        $this->assertNull($workEntry->getEndDate());
        $this->assertTrue($workEntry->isOpen());
    }

    public function testWorkEntryGetters(): void
    {
        $startDate = new DateTimeImmutable('2025-03-19 09:00:00');

        $workEntry = new WorkEntry(
            $this->workEntryId,
            $this->user,
            $startDate,
        );


        $this->assertSame($this->workEntryId, $workEntry->getId());
        $this->assertSame($this->user, $workEntry->getUser());
        $this->assertSame($startDate, $workEntry->getStartDate());
        $this->assertTrue($workEntry->isOpen());
    }

    public function testCloseWorkEntry(): void
    {
        $startDate = new DateTimeImmutable('2025-03-19 09:00:00');

        $workEntry = new WorkEntry(
            $this->workEntryId,
            $this->user,
            $startDate,
        );

        $this->assertTrue($workEntry->isOpen());

        $workEntry->close();

        $this->assertFalse($workEntry->isOpen());
    }
}
