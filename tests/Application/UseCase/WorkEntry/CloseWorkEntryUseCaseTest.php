<?php

declare(strict_types=1);


namespace App\Tests\Application\UseCase\WorkEntry;

use App\Application\UseCase\User\GetUserByIdUseCase;
use App\Application\UseCase\WorkEntry\CloseWorkEntryUseCase;
use App\Domain\Shared\ValueObject\IdGeneratorInterface;
use App\Domain\User\Entity\User;
use App\Domain\User\ValueObject\UserId;
use App\Domain\WorkEntry\Entity\WorkEntry;
use App\Domain\WorkEntry\Exception\NotWorkEntryOpenException;
use App\Domain\WorkEntry\Exception\UnauthorizedAccessToWorkEntry;
use App\Domain\WorkEntry\Repository\WorkEntryReadRepositoryInterface;
use App\Domain\WorkEntry\Repository\WorkEntryWriteRepositoryInterface;
use App\Domain\WorkEntry\Service\WorkEntryDomainService;
use App\Domain\WorkEntry\ValueObject\WorkEntryId;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

class CloseWorkEntryUseCaseTest extends TestCase
{

    private CloseWorkEntryUseCase $useCase;
    private WorkEntryReadRepositoryInterface $workEntryReadRepository;
    private WorkEntryWriteRepositoryInterface $workEntryWriteRepository;
    private WorkEntryDomainService $workEntryDomainService;
    private GetUserByIdUseCase $getUserByIdUseCase;
    private User $user;
    private WorkEntry $workEntry;

    protected function setUp(): void
    {
        \DG\BypassFinals::enable();
        $this->workEntryReadRepository = $this->createMock(WorkEntryReadRepositoryInterface::class);
        $this->workEntryWriteRepository = $this->createMock(WorkEntryWriteRepositoryInterface::class);
        $this->workEntryDomainService = $this->createMock(WorkEntryDomainService::class);
        $this->getUserByIdUseCase = $this->createMock(GetUserByIdUseCase::class);
        $idGenerator = $this->createMock(IdGeneratorInterface::class);

        $this->useCase = new CloseWorkEntryUseCase(
            $this->getUserByIdUseCase,
            $this->workEntryReadRepository,
            $this->workEntryWriteRepository,
            $this->workEntryDomainService,
        );

        $matcher = $this->exactly(2);
        $idGenerator->expects($matcher)
            ->method('generate')
            ->willReturnCallback(function () use ($matcher) {
                return Uuid::v4()->toRfc4122();
            });

        $this->user = new User(UserId::create($idGenerator), 'Test User');

        $startDate = new \DateTimeImmutable();
        $this->workEntry = new WorkEntry(
            WorkEntryId::create($idGenerator),
            $this->user,
            $startDate,
        );
    }

    public function testExecuteSuccessfully(): void
    {
        $this->getUserByIdUseCase->expects($this->once())
            ->method('execute')
            ->with((string) $this->user->getId())
            ->willReturn($this->user);

        $this->workEntryReadRepository->expects($this->once())
            ->method('findOpenWorkEntryByUserId')
            ->with((string) $this->user->getId())
            ->willReturn($this->workEntry);
        $this->workEntryWriteRepository->expects($this->once())
            ->method('save')
            ->with($this->workEntry);

        $this->useCase->execute((string) $this->user->getId());

        $this->assertFalse($this->workEntry->isOpen());

    }

    public function testNotWorkEntryOpenException()
    {
        $this->getUserByIdUseCase->expects($this->once())
            ->method('execute')
            ->with((string) $this->user->getId())
            ->willReturn($this->user);

        $this->workEntryReadRepository->expects($this->once())
            ->method('findOpenWorkEntryByUserId')
            ->with((string) $this->user->getId())
            ->willReturn(null);

        $this->expectException(NotWorkEntryOpenException::class);
        $this->expectExceptionMessage('No work entry open');

        $this->useCase->execute((string) $this->user->getId());

    }


}
