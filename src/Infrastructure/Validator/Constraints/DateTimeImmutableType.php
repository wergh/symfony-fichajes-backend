<?php

declare(strict_types=1);

namespace App\Infrastructure\Validator\Constraints;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class DateTimeImmutableType extends Constraint
{
    public string $message = 'The value "{{ value }}" is not a valid instance of DateTimeImmutable.';

    public function __construct(string $message = null, array $groups = null, mixed $payload = null)
    {
        parent::__construct([], $groups, $payload);
        if ($message) {
            $this->message = $message;
        }
    }
}
