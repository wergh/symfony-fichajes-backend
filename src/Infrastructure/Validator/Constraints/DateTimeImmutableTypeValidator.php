<?php

declare(strict_types=1);

namespace App\Infrastructure\Validator\Constraints;

use DateTimeInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use DateTimeImmutable;

class DateTimeImmutableTypeValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof DateTimeImmutableType) {
            throw new UnexpectedTypeException($constraint, DateTimeImmutableType::class);
        }

        if (null === $value || $value === '') {
            return;
        }

        if (!is_string($value)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', (string) $value)
                ->addViolation();
            return;
        }

        try {
            $date = DateTimeImmutable::createFromFormat(DateTimeInterface::ATOM, $value) ?: new DateTimeImmutable($value);
        } catch (\Exception $e) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value)
                ->addViolation();
            return;
        }

        if (!$date) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value)
                ->addViolation();
        }
    }
}
