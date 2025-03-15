<?php

declare(strict_types=1);


namespace App\Infrastructure\Validator;

use App\Application\Command\WorkEntry\UpdateWorkEntryCommand;
use App\Application\Validator\WorkEntry\UpdateWorkEntryValidatorInterface;
use App\Infrastructure\Validator\Constraints\DateTimeImmutableType;
use App\Infrastructure\Validator\Constraints\StartDateBeforeEndDate;
use App\Infrastructure\Validator\Constraints\UserExists;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

class UpdateWorkEntryValidator implements UpdateWorkEntryValidatorInterface
{

    public function __construct(private readonly ValidatorInterface $validator) {}

    public function validate(UpdateWorkEntryCommand $command): void
    {
        $constraints = new Assert\Collection([
            'userId' => [new Assert\NotBlank(), new Assert\Uuid(), new UserExists()],
            'startDate' => [new Assert\NotBlank(), new DateTimeImmutableType()],
            'endDate' => [new Assert\NotBlank(), new DateTimeImmutableType()]
        ]);

        $violations = $this->validator->validate([
            'userId' => $command->getUserId(),
            'startDate' => $command->getStartDate()->format(DATE_ATOM),
            'endDate' => $command->getEndDate()->format(DATE_ATOM)
        ], $constraints);

        $violations->addAll($this->validator->validate([
            'startDate' => $command->getStartDate(),
            'endDate' => $command->getEndDate()
        ], [new StartDateBeforeEndDate()]));

        if ($violations->count() > 0) {
            throw new ValidationFailedException($command, $violations);
        }
    }
}
