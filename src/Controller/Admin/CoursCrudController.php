<?php

namespace App\Controller\Admin;

use App\Entity\Cours;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;

class CoursCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Cours::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            'dateHeureDebut',
            'dateHeureFin',
            ChoiceField::new('type')
                ->setChoices(fn() => ["Cours" => "Cours", "TD" => "TD", "TP" => "TP"])
                ->renderAsNativeWidget(),
            AssociationField::new('matiere'),
            AssociationField::new('professeur'),
            AssociationField::new('salle'),
        ];
    }
    
}
