<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class SalleDisponible extends Constraint {
    public $message = 'Un cours est déjà programmé dans cette salle sur ce créneau horaire.';

    public function getTargets() {
        return self::CLASS_CONSTRAINT;
    }
}