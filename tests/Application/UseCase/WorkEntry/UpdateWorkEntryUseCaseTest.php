<?php

declare(strict_types=1);


namespace App\Tests\Application\UseCase\WorkEntry;

use App\Application\Command\WorkEntry\UpdateWorkEntryCommand;
use App\Application\UseCase\WorkEntry\GetWorkEntryByIdUseCase;
use App\Application\UseCase\WorkEntry\UpdateWorkEntryUseCase;
use App\Application\Validator\WorkEntry\UpdateWorkEntryValidatorInterface;
use App\Domain\Shared\ValueObject\IdGeneratorInterface;
use App\Domain\User\Entity\User;
use App\Domain\User\Exception\EndDateInTheFutureNotAllowed;
use App\Domain\User\ValueObject\UserId;
use App\Domain\WorkEntry\Entity\WorkEntry;
use App\Domain\WorkEntry\Exception\NotOverlapException;
use App\Domain\WorkEntry\Exception\WorkEntryIsAlreadyOpenException;
use App\Domain\WorkEntry\Repository\WorkEntryReadRepositoryInterface;
use App\Domain\WorkEntry\Repository\WorkEntryWriteRepositoryInterface;
use App\Domain\WorkEntry\ValueObject\WorkEntryId;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Exception\ValidationFailedException;

class UpdateWorkEntryUseCaseTest extends TestCase
{
    private UpdateWorkEntryUseCase $useCase;
    private GetWorkEntryByIdUseCase $getWorkEntryByIdUseCase;
    private WorkEntryWriteRepositoryInterface $workEntryWriteRepository;
    private WorkEntryReadRepositoryInterface $workEntryReadRepository;
    private UpdateWorkEntryValidatorInterface $validator;
    private MessageBusInterface $eventBus;
    private UserId $userId;
    private WorkEntryId $workEntryId;

    protected function setUp(): void
    {
        \DG\BypassFinals::enable();
        $this->getWorkEntryByIdUseCase = $this->createMock(GetWorkEntryByIdUseCase::class);
        $this->workEntryWriteRepository = $this->createMock(WorkEntryWriteRepositoryInterface::class);
        $this->workEntryReadRepository = $this->createMock(WorkEntryReadRepositoryInterface::class);
        $this->validator = $this->createMock(UpdateWorkEntryValidatorInterface::class);
        $this->eventBus = $this->createMock(MessageBusInterface::class);

        $idGenerator = $this->createMock(IdGeneratorInterface::class);

        $matcher = $this->exactly(2);
        $idGenerator->expects($matcher)
            ->method('generate')
            ->willReturnCallback(function () use ($matcher) {
                return Uuid::v4()->toRfc4122();
            });

        $this->userId = UserId::create($idGenerator);
        $this->workEntryId = WorkEntryId::create($idGenerator);

        $this->useCase = new UpdateWorkEntryUseCase(
            $this->getWorkEntryByIdUseCase,
            $this->workEntryWriteRepository,
            $this->workEntryReadRepository,
            $this->validator,
            $this->eventBus
        );

    }

    public function testValidationFailedException()
    {
        $workEntryFailedCommand = new UpdateWorkEntryCommand(
            'user-id-not-found',
            (string) $this->workEntryId,
            (new \DateTimeImmutable())->modify('-2 hour'),
            new \DateTimeImmutable()
        );

        $violations = $this->createMock(ConstraintViolationList::class);

        $this->validator->expects($this->once())
            ->method('validate')
            ->with($workEntryFailedCommand)
            ->willThrowException(new ValidationFailedException($workEntryFailedCommand,$violations));

        $this->expectException(ValidationFailedException::class);

        $this->useCase->execute($workEntryFailedCommand);
    }

    public function testWorkEntryIsAlreadyOpenException()
    {
        $user = new User($this->userId, 'Test user');
        $startTime = new \DateTimeImmutable();
        $workEntry  = new WorkEntry(
            $this->workEntryId,
            $user,
            $startTime
        );
        $this->getWorkEntryByIdUseCase->expects($this->once())
            ->method('execute')
            ->with((string) $this->userId, (string) $this->workEntryId)
            ->willReturn($workEntry);
        $this->expectException(WorkEntryIsAlreadyOpenException::class);
        $this->expectExceptionMessage('Work entry is already open');
        $workEntryCommand = new UpdateWorkEntryCommand(
            (string) $this->userId,
            (string) $this->workEntryId,
            $startTime->modify('-2 hour'),
            new \DateTimeImmutable(),
        );
        $this->useCase->execute($workEntryCommand);

    }

    public function testEndDateInTheFutureNotAllowed()
    {
        $user = new User($this->userId, 'Test user');
        $startTime = new \DateTimeImmutable();
        $workEntry  = new WorkEntry(
            $this->workEntryId,
            $user,
            $startTime
        );
        $workEntry->close();
        $this->getWorkEntryByIdUseCase->expects($this->once())
            ->method('execute')
            ->with((string) $this->userId, (string) $this->workEntryId)
            ->willReturn($workEntry);

        $workEntryCommand = new UpdateWorkEntryCommand(
            (string) $this->userId,
            (string) $this->workEntryId,
            $startTime,
            (new \DateTimeImmutable())->modify('+3 hour')
        );

        $this->expectException(EndDateInTheFutureNotAllowed::class);
        $this->expectExceptionMessage('End date cannot be in the future.');
        $this->useCase->execute($workEntryCommand);

    }

    public function testNotOverlapException()
    {
        $user = new User($this->userId, 'Test user');
        $idGenerator = $this->createMock(IdGeneratorInterface::class);
        $idGenerator->expects($this->once())
            ->method('generate')
            ->willReturn(
                Uuid::v4()->toRfc4122()
            );
        $workEntryId = WorkEntryId::create($idGenerator);

        $workEntryPrevious  = new WorkEntry(
            $workEntryId,
            $user,
            (new \DateTimeImmutable())->modify('-2 hours')
        );
        $workEntryPrevious->close();
        $workEntryPrevious->setEndDate((new \DateTimeImmutable())->modify('-1 hours'));
        $startTime = new \DateTimeImmutable();
        $workEntry  = new WorkEntry(
            $this->workEntryId,
            $user,
            $startTime
        );
        $workEntry->close();
        $this->getWorkEntryByIdUseCase->expects($this->once())
            ->method('execute')
            ->with((string) $this->userId, (string) $this->workEntryId)
            ->willReturn($workEntry);

        $this->workEntryReadRepository->expects($this->once())
            ->method('findPreviousWorkEntry')
            ->with((string) $this->userId, $startTime)
            ->willReturn($workEntryPrevious);

        $workEntryCommand = new UpdateWorkEntryCommand(
            (string) $this->userId,
            (string) $this->workEntryId,
            $startTime->modify('-2 hour'),
            $workEntry->getEndDate(),
        );

        $this->expectException(NotOverlapException::class);
        $this->expectExceptionMessage('Start date cannot be before the end date of the previous entry.');
        $this->useCase->execute($workEntryCommand);

    }

    public function testExecuteSuccessfully()
    {
        $user = new User($this->userId, 'Test user');
        $startTime = new \DateTimeImmutable();
        $workEntry  = new WorkEntry(
            $this->workEntryId,
            $user,
            $startTime
        );
        $workEntry->close();
        $this->getWorkEntryByIdUseCase->expects($this->once())
            ->method('execute')
            ->with((string) $this->userId, (string) $this->workEntryId)
            ->willReturn($workEntry);
        $workEntryCommand = new UpdateWorkEntryCommand(
            (string) $this->userId,
            (string) $this->workEntryId,
            $startTime->modify('-2 hour'),
            $workEntry->getEndDate(),
        );

        $this->useCase->execute($workEntryCommand);

        $this->assertEquals($workEntry->getStartDate(), $startTime->modify('-2 hour'));

    }
}
