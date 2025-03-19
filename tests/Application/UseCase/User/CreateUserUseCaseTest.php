<?php

declare(strict_types=1);

namespace App\Tests\Application\UseCase\User;

use App\Application\Command\User\CreateUserCommand;
use App\Application\UseCase\User\CreateUserUseCase;
use App\Application\Validator\User\CreateUserValidatorInterface;
use App\Domain\Shared\ValueObject\IdGeneratorInterface;
use App\Domain\User\Entity\User;
use App\Domain\User\Repository\UserWriteRepositoryInterface;
use App\Domain\User\ValueObject\UserId;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

class CreateUserUseCaseTest extends TestCase
{
    private CreateUserUseCase $useCase;
    private CreateUserValidatorInterface $validator;
    private IdGeneratorInterface $idGenerator;
    private UserWriteRepositoryInterface $userWriteRepository;

    protected function setUp(): void
    {
        $this->validator = $this->createMock(CreateUserValidatorInterface::class);
        $this->idGenerator = $this->createMock(IdGeneratorInterface::class);
        $this->userWriteRepository = $this->createMock(UserWriteRepositoryInterface::class);

        $this->useCase = new CreateUserUseCase(
            $this->userWriteRepository,
            $this->validator,
            $this->idGenerator,
        );

    }

    public function testExecuteSuccessfully(): void
    {
        $command = new CreateUserCommand('Test User');

        $this->validator->expects($this->once())
            ->method('validate')
            ->with($command);

        $userId = Uuid::v4()->toRfc4122();
        $this->idGenerator->expects($this->once())
            ->method('generate')
            ->willReturn($userId);

        $this->userWriteRepository->expects($this->once())
            ->method('save')
            ->with($this->callback(function (User $user) use ($userId) {
                return (string) $user->getId() === $userId
                    && $user->getName() === 'Test User';
            }));

        $result = $this->useCase->execute($command);

        $this->assertSame($userId, (string) $result->getId());
    }
}
