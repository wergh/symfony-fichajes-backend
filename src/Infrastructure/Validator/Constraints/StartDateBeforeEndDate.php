<?php

declare(strict_types=1);

namespace App\Infrastructure\Validator\Constraints;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class StartDateBeforeEndDate extends Constraint
{
    public string $message = 'The start date "{{ startDate }}" must be before the end date "{{ endDate }}".';

    public function __construct(string $message = null, array $groups = null, mixed $payload = null)
    {
        parent::__construct([], $groups, $payload);
        if ($message) {
            $this->message = $message;
        }
    }
}
