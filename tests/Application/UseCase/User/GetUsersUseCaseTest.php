<?php

declare(strict_types=1);

namespace App\Tests\Application\UseCase\User;

use App\Application\Dto\User\UserDto;
use App\Application\Dto\User\UserListDto;
use App\Application\Mapper\User\UserListDtoMapper;
use App\Application\UseCase\User\GetUsersUseCase;
use App\Domain\Shared\ValueObject\IdGeneratorInterface;
use App\Domain\User\Entity\User;
use App\Domain\User\Repository\UserReadRepositoryInterface;
use App\Domain\User\ValueObject\UserId;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

class GetUsersUseCaseTest extends TestCase
{
    private GetUsersUseCase $useCase;
    private UserReadRepositoryInterface $userReadRepository;
    private IdGeneratorInterface $idGenerator;

    protected function setUp(): void
    {
        $this->userReadRepository = $this->createMock(UserReadRepositoryInterface::class);
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

        $this->useCase = new GetUsersUseCase(
            $this->userReadRepository,
        );
    }

    public function testExecuteSuccessfully(): void
    {
        $user1 = new User(UserId::create($this->idGenerator), 'Test User 1');
        $user2 = new User(UserId::create($this->idGenerator), 'Test User 2');
        $users = [$user1, $user2];


        $this->userReadRepository->expects($this->once())
            ->method('all')
            ->willReturn($users);

        $result = $this->useCase->execute();

        $this->assertSame($users, $result);
    }
}
