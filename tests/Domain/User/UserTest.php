<?php

declare(strict_types=1);

namespace App\Tests\Domain\User;

use App\Domain\Shared\ValueObject\IdGeneratorInterface;
use App\Domain\User\Entity\User;
use App\Domain\User\ValueObject\UserId;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

class UserTest extends TestCase
{

    private IdGeneratorInterface $idGenerator;
    protected function setUp(): void
    {
        $this->idGenerator = $this->createMock(IdGeneratorInterface::class);

    }
    public function testUserConstruction(): void
    {

        $this->idGenerator->expects($this->once())
            ->method('generate')
            ->willReturn(Uuid::v4()->toRfc4122());

        $userId = UserId::create($this->idGenerator);
        $name = 'Test User';

        $user = new User($userId, $name);

        $this->assertSame($userId, $user->getId());
        $this->assertSame($name, $user->getName());
    }

    public function testUserGetters(): void
    {
        $this->idGenerator->expects($this->once())
            ->method('generate')
            ->willReturn(Uuid::v4()->toRfc4122());

        $userId = UserId::create($this->idGenerator);
        $name = 'Test User';

        $user = new User($userId, $name);

        $this->assertSame($userId, $user->getId());
        $this->assertSame($name, $user->getName());
        $this->assertInstanceOf(UserId::class, $user->getId());
    }
}
