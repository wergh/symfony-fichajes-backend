<?php

declare(strict_types=1);


namespace App\Application\Validator\WorkEntry;

use App\Application\Command\WorkEntry\CreateWorkEntryCommand;

interface CreateWorkEntryValidatorInterface
{

    public function validate(CreateWorkEntryCommand $command): void;
}
