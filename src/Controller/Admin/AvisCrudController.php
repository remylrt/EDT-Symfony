<?php

namespace App\Controller\Admin;

use App\Entity\Avis;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;

class AvisCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Avis::class;
    }
    
    public function configureFields(string $pageName): iterable
    {
        return [
            ChoiceField::new('note')
                ->setChoices(fn() => [1 => 1, 2 => 1, 3 => 3, 4 => 4, 5 => 4])
                ->renderAsNativeWidget(),
            'commentaire',
            'emailEtudiant',
            AssociationField::new('professeur')
        ];
    }
}


