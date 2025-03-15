<?php

namespace App\Application\Validator\User;

use App\Application\Command\User\CreateUserCommand;

interface CreateUserValidatorInterface
{
    public function validate(CreateUserCommand $command): void;
}
