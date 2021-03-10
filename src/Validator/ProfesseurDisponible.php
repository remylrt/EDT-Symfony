<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class ProfesseurDisponible extends Constraint {
    public $message = 'Ce professeur dispense déjà un cours sur ce créneau horaire.';

    public function getTargets() {
        return self::CLASS_CONSTRAINT;
    }
}