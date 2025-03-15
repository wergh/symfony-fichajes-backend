<?php

declare(strict_types=1);


namespace App\Infrastructure\Validator;

use App\Application\Command\WorkEntry\CreateWorkEntryCommand;
use App\Application\Validator\WorkEntry\CreateWorkEntryValidatorInterface;
use App\Infrastructure\Validator\Constraints\UserExists;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

class CreateWorkEntryValidator implements CreateWorkEntryValidatorInterface
{

    public function __construct(private readonly ValidatorInterface $validator) {}

    public function validate(CreateWorkEntryCommand $command): void
    {
        $constraints = new Assert\Collection([
            'userId' => [new Assert\NotBlank(), new Assert\Uuid(), new UserExists()]
        ]);

        $violations = $this->validator->validate([
            'userId' => $command->getUserId(),
        ], $constraints);

        if ($violations->count() > 0) {
            throw new ValidationFailedException($command, $violations);
        }
    }
}
