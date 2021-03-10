<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class SalleDisponibleValidator extends ConstraintValidator {
    public function validate($nouveauCours, Constraint $constraint) {
        $coursSalle = $nouveauCours->getSalle()->getCours();

        foreach($coursSalle as $cours){
            if(!(($nouveauCours->getDateHeureDebut() <= $cours->getDateHeureDebut() && $nouveauCours->getDateHeureFin() <= $cours->getDateHeureDebut()) ||  ($nouveauCours->getDateHeureDebut() >= $cours->getDateHeureFin() && $nouveauCours->getDateHeureFin() >= $cours->getDateHeureFin())) && $nouveauCours !== $cours) {
                $this->context->buildViolation($constraint->message)
                    ->atPath('salle')
                    ->addViolation();
                return;
            }
        }
    }
}