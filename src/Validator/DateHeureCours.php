<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class DateHeureCours extends Constraint {
    public $message = 'Le début et la fin du cours doivent être le même jour.';

    public function getTargets() {
        return self::CLASS_CONSTRAINT;
    }
}