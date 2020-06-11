<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class IsColourForCategorie extends Constraint
{
    public $message = 'Cette couleur "{{ string }}" n\'est pas gérer par notre application';

    public function validatedBy()
    {
        return \get_class($this).'Validator';
    }
}