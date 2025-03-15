<?php

declare(strict_types=1);


namespace App\Application\Validator\WorkEntry;

use App\Application\Command\WorkEntry\UpdateWorkEntryCommand;

interface UpdateWorkEntryValidatorInterface
{

    public function validate(UpdateWorkEntryCommand $command): void;
}
