<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class DateHeureCoursValidator extends ConstraintValidator {
    public function validate($cours, Constraint $constraint) {
        $dateHeureDebutCours = $cours->getDateHeureDebut();
        $dateHeureFinCours = $cours->getDateHeureFin();

        if (!$dateHeureDebutCours || !$dateHeureFinCours) {
            return;
        }

        if ($dateHeureDebutCours > $dateHeureFinCours) {
            $this->context->buildViolation("La date de fin du cours doit être ultérieure à la date de début.")
                ->atPath('dateHeureFin')
                ->addViolation();
        }

        if ($dateHeureDebutCours->format('Y-m-d') != $dateHeureFinCours->format('Y-m-d')) {
            $this->context->buildViolation($constraint->message)
                ->atPath('dateHeureFin')
                ->addViolation();
        }

        $dureeCours = date_diff($dateHeureDebutCours, $dateHeureFinCours);

        if ($dureeCours->format('%H') > 4 || ($dureeCours->format('%H') >= 4 && $dureeCours->format('%i') > 30)) {
            $this->context->buildViolation("La durée du cours ne doit pas excéder 4h30.")
                ->atPath('dateHeureFin')
                ->addViolation();
        }

        if ($dureeCours->format('%H') <= 0 && $dureeCours->format('%i') < 15) {
            $this->context->buildViolation("La durée du cours ne doit pas être inférieure à 15mn.")
                ->atPath('dateHeureFin')
                ->addViolation();
        }
    }
}