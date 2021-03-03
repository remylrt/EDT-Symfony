<?php

namespace App\Entity;

trait Updatable {
    public function updateFromArray(array $data) {
        $errors = [];

        foreach ($data as $attribute => $value) {
            if (property_exists($this, $attribute)) {
                $this->{$attribute} = $value;
            } else {
                $errors[] = $attribute;
            }
        }

        return $errors;
    }
}