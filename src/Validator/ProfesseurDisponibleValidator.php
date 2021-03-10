<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class ProfesseurDisponibleValidator extends ConstraintValidator {
    public function validate($nouveauCours, Constraint $constraint) {
        $coursProfesseur = $nouveauCours->getProfesseur()->getCours();

        foreach($coursProfesseur as $cours) {
            if(!(($nouveauCours->getDateHeureDebut() <= $cours->getDateHeureDebut() && $nouveauCours->getDateHeureFin() <= $cours->getDateHeureDebut()) || ($nouveauCours->getDateHeureDebut() >= $cours->getDateHeureFin() && $nouveauCours->getDateHeureFin() >= $cours->getDateHeureFin())) && $nouveauCours !== $cours) {
                $this->context->buildViolation($constraint->message)
                    ->atPath('professeur')
                    ->addViolation();
                return;
            }
        }
    }
}