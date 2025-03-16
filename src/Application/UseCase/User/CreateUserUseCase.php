<?php

declare(strict_types=1);

namespace App\Application\UseCase\User;

use App\Application\Command\User\CreateUserCommand;
use App\Application\Validator\User\CreateUserValidatorInterface;
use App\Domain\Shared\ValueObject\IdGeneratorInterface;
use App\Domain\User\Entity\User;
use App\Domain\User\Repository\UserWriteRepositoryInterface;
use App\Domain\User\ValueObject\UserId;

final class CreateUserUseCase
{
    public function __construct(
        private readonly UserWriteRepositoryInterface $userWriteRepository,
        private readonly CreateUserValidatorInterface $validator,
        private readonly IdGeneratorInterface         $idGenerator
    )
    {
    }

    public function execute(CreateUserCommand $command): User
    {
        $this->validator->validate($command);

        $user = new User(
            id: UserId::create($this->idGenerator),
            name: $command->getName(),
        );

        $this->userWriteRepository->save($user);

        return $user;

    }
}
