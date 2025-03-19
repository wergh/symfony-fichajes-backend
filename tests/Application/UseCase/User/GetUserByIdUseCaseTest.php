<?php

declare(strict_types=1);

namespace App\Tests\Application\UseCase\User;

use App\Application\UseCase\User\GetUserByIdUseCase;
use App\Domain\Shared\Exceptions\EntityNotFoundException;
use App\Domain\Shared\ValueObject\IdGeneratorInterface;
use App\Domain\User\Entity\User;
use App\Domain\User\Repository\UserReadRepositoryInterface;
use App\Domain\User\ValueObject\UserId;
use DG\BypassFinals;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

class GetUserByIdUseCaseTest extends TestCase
{
    private GetUserByIdUseCase $useCase;
    private UserReadRepositoryInterface $userReadRepository;
    private UserId $userId;

    public function testExecuteSuccessfully(): void
    {
        $user = new User($this->userId, 'Test User');

        $this->userReadRepository->expects($this->once())
            ->method('findById')
            ->with((string)$this->userId)
            ->willReturn($user);

        $result = $this->useCase->execute((string)$this->userId);

        $this->assertSame($user, $result);
    }

    public function testExecuteThrowsExceptionWhenUserNotFound(): void
    {

        $this->userReadRepository->expects($this->once())
            ->method('findById')
            ->with((string)$this->userId)
            ->willReturn(null);

        $this->expectException(EntityNotFoundException::class);
        $this->expectExceptionMessage('User not found');

        $this->useCase->execute((string)$this->userId);
    }

    protected function setUp(): void
    {
        BypassFinals::enable();
        $this->userReadRepository = $this->createMock(UserReadRepositoryInterface::class);
        $idGenerator = $this->createMock(IdGeneratorInterface::class);

        $this->useCase = new GetUserByIdUseCase(
            $this->userReadRepository,
        );

        $idGenerator->expects($this->once())
            ->method('generate')
            ->willReturn(Uuid::v4()->toRfc4122());

        $this->userId = UserId::create($idGenerator);
    }
}
