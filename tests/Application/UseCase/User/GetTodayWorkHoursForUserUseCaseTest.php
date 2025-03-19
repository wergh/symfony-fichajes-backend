<?php

declare(strict_types=1);

namespace App\Tests\Application\UseCase\User;

use App\Application\UseCase\User\GetTodayWorkHoursForUserUseCase;
use App\Domain\Shared\Exceptions\EntityNotFoundException;
use App\Domain\Shared\ValueObject\IdGeneratorInterface;
use App\Domain\User\Entity\User;
use App\Domain\User\Repository\UserReadRepositoryInterface;
use App\Domain\User\ValueObject\UserId;
use App\Domain\WorkEntry\Entity\WorkEntry;
use App\Domain\WorkEntry\Repository\WorkEntryReadRepositoryInterface;
use App\Domain\WorkEntry\ValueObject\WorkEntryId;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

class GetTodayWorkHoursForUserUseCaseTest extends TestCase
{
    private GetTodayWorkHoursForUserUseCase $useCase;
    private UserReadRepositoryInterface $userReadRepository;
    private WorkEntryReadRepositoryInterface $workEntryReadRepository;
    private UserId $userId;

    private IdGeneratorInterface $idGenerator;

    protected function setUp(): void
    {
        $this->userReadRepository = $this->createMock(UserReadRepositoryInterface::class);
        $this->workEntryReadRepository = $this->createMock(WorkEntryReadRepositoryInterface::class);

        $this->idGenerator = $this->createMock(IdGeneratorInterface::class);
        $this->useCase = new GetTodayWorkHoursForUserUseCase(
            $this->workEntryReadRepository
        );

        $matcher = $this->exactly(3);
        $this->idGenerator->expects($matcher)
            ->method('generate')
            ->willReturnCallback(function () use ($matcher) {
                return Uuid::v4()->toRfc4122();
            });

        $this->userId = UserId::create($this->idGenerator);
    }

    public function testExecuteSuccessfully(): void
    {
        $user = new User($this->userId,'Test User');

        $startDate = (new DateTimeImmutable())->modify('-1 hour');
        $workEntry = new WorkEntry(
            WorkEntryId::create($this->idGenerator),
            $user,
            $startDate,
        );

        $endDate = $workEntry->close()->getEndDate();

        $startDate2 = (new DateTimeImmutable())->modify('-3 hour');
        $workEntry2 = new WorkEntry(
            WorkEntryId::create($this->idGenerator),
            $user,
            $startDate2,
        );

        $endDate2 = $workEntry2->close()->getEndDate();

        $this->workEntryReadRepository->expects($this->once())
            ->method('getWorkEntriesForToday')
            ->with($this->userId)
            ->willReturn([$workEntry, $workEntry2]);

        $result = $this->useCase->execute($user);

        $totalSeconds = ($endDate->getTimestamp() - $startDate->getTimestamp()) + ($endDate2->getTimestamp() - $startDate2->getTimestamp());

        $this->assertSame($totalSeconds, $result);
    }

}
