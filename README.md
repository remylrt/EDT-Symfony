# Rapport de projet EDT Symfony

## Introduction
Ce projet consistait en la réalisation d'une application web permettant aux étudiants de visualiser leurs emplois du temps quotidiens, en utilisant le framework PHP Symfony dans le cadre du module Programmation web avancée de la LP Programmation avancée.

Notre binôme était composé de Nicolas LOPES et de Rémy LARTIGUELONGUE.

Ce rapport sera séparé en deux grandes parties. L'une où nous détaillerons notre travail pour réaliser la partie obligatoire du sujet, commune à tous les groupes et l'autre où nous aborderons les améliorations que nous avons pris l'initiative d'implémenter dans l'application.

## Partie commune
### Extension du modèle de données
Voici à quoi ressemblait notre modèle de données au début du projet :

![Modèle de données existant](/readmeAssets/img/ModeleDonneesAvant.PNG)

Il nous était demandé, avant de se lancer dans le développement de l'application, d'étendre ce modèle en y rajoutant des entités **Cours** et **Salle** comme ci-dessous :

![Modèle de données étendu](/readmeAssets/img/ModeleDonneesApres.PNG)

Nous nous sommes donc servi de la console Symfony pour générer ces nouvelles entités avec la commande `bin/console make:entity`.

Pour l'entité **Cours**, nous avons fait le choix d'implémenter *dateHeureFin* et *dateHeureDebut* en type `DateTime` et le *type* en `String` de taille 255.

Dans l'entité **Salle**, le *numero* a également été implémenté en `String` de taille 255.

En ce qui concerne les relations, nous avons choisi une ManyToOne entre **Cours** et **Matière**, entre **Cours** et **Professeur** et entre **Cours** et **Salle** car :
- Un **Cours** ne peut concerner qu'une **Matière** et une **Matière** peut faire référence à plusieurs **Cours**;
- Un **Cours** ne peut être assuré que par un **Professeur** et un **Professeur** peut assurer plusieurs **Cours**;
- Un **Cours** ne peut se dérouler que dans une **Salle** et une **Salle** peut accueillir plusieurs **Cours**.

Voici le résultat obtenu dans *Cours.php* :
```php
/**
 * @ORM\Entity(repositoryClass=CoursRepository::class)
 */
class Cours {
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateHeureDebut;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateHeureFin;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $type;

    /**
     * @ORM\ManyToOne(targetEntity=Matiere::class, inversedBy="cours")
     * @ORM\JoinColumn(nullable=false)
     */
    private $matiere;

    /**
     * @ORM\ManyToOne(targetEntity=Professeur::class, inversedBy="cours")
     * @ORM\JoinColumn(nullable=false)
     */
    private $professeur;

    /**
     * @ORM\ManyToOne(targetEntity=Salle::class, inversedBy="cours")
     * @ORM\JoinColumn(nullable=false)
     */
    private $salle;

    ...
}
```

Et le résultat dans *Salle.php* :
```php
/**
 * @ORM\Entity(repositoryClass=SalleRepository::class)
 */
class Salle {
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $numero;

    /**
     * @ORM\OneToMany(targetEntity=Cours::class, mappedBy="salle")
     */
    private $cours;

    ...
}
```

Il nous restait alors qu'à mettre à jour le schéma de la base de données en conséquence avec la commande `bin/console doctrine:schema:update --force`.

### Interface d'administration
L'étape suivante était de mettra à jour l'interface d'administration Easy Admin pour y ajouter la gestion de nos entités **Cours** et **Salle** nouvellement créées.

Avec la commande `bin/console make:admin:crud` nous avons pu créer les contrôleurs pour le CRUD de nos entités dans Easy Admin.

Cependant, elles n'apparaissent pas encore dans le menu du dashboard. Pour corriger cela, nous avons modifié la méthode `configureMenuItems()` du *DashboardController.php* pour y rajouter les liens comme cela :
```php
public function configureMenuItems(): iterable {
    return [
        MenuItem::linktoDashboard('Dashboard', 'fa fa-home'),
        MenuItem::linkToCrud('Professeurs', 'fas fa-user', Professeur::class),
        MenuItem::linkToCrud('Matières', 'fas fa-chalkboard-teacher', Matiere::class),
        MenuItem::linkToCrud('Avis', 'fa fa-star', Avis::class),
        MenuItem::linkToCrud('Cours', 'fa fa-book-open', Cours::class),
        MenuItem::linkToCrud('Salle', 'fas fa-school', Salle::class),
     ;
}
```
Voici le menu après cette modification :

![Menu Easy Admin](/readmeAssets/img/MenuEasyAdmin.PNG)

Et voici à quoi ressemblent les formulaires de création de **Cours** :

![Formulaire de création de cours](/readmeAssets/img/CreateCoursAvant.PNG)

Et de création de **Salle** :

![Formulaire de création de salle](/readmeAssets/img/CreateSalle.PNG)

Le formulaire de création de **Salle** nous convient mais nous souhaiterions pouvoir sélectionner une **Matiere**, un **Professeur** et une **Salle** dans le formulaire de création de **Cours** et éventuellement de restreindre le type de **Cours** à des valeurs prédéfinies.

Pour cela, nous avons modifié le contrôleur *CoursCrudController.php* pour y ajouter des champs permettant de saisir la **Matiere**, le **Professeur** et la **Salle** ainsi que pour mettre un champ de saisie de type `ChoiceField` sur le type de **Cours** avec les valeurs *Cours*, *TD* et *TP*.

```php
public function configureFields(string $pageName): iterable {
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
```

Le formulaire de création de **Cours** après ces modifications :

![Formulaire de création de cours après modifications](/readmeAssets/img/CreateCoursApres.PNG)

Une fois le formulaires corrects, nous devions mettre en place des validateurs pour contrôler les données qui seraient saisies.

Nous avons donc commencé par mettre des validateurs pour vérifier qu'aucun des champs ne soient vides :

Dans *Cours.php* :
```php
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=CoursRepository::class)
 */
class Cours {
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\NotBlank(
     *      message = "Veuillez renseigner la date de début."
     * )
     */
    private $dateHeureDebut;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\NotBlank(
     *      message = "Veuillez renseigner la date de fin."
     * )
     */
    private $dateHeureFin;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(
     *      message = "Veuillez renseigner le type."
     * )
     */
    private $type;

    /**
     * @ORM\ManyToOne(targetEntity=Matiere::class, inversedBy="cours")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank(
     *      message = "Veuillez renseigner la matière."
     * )
     */
    private $matiere;

    /**
     * @ORM\ManyToOne(targetEntity=Professeur::class, inversedBy="cours")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank(
     *      message = "Veuillez renseigner le professeur."
     * )
     */
    private $professeur;

    /**
     * @ORM\ManyToOne(targetEntity=Salle::class, inversedBy="cours")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank(
     *      message = "Veuillez renseigner la salle."
     * )
     */
    private $salle;

    ...
}
```

Dans *Salle.php* :
```php
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=SalleRepository::class)
 */
class Salle {
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(
     *      message = "Veuillez renseigner le numéro."
     * )
     */
    private $numero;

    /**
     * @ORM\OneToMany(targetEntity=Cours::class, mappedBy="salle")
     */
    private $cours;

    ...
}
```

Nous avons également ajouté un validateur pour que le type de **Cours** soit bien *Cours*, *TD* ou *TP* :

```php
/**
 * @ORM\Column(type="string", length=255)
 * @Assert\NotBlank(
 *      message = "Veuillez renseigner le type."
 * )
 * @Assert\Choice(
 *      {"Cours", "TD", "TP"},
 *      message = "Le type est invalide."
 * )
 */
private $type;
```

Aussi, nous avons mis en place un validateur `UniqueEntity` sur l'entité **Salle** pour ne pas que l'on puisse avoir deux salles ayant le même numéro :
```php
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=SalleRepository::class)
 * @UniqueEntity(
 *      fields={"numero"},
 *      message="Cette salle existe déjà."
 * )
 */
class Salle {
    ...
}
```

En plus de ces validateurs, nous avons pensé à des cas qui, selon nous, nécessitaient que l'on en mette en place d'autres :
- Vérifier que la date de début soit ultérieure à la date de fin du **Cours**;
- Vérifier que la date de début et la date de fin du **Cours** soient le même jour;
- Vérifier que le **Cours** fasse au minimum 15 minutes et au maximum 4 heures 30;
- Vérifier que le **Professeur** sélectionné pour un **Cours** ne soit pas déjà affecté à un **Cours** au mêmes horaires;
- Vérifier que la **Salle** sélectionnée pour un **Cours** ne soit pas déjà affectée à un **Cours** au mêmes horaires.

Ces cas ont nécessité que nous méttions en place des validateurs personnalisés. Pour cela nous nous sommes appuyés sur la documentation de Symfony à ce propos accessible [ici](https://symfony.com/doc/current/validation/custom_constraint.html).

Elle nous explique que le doit créer un dossier *Validator* dans *src* comme cela :

![Dossier Validator](/readmeAssets/img/ValidatorFolder.PNG)

À l'intérieur, on créé deux fichiers PHP : l'un avec nom libre et l'autre avec le même nom que le premier suivi de "Validator". Ici nous avons *DateHeureCours.php* et *DateHeureCoursValidator.php*.

Dans *DateHeureCours.php* nous avons :
```php
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
```

Cette classe doit étendre de `Symfony\Component\Validator\Constraint` et contenir un attribut *message* qui sera le message d'erreur affiché dans le formulaire lorsque la validation n'est pas passée.

À côté de ça, dans *DateHeureCoursValidator.php* nous avons :
```php
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
```

Cette classe doit étendre de `Symfony\Component\Validator\ConstraintValidator` et doit implémenter une méthode `validate()` qui sera exécutée à chaque fois que l'on soumettra le formulaire. Elle prend en paramètre l'entité que l'on souhaite créer ou modifier donc nous pouvons effectuer dessus des vérifications à notre guise.
Ici nous vérifions :
- Que les dates de début et de fin du **Cours** soient définis, sinon on sort de la méthode et on laisse la contrainte `NotBlank` gérer ce cas;
- Que la date de fin du **Cours** soit ultérieure à la date de début;
- Que les dates de début et de fin du **Cours** soient le même jour;
- Que les dates de début et de fin du **Cours** soient espacés d'au moins 15 minutes;
- Que les dates de début et de fin du **Cours** ne soient pas espacés de plus de 4 heures 30.

Comme on le voit, pour générer l'erreur on récupère le contexte dans lequel le validateur est exécuté, on lui indique le message d'erreur souhaité, le champ sur lequel on souhaite voir apparaître le message et on l'ajoute à la liste des violations. Notre méthode `validate()` peut donc retourner plusieurs erreurs de saisie en une seule fois.

Les deux autres validateurs, qui vérifient si le professeur et la salle sélectionnés sont disponibles, étant grandement similaires, nous avons fait le choix de ne pas les détailler ici.

Une fois nos validateurs faits, nous avons pu les ajouter à notre entité **Cours** comme ceci :
```php
use App\Validator as EDTConstraints;

/**
 * @ORM\Entity(repositoryClass=CoursRepository::class)
 * @EDTConstraints\DateHeureCours
 * @EDTConstraints\ProfesseurDisponible
 * @EDTConstraints\SalleDisponible
 */
class Cours {
    ...
}
```

Et voici le résultat dans le formulaire de création de **Cours** :

![Déclenchement validateur](/readmeAssets/img/DeclenchementValidateur1.PNG)

![Déclenchement validateur](/readmeAssets/img/DeclenchementValidateur2.PNG)

En ayant au préalable créé un **Cours** identique :

![Déclenchement validateur](/readmeAssets/img/DeclenchementValidateur3.PNG)