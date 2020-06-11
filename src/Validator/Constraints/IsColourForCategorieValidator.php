<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class IsColourForCategorieValidator extends ConstraintValidator
{
     private $handledColors = [
         "red",
         "darkred",
         "orange",
         "green",
         "darkgreen",
         "blue",
         "purple",
         "darkpurple",
         "cadetblue",
         "darkblue",
     ];

    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof IsColourForCategorie) {
            throw new UnexpectedTypeException($constraint, IsColourForCategorie::class);
        }

        // custom constraints should ignore null and empty values to allow
        // other constraints (NotBlank, NotNull, etc.) take care of that
        if (null === $value || '' === $value) {
            return;
        }

        if (!is_string($value)) {
            // throw this exception if your validator cannot handle the passed type so that it can be marked as invalid
            throw new UnexpectedValueException($value, 'string');

            // separate multiple types using pipes
            // throw new UnexpectedValueException($value, 'string|int');
        }

        if (!in_array($value,$this->handledColors,false)) {
            // the argument must be a string or an object implementing __toString()
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ string }}',$value)
                ->addViolation();
        }
    }
}