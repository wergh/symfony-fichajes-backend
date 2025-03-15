<?php

declare(strict_types=1);


namespace App\Domain\User\Exception;

use App\Domain\Shared\Exceptions\DomainException;

class EndDateInTheFutureNotAllowed extends DomainException
{

}
