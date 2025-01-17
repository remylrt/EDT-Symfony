# Rapport de projet EDT Symfony

## Introduction
Ce projet consistait en la réalisation d'une application web permettant aux étudiants de visualiser leurs emplois du temps quotidiens, en utilisant le framework PHP Symfony dans le cadre du module Programmation web avancée de la LP Programmation avancée.

Notre binôme était composé de Nicolas LOPES et de Rémy LARTIGUELONGUE.

Ce rapport sera séparé en deux grandes parties. L'une où nous détaillerons notre travail pour réaliser la partie obligatoire du sujet, commune à tous les groupes et l'autre où nous aborderons les améliorations que nous avons pris l'initiative d'implémenter dans l'application.


Sommaire 
=========
- [Rapport de projet EDT Symfony](#rapport-de-projet-edt-symfony)
- [Introduction](#introduction)
- [Installation](#installation)
- [Résumé](#résumé)
  - [Points d'entrée API](#points-dentrée-api)
- [Partie commune](#partie-commune)
  - [Extension du modèle de données](#extension-du-modèle-de-données)
  - [Interface d'administration](#interface-dadministration)
  - [API](#api)
  - [Interface VueJS](#interface-vuejs)
    - [Affichage des cours d'aujourd'hui et du plus tôt au plus tard](#affichage-des-cours-daujourdhui-et-du-plus-tôt-au-plus-tard)
    - [Boutons jour précédent et jour suivant pour afficher le calendrier des autres jours](#boutons-jour-précédent-et-jour-suivant-pour-afficher-le-calendrier-des-autres-jours)
    - [Pour chaque cours affichage de l'heure de début, de fin, le type, la salle, la matière et le professeur](#pour-chaque-cours-affichage-de-lheure-de-début-de-fin-le-type-la-salle-la-matière-et-le-professeur)
- [Améliorations apportées](#améliorations-apportées)
  - [Intégration de "Note ton prof!"](#intégration-de-note-ton-prof)
  - [Création d'une page d'accueil](#création-dune-page-daccueil)
  - [Récupération des articles depuis le flux RSS de l'IUT](#récupération-des-articles-depuis-le-flux-rss-de-liut)
  - [Emplois du temps de la semaine](#emplois-du-temps-de-la-semaine)
  - [Emplois du temps des salles](#emplois-du-temps-des-salles)
  - [Exportation des calendriers au format iCalendar](#exportation-des-calendriers-au-format-icalendar)
  - [Skeleton loaders](#skeleton-loaders)
  - [Indicateur d'heure](#indicateur-dheure)
  - [Authentification au panneau d'administration](#authentification-au-panneau-dadministration)




## Installation
### Archive 
- Extraire le contenu de l'archive
- Entrer dans le dossier *edt_lartiguelongue_lopes*
- Exécuter `composer install`
- Modifier le fichier .env pour y indiquer les informations suivantes : 

```dotenv
APP_ENV=dev
APP_SECRET=4216e7a8a60c731y6x6t2ad3540940e8
DATABASE_URL="mysql://Utilisateur:MotDePasse@localhost:3306/nomDeLaTable"
```
- Vérifier que la base de données est bien lancée
- Exécuter `php bin/console doctrine:database:create`
- Exécuter `php bin/console doctrine:schema:update --force`
- Exécuter le script SQL pour peupler la base de données
- Exécuter `cd public` puis `php -S localhost:8000` sans changer le port
- Accéder à localhost:8000 pour arriver sur la page d'accueil
- Pour accéder au panel admin il faut se connecter avec les identifiants suivants : 

    Email : `admin@edt.com` 
    Mot de passe : `admin`
    
### Github
- Exécuter `git clone https://github.com/remylrt/EDT-Symfony.git edt_lartiguelongue_lopes`
- Exécuter `cd edt_lartiguelongue_lopes`
- Exécuter `composer install`
- Modifier le fichier .env pour y indiquer les informations suivantes : 

```dotenv
APP_ENV=dev
APP_SECRET=4216e7a8a60c731y6x6t2ad3540940e8
DATABASE_URL="mysql://Utilisateur:MotDePasse@localhost:3306/nomDeLaTable"
```
- Vérifier que la base de données est bien lancée
- Exécuter `php bin/console doctrine:database:create`
- Exécuter `php bin/console doctrine:schema:update --force`
- Exécuter le script SQL pour peupler la base de données
- Exécuter `cd public` puis `php -S localhost:8000` sans changer le port
- Accéder à localhost:8000 pour arriver sur la page d'accueil
- Pour accéder au panel admin il faut se connecter avec les identifiants suivants :  

    Email : `admin@edt.com` 
    Mot de passe : `admin`

## Résumé
Notre objectif dans ce projet était, en plus de répondre aux contraintes du sujet de base, de palier à certains inconvénients que l'on rencontre lors de notre utilisation de l'emploi du temps actuel de l'IUT.

C'est pourquoi la page d'accueil de notre application est une sorte de "hub" permettant d'accéder aux emplois du temps mais également aux autres services de l'IUT que l'on trouve sur leur site, afin que les étudiants puissent tout faire depuis l'application.

### Points d'entrée API
#### `GET` */professeurs*
Retourne la liste des professeurs.

#### `GET` */professeurs/{id}*
Retourne le professeur correspondant à l'ID passé en paramètre.

#### `GET` */professeurs/daily/{date}*
Retourne la liste des professeurs dispensant des cours à la date passée en paramètre.

#### `GET` */avis*
Retourne la liste des avis.

#### `POST` */professeurs/{id}*
Créé un avis associé au professeur correspondant à l'ID passé en paramètre.

#### `PATCH` */professeurs/{id}*
Modifie l'avis correspondant à l'ID passé en paramètre.

#### `DELETE` */professeurs/{id}*
Supprime l'avis correspondant à l'ID passé en paramètre.

#### `GET` */cours*
Retourne la liste des cours.

#### `GET` */cours/{date}*
Retourne la liste des cours se déroulant à la date passée en paramètre.

#### `GET` */cours/weekly/{date}*
Retourne la liste des cours se déroulant durant la semaine qui contient la date passée en paramètre.

#### `GET` */salles*
Retourne la liste des salles.

#### `GET` */salles/{numero}*
Retourne la salle correspondant au numéro passé en paramètre.

## Partie commune
### Extension du modèle de données
Voici à quoi ressemblait notre modèle de données au début du projet :

![Modèle de données existant](http://testsymfonyvues.fxcj3275.odns.fr/imagesReadme/ModeleDonneesAvant.PNG)

Il nous était demandé, avant de se lancer dans le développement de l'application, d'étendre ce modèle en y rajoutant des entités **Cours** et **Salle** comme ci-dessous :

![Modèle de données étendu](http://testsymfonyvues.fxcj3275.odns.fr/imagesReadme/ModeleDonneesApres.PNG)

Nous nous sommes donc servi de la console Symfony pour générer ces nouvelles entités avec la commande `bin/console make:entity`.

Pour l'entité **Cours**, nous avons fait le choix d'implémenter *dateHeureFin* et *dateHeureDebut* en `DateTime` et le *type* en `String` de taille 255.

Dans l'entité **Salle**, le *numéro* a également été implémenté en `String` de taille 255.

En ce qui concerne les relations, nous avons choisi une ManyToOne entre **Cours** et **Matière**, entre **Cours** et **Professeur** et entre **Cours** et **Salle** car :
- Un **Cours** ne peut concerner qu'une **Matière** et une **Matière** peut faire référence à plusieurs **Cours**;
- Un **Cours** ne peut être assuré que par un **Professeur** et un **Professeur** peut assurer plusieurs **Cours**;
- Un **Cours** ne peut se dérouler que dans une **Salle** et une **Salle** peut accueillir plusieurs **Cours**.

Voici le résultat obtenu dans *Cours.php* :

<details>
  <summary>Cliquez pour afficher le code</summary>

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

</details>

Et le résultat dans *Salle.php* :

<details>
  <summary>Cliquez pour afficher le code</summary>

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
</details>


Il nous restait alors qu'à mettre à jour le schéma de la base de données en conséquence avec la commande `bin/console doctrine:schema:update --force`.

### Interface d'administration
L'étape suivante était de mettre à jour l'interface d'administration Easy Admin pour y ajouter la gestion de nos entités **Cours** et **Salle** nouvellement créées.

Avec la commande `bin/console make:admin:crud` nous avons pu créer les contrôleurs pour le CRUD de nos entités dans Easy Admin.

Cependant, elles n'apparaissent pas encore dans le menu du dashboard. Pour corriger cela, nous avons modifié la méthode `configureMenuItems()` du *DashboardController.php* pour y rajouter les liens comme cela :

<details>
  <summary>Cliquez pour afficher le code</summary>

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

</details>


Voici le menu après cette modification :

![Menu Easy Admin](http://testsymfonyvues.fxcj3275.odns.fr/imagesReadme/MenuEasyAdmin.PNG)

Et voici à quoi ressemblent les formulaires de création de **Cours** :

![Formulaire de création de cours](http://testsymfonyvues.fxcj3275.odns.fr/imagesReadme/CreateCoursAvant.PNG)

Et de création de **Salle** :

![Formulaire de création de salle](http://testsymfonyvues.fxcj3275.odns.fr/imagesReadme/CreateSalle.PNG)

Le formulaire de création de **Salle** nous convient mais nous souhaiterions pouvoir sélectionner une **Matiere**, un **Professeur** et une **Salle** dans le formulaire de création de **Cours** et éventuellement de restreindre le type de **Cours** à des valeurs prédéfinies.

Pour cela, nous avons modifié le contrôleur *CoursCrudController.php* pour y ajouter des champs permettant de saisir la **Matiere**, le **Professeur** et la **Salle** ainsi que pour mettre un champ de saisie de type `ChoiceField` sur le type de **Cours** avec les valeurs *Cours*, *TD* et *TP*.

<details>
  <summary>Cliquez pour afficher le code</summary>

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

</details>

Le formulaire de création de **Cours** après ces modifications :

![Formulaire de création de cours après modifications](http://testsymfonyvues.fxcj3275.odns.fr/imagesReadme/CreateCoursApres.PNG)

Une fois le formulaires corrects, nous devions mettre en place des validateurs pour contrôler les données qui seraient saisies.

Nous avons donc commencé par mettre des validateurs pour vérifier qu'aucun des champs ne soit vide :

Dans *Cours.php* :

<details>
  <summary>Cliquez pour afficher le code</summary>

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

</details>

Dans *Salle.php* :

<details>
  <summary>Cliquez pour afficher le code</summary>

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

</details>

Nous avons également ajouté un validateur pour que le type de **Cours** soit bien *Cours*, *TD* ou *TP* :

<details>
  <summary>Cliquez pour afficher le code</summary>

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

</details>

Aussi, nous avons mis en place un validateur `UniqueEntity` sur l'entité **Salle** pour ne pas que l'on puisse avoir deux salles ayant le même numéro :

<details>
  <summary>Cliquez pour afficher le code</summary>

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

</details>

En plus de ces validateurs, nous avons pensé à des cas qui, selon nous, nécessitaient que l'on en mette en place d'autres :
- Vérifier que la date de début est ultérieure à la date de fin du **Cours**;
- Vérifier que la date de début et la date de fin du **Cours** sont le même jour;
- Vérifier que le **Cours** fait au minimum 15 minutes et au maximum 4 heures 30;
- Vérifier que le **Professeur** sélectionné pour un **Cours** n'est pas déjà affecté à un **Cours** au mêmes horaires;
- Vérifier que la **Salle** sélectionnée pour un **Cours** n'est pas déjà affectée à un **Cours** aux mêmes horaires.

Ces cas ont nécessité que nous mettions en place des validateurs personnalisés. Pour cela nous nous sommes appuyés sur la documentation de Symfony à ce propos accessible [ici](https://symfony.com/doc/current/validation/custom_constraint.html).

Elle nous explique que le doit créer un dossier *Validator* dans *src* comme cela :

![Dossier Validator](http://testsymfonyvues.fxcj3275.odns.fr/imagesReadme/ValidatorFolder.png)

À l'intérieur, on créé deux fichiers PHP : l'un avec nom libre et l'autre avec le même nom que le premier suivi de "Validator". Ici nous avons *DateHeureCours.php* et *DateHeureCoursValidator.php*.

Dans *DateHeureCours.php* nous avons :

<details>
  <summary>Cliquez pour afficher le code</summary>

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

</details>

Cette classe doit étendre de `Symfony\Component\Validator\Constraint` et contenir un attribut *message* qui sera le message d'erreur affiché dans le formulaire lorsque la validation a échoué.

À côté de ça, dans *DateHeureCoursValidator.php* nous avons :

<details>
  <summary>Cliquez pour afficher le code</summary>

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

</details>


Cette classe doit étendre de `Symfony\Component\Validator\ConstraintValidator` et doit implémenter une méthode `validate()` qui sera exécutée à chaque fois que l'on soumettra le formulaire. Elle prend en paramètre l'entité que l'on souhaite créer ou modifier donc nous pouvons effectuer dessus des vérifications à notre guise.
Ici nous vérifions :
- Que les dates de début et de fin du **Cours** soient définies, sinon on sort de la méthode et on laisse la contrainte `NotBlank` gérer ce cas;
- Que la date de fin du **Cours** soit ultérieure à la date de début;
- Que les dates de début et de fin du **Cours** soient le même jour;
- Que les dates de début et de fin du **Cours** soient espacées d'au moins 15 minutes;
- Que les dates de début et de fin du **Cours** ne soient pas espacés de plus de 4 heures 30.

Comme on le voit, pour générer l'erreur on récupère le contexte dans lequel le validateur est exécuté, on lui indique le message d'erreur souhaité, le champ sur lequel on souhaite voir apparaître le message et on l'ajoute à la liste des violations. Notre méthode `validate()` peut donc retourner plusieurs erreurs de saisie en une seule fois.

Les deux autres validateurs, qui vérifient si le professeur et la salle sélectionnés sont disponibles, étant grandement similaires, nous avons fait le choix de ne pas les détailler ici.

Une fois nos validateurs faits, nous avons pu les ajouter à notre entité **Cours** comme ceci :

<details>
  <summary>Cliquez pour afficher le code</summary>

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

</details>

Et voici le résultat dans le formulaire de création de **Cours** :

![Déclenchement validateur](http://testsymfonyvues.fxcj3275.odns.fr/imagesReadme/DeclenchementValidateur1.png)

![Déclenchement validateur](http://testsymfonyvues.fxcj3275.odns.fr/imagesReadme/DeclenchementValidateur2.png)

En ayant au préalable créé un **Cours** identique :

![Déclenchement validateur](http://testsymfonyvues.fxcj3275.odns.fr/imagesReadme/DeclenchementValidateur3.png)

## API
Il nous était demandé de créer deux points d'entrée API. L'un pour récupérer la liste des **Cours** et l'autre pour récupérer la liste des **Salles**.

Nous avons donc créé deux nouveaux contrôleurs *CoursController.php* et *SalleController.php* dans le dossier *Api* avec la commande `bin/console make:controller`.

Nous nous sommes basés sur les contrôleurs d'API que nous avions faits en TP pour créer ceux-là.

*CoursController.php* :

<details>
  <summary>Cliquer pour afficher le code</summary>

```php
/**
 * @Route("/api/cours", name="api_cours_")
 */
class CoursController extends AbstractController {

    /**
     * @Route("", name="index", methods={"GET"})
     */
    public function index(CoursRepository $coursRepository): JsonResponse {
        $cours = $coursRepository->findAll();

        return $this->json($cours, 200);
    }
}
```

</details>

*SalleController.php* :

<details>
  <summary>Cliquer pour afficher le code</summary>

```php
/**
 * @Route("/api/salles", name="api_salles_")
 */
class SalleController extends AbstractController {

    /**
     * @Route("", name="index", methods={"GET"})
     */
    public function index(SalleRepository $salleRepository): JsonResponse {
        $salles = $salleRepository->findAll();

        return $this->json($salles, 200);
    }
}
```

</details>

Egalement en prenant exemple sur les travaux réalisés en TP, nous avons modifié les entités **Cours** et **Salle** pour les faire implémenter la classe `JsonSerializable` et une méthode `jsonSerialize()` afin d'indiquer comment l'on souhaite formater nos objets en JSON.

<details>
  <summary>Cliquer pour afficher le code</summary>

```php
use JsonSerializable;

class Cours implements JsonSerializable {

    ...

    public function jsonSerialize() {
        return [
            'id' => $this->id,
            'name' => $this->matiere->__toString(),
            'type' => $this->type,
            'professeur' => $this->professeur->__toString(),
            'salle' => $this->salle->__toString(),
            'start' => $this->dateHeureDebut->format('Y-m-d H:i:s'),
            'end' => $this->dateHeureFin->format('Y-m-d H:i:s'),
        ];
    }

    ...

}
```

</details>

*SalleController.php* :

<details>
  <summary>Cliquer pour afficher le code</summary>

```php
use JsonSerializable;

class Salle implements JsonSerializable {

    ...

    public function jsonSerialize() {
        return [
            'id' => $this->id,
            'salle' => $this->numero,
            'cours' => $this->cours->toArray(),
        ];
    }

    public function __toString() {
        if ($this->numero) {
            return $this->numero;
        }

        return '';
    }

    ...

}
```

</details>

Voici des extraits des retours obtenus en appelant ces deux points d'entrée.

GET /api/cours :

<details>
  <summary>Cliquer pour afficher le code</summary>

```json
[
  {
    "id": 1,
    "name": "M1102: Introduction à l'algorithmique",
    "type": "Cours",
    "professeur": "Patrick Etcheverry",
    "salle": "125",
    "start": "2021-03-08 08:30:00",
    "end": "2021-03-08 12:30:00"
  },
  {
    "id": 2,
    "name": "M1102: Introduction à l'algorithmique",
    "type": "TP",
    "professeur": "Philippe Roose",
    "salle": "126",
    "start": "2021-03-10 12:07:00",
    "end": "2021-03-10 12:10:00"
  },
  {
    "id": 3,
    "name": "M1102: Introduction à l'algorithmique",
    "type": "TP",
    "professeur": "Christophe Marquesuzaa",
    "salle": "124",
    "start": "2021-03-08 15:21:00",
    "end": "2021-03-08 15:22:00"
  },

  ...
]
```

</details>

GET /api/salles :

<details>
  <summary>Cliquer pour afficher le code</summary>

```json
[
  {
    "id": 1,
    "salle": "124",
    "cours": [
      {
        "id": 3,
        "name": "M1102: Introduction à l'algorithmique",
        "type": "TP",
        "professeur": "Christophe Marquesuzaa",
        "salle": "124",
        "start": "2021-03-08 15:21:00",
        "end": "2021-03-08 15:22:00"
      },
      {
        "id": 5,
        "name": "M1102: Introduction à l'algorithmique",
        "type": "TP",
        "professeur": "Christophe Marquesuzaa",
        "salle": "124",
        "start": "2021-03-09 10:45:00",
        "end": "2021-03-09 15:15:00"
      }
    ]
  },

  ...
]
```

</details>

### Interface VueJS

Pour la partie front nous avons choisi d'utiliser la librairie [Vuetify](https://vuetifyjs.com/en/) pour simplifier le développement et avoir une interface complexe en très peu de lignes de code. Nous l'avons également utilisée car il est possible de l'intégrer grâce à un CDN, plus simple pour éviter tous les problèmes éventuels avec npm. 


Pour l'installer il faut simplement ajouter les lignes suivantes dans le template de base :

<details>
<summary>Cliquez pour afficher le code</summary>

```html
<head>
  ...
  <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/@mdi/font@4.x/css/materialdesignicons.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/vuetify@2.x/dist/vuetify.min.css" rel="stylesheet">
  ...
</head>
<body>
  ...
  <script src="https://cdn.jsdelivr.net/npm/vue@2.x/dist/vue.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/vuetify@2.x/dist/vuetify.js"></script>
  ...
</body>
</html>

```
</details>


Nous avons fait le choix d'utiliser les templates afin d'avoir des fichiers plus organisés que dans le dossier *public* mais également pour le pas avoir les extensions des fichiers dans l'URL.

Pour contourner l'incompatibilité de la syntaxe TWIG et VueJS nous avons, dans chaque fichier avec VueJS, redéclaré les délimiteurs avec la ligne `delimiters` afin d'utiliser `${ }` à la place de  `{{ }}`.

<details>
<summary>Cliquez pour afficher le code</summary>

```javascript
var app = new Vue({
    delimiters: ['${', '}'],
    el: '#app'
}
```

</details> 

Pour indiquer à Vue que nous utilisons Vuetify il faut ajouter une ligne `vuetify` dans la déclaration.

<details>
<summary>Cliquez pour afficher le code</summary>

```javascript
var app = new Vue({
    delimiters: ['${', '}'],
    el: '#app',
    vuetify: new Vuetify({
        lang: {
            current: 'fr'
        }
    })
}
```

</details>



#### Affichage des cours d'aujourd'hui et du plus tôt au plus tard

Pour afficher les cours nous avons utilisé le composant `<v-calendar>` de Vuetify.
Pour ajouter le calendrier il faut simplement ajouter ce code et nous avons un affichage basique :

agenda.html.twig :

<details>
<summary>Cliquez pour afficher le code</summary>

```html
<v-calendar  
	color="primary" // couleur
	type="day" //Type de calendrier (semaine, mois, jours)
	:first-interval="15" //heure de début du calendrier -> 7h30
	:interval-count="24" //heure de fin du calendrier -> 19h30
	interval-minutes="30" //intervale de temps à afficher ici 30 minute
	:events="events" //liste des cours
	:value="today"  //date du jour à afficher 
>
</v-calendar>
```
</details>

calendar.js

<details>
<summary>Cliquez pour afficher le code</summary>

```javascript
var app = new Vue({
    delimiters: ['${', '}'],
    el: '#app',
    vuetify: new Vuetify({
        lang: { current: 'fr'}
    }),
    data: {
        appName: "EDT",
        today: new Date(),
        events: [],
    },
    methods: {
        //renvoie la date du jour au format YYYY-mm-dd
        getFormattedTodaysDate() {
            let year = this.today.getFullYear();
            let month = this.today.getMonth() + 1;
            if(month < 10){month = "0" + month;}
            let day = this.today.getDate();
            if(day < 10){day = "0" + day;}
            let formattedDate = `${ year }-${ month }-${ day }`;
            return formattedDate;
        },
        //fait un appel à l'API pour récupérer la liste des cours du jour
        getCours(){
            let date = this.getFormattedTodaysDate();
            axios.get(this.apiBase + '/cours/' + date )
                .then(response => { this.events = response.data; })
                .catch(error => { console.log(error); })
        }
    },
    mounted() {
        this.getCours();
    }
})
```
</details>



La méthode `getCours()` fait un appel à l'API pour récupérer les cours du jour sous forme d'un tableau d'objets qui seront directement affectés à la data `event`.

Voici un exemple de résultat de l'API pour `api/cours/2021-03-12`

<details>
<summary>Cliquez pour afficher le code</summary>

```json
[
   {
      "id":41,
      "name":"M1102: Introduction \u00e0 l\u0027algorithmique",
      "type":"TP",
      "professeur":"Yann Carpentier",
      "salle":"124",
      "start":"2021-03-12 08:30:00",
      "end":"2021-03-12 12:30:00"
   },
   {
      "id":42,
      "name":"WS: M4102C - Webservices",
      "type":"Cours",
      "professeur":"Marc Dalmau",
      "salle":"125",
      "start":"2021-03-12 14:30:00",
      "end":"2021-03-12 17:30:00"
   },
   {
      "id":43,
      "name":"M1102: Introduction \u00e0 l\u0027algorithmique",
      "type":"Cours",
      "professeur":"Patrick Etcheverry",
      "salle":"127",
      "start":"2021-03-12 17:30:00",
      "end":"2021-03-12 18:30:00"
   }
]
```
</details>

![simpleAgenda](http://testsymfonyvues.fxcj3275.odns.fr/imagesReadme/coursSimple.PNG)





#### Boutons jour précédent et jour suivant pour afficher le calendrier des autres jours

Pour pouvoir naviguer entre différents jours nous avons ajouté 3 boutons, un pour aller dans le passé, un dans le futur et un pour revenir à aujourd'hui. 


Pour ce faire il faut ajouter le code suivant à l'intérieur des balises `<v-calendar> </v-calendar>` :

<details>
<summary>Cliquez pour afficher le code</summary>

```html
<template v-slot:day-header="{ day }">
    <template v-if="day" class="text-center" >
        <v-toolbar flat>
            <v-layout justify-center class="pa-0">
            	<v-spacer></v-spacer>
            	<v-btnfab text small color="grey darken-2" @click="getPreviousDate()" >
            	<v-icon small>
            		mdi-chevron-left
            	</v-icon>
            	</v-btn>
            	<v-btn outlined class="mx-4" color="grey darken-2" @click="backToCurrentDate()" >
            		Aujourd'hui
            	</v-btn>
            	<v-btn fab text small color="grey darken-2" @click="getNextDate()" >
            		<v-icon small>
                        mdi-chevron-right
                    </v-icon>
                </v-btn>
                <v-spacer></v-spacer>
            </v-layout>
        </v-toolbar>
    </template>
</template>
```
</details>

Il faut également ajouter les méthodes suivantes dans le fichier javascript :

<details>
<summary>Cliquez pour afficher le code</summary>

```javascript
//Permet d'aller au jour précédent
getPreviousDate(){
    let dateStr = this.today;
    this.today = new Date(new Date(dateStr).setDate(new Date(dateStr).getDate() - 1));
    this.getCours();
},
//Permet d'aller au jour suivant
getNextDate(){
    let dateStr = this.today;
    this.today = new Date(new Date(dateStr).setDate(new Date(dateStr).getDate() + 1));
    this.getCours();
},
//permet de retourner à la date d'aujourd"hui
backToCurrentDate(){
    this.today = new Date()
    this.getCours();
},
```
</details>


![image-20210311164150490](http://testsymfonyvues.fxcj3275.odns.fr/imagesReadme/boutonsNav.PNG)

#### Pour chaque cours affichage de l'heure de début, de fin, le type, la salle, la matière et le professeur

Pour afficher les détails des cours nous avons fait apparaître que le type de cours, le nom du cours, l'enseignant et la salle sur le calendrier en ajoutant le code suivant. 

<details>
<summary>Cliquez pour afficher le code</summary>

```html
<template v-slot:event="{event}">
    <div class="fill-height pl-2">
        <strong> ${ event.type } de ${ event.name } </strong> ( ${ event.professeur } ) <br/>
        Salle ${ event.salle }
    </div>
</template>
```
</details>

![DetailsCours](http://testsymfonyvues.fxcj3275.odns.fr/imagesReadme/detailCours.PNG)


Nous avons également décidé d'ajouter une modal pour faire apparaitre plus clairement les informations si l'écran est trop petit pour tout afficher.

<details>
<summary>Cliquez pour afficher le code</summary>

```html
<v-dialog v-model="showSmallSizeDialog" max-width="800" hide-overlay transition="dialog-bottom-transition" >
    <v-card>
        <v-card-title>
            <v-btn icon class="pr-5" @click="showSmallSizeDialog = false, currentClassInformations = {}" >
                <v-icon>mdi-close</v-icon>
            </v-btn>
            <span class="headline">Détails</span>
        </v-card-title>
        <div>
            <v-list two-line>
                <v-list-item>
                    <v-list-item-icon>
                        <v-icon color="indigo">
                            mdi-school
                        </v-icon>
                    </v-list-item-icon>
                    <v-list-item-content>
                        <v-list-item-title>Nom du module</v-list-item-title>
                        <v-list-item-subtitle>${ currentClassInformations.name }</v-list-item-subtitle>
                    </v-list-item-content>
                </v-list-item>
                <v-list-item>
                    <v-list-item-action></v-list-item-action>

                    <v-list-item-content>
                        <v-list-item-title>Type</v-list-item-title>
                        <v-list-item-subtitle>${ currentClassInformations.type }</v-list-item-subtitle>
                    </v-list-item-content>
                </v-list-item>
                <v-list-item>
                    <v-list-item-action></v-list-item-action>
                    <v-list-item-content>
                        <v-list-item-title>Salle</v-list-item-title>
                        <v-list-item-subtitle>${ currentClassInformations.salle }</v-list-item-subtitle>
                    </v-list-item-content>
                </v-list-item>
                <v-divider inset></v-divider>
                <v-list-item>
                    <v-list-item-icon>
                        <v-icon color="indigo">
                            mdi-account
                        </v-icon>
                    </v-list-item-icon>
                    <v-list-item-content>
                        <v-list-item-title>Enseignant</v-list-item-title>
                        <v-list-item-subtitle>${ currentClassInformations.professeur }</v-list-item-subtitle>
                    </v-list-item-content>
                </v-list-item>
                <v-divider inset></v-divider>
                <v-list-item>
                    <v-list-item-icon>
                        <v-icon color="indigo">
                            mdi-clock
                        </v-icon>
                    </v-list-item-icon>

                    <v-list-item-content>
                        <v-list-item-title>Horaires</v-list-item-title>
                        <v-list-item-subtitle>${ getOnlyHourOfDate(currentClassInformations.start) } : ${ getOnlyHourOfDate(currentClassInformations.end) }</v-list-item-subtitle>
                    </v-list-item-content>
                </v-list-item>
            </v-list>
        </div>
    </v-card>
</v-dialog>
```
</details>


Ce code génère donc une modal qui est affiché dès qu'on clique sur un cours grâce à la méthode `showEventDetails` :

<details>
<summary>Cliquez pour afficher le code</summary>

```javascript
showEventDetails ({ nativeEvent, event }) {
    this.currentClassInformations = {
        id: event.id,
        name: event.name,
        professeur: event.professeur,
        type: event.type,
        salle: event.salle,
        start: event.start,
        end: event.end,
    }
    this.showSmallSizeDialog = true;
    nativeEvent.stopPropagation()
},
```
</details>


![modalDetailsCours](http://testsymfonyvues.fxcj3275.odns.fr/imagesReadme/detailCoursModal.PNG)


## Améliorations apportées 

### Intégration de "Note ton prof!"

Nous avons intégré le travail réalisé durant le module mais uniquement sur la page permettant de consulter l'EDT du jour. Etant donné que ce serait un peu stupide d'afficher à chaque fois tous les enseignants nous n'affichons que les enseignants qui ont un cours durant le jour sélectionné.

![notetonprof](http://testsymfonyvues.fxcj3275.odns.fr/imagesReadme/notetonprrof.PNG)



Nous avons également modifié l'interface pour consulter les avis et pour donner son avis sur un enseignant.

![noter](http://testsymfonyvues.fxcj3275.odns.fr/imagesReadme/donnercoms.PNG)
![comms](http://testsymfonyvues.fxcj3275.odns.fr/imagesReadme/commss.PNG)



### Création d'une page d'accueil

Afin d'avoir une page centrale lorsqu'un utilisateur arrive sur le site nous avons créé une page d'accueil qui centralise les liens utiles et qui propore également les derniers articles de l'IUT.

![home](http://testsymfonyvues.fxcj3275.odns.fr/imagesReadme/home.PNG)


### Récupération des articles depuis le flux RSS de l'IUT

Pour afficher les derniers articles de l'IUT nous avons utilisé le flux RSS de l'IUT mais nous avons rencontré quelques problèmes car il renvoie du HTML déjà formaté, chose que nous ne voulions pas. Nous avons donc mis en place un traitement avant d'afficher la page pour récupérer uniquement les informations qui nous intéressaient sans balise html.

Pour se faire nous avons utilisé des exprésions régulières.

 <details>
<summary>Cliquez pour afficher le code</summary>

```php
$xml = simplexml_load_file("https://www.iutbayonne.univ-pau.fr/rss/news");
$articles = $xml->xpath("//item");

foreach ($articles as $key => $article) {
    $htmlContent = $article->description;

    //récupération image
    preg_match('/<img typeof="foaf:Image" class="img-responsive" src="(.*?)" width="100" height="100" alt="" \/>/s', $htmlContent, $matchImage);
    if(array_key_exists(1, $matchImage)){
        $article->image = $matchImage[1];
    }

    //récupération description
    preg_match('/<p>(.*?)<\/p>/s', $htmlContent, $matchDescription);
    if(array_key_exists(1, $matchDescription)){
        $article->description = $matchDescription[1];
    }

    if(sizeof($matchImage) == 0){
        unset($articles[$key]);
    }
} 
```
</details>



### Emplois du temps de la semaine 

Nous pensions qu'il était important de proposer un affichage de l'EDT par semaine. Pour ce faire nous avons créé une 
nouvelle page similaire à la page pour l'EDT du jour. Nous avons retiré la partie note ton prof et avons changé les 
attributs du `<v-calendar>` pour avoir un affichage par semaine. Le fonctionnement des détails des cours est identique.

![home](http://testsymfonyvues.fxcj3275.odns.fr/imagesReadme/edtSemaine.PNG)

Nous avons simplement eu à créer un nouveau point d'entrée API pour récupérer les cours de la semaine à partir d'une date. Voici donc la méthode du contrôleur *CoursController.php* correspondante :

<details>
  <summary>Cliquer pour afficher le code</summary>

```php
/**
 * @Route("/weekly/{date}", name="showWeekly", methods={"GET"})
 */
public function showWeekly($date, CoursRepository $coursRepository): JsonResponse {
    $dateCours = \DateTime::createFromFormat('Y-m-d', $date);

    if (!$dateCours) {
        return $this->json([
            'message' => 'Le format de la date est invalide. Format accepté: AAAA-MM-JJ'
        ], 404);
    }
        
    $cours = $coursRepository->findByDateWeekly($dateCours);

    return $this->json($cours, 200);
}
```

</details>

On récupère la date passée dans l'URL et on la passe à la méthode `DateTime::createFromFormat()` qui va tenter d'en créer un objet `DateTime`. Si la conversion échoue, `$dateCours` vaut *false* et on peut retourner une erreur 404.

Si la date est correcte, on peut récupérer les cours de la semaine avec le repository en appelant la méthode `findByDateWeekly()` que voici :

<details>
  <summary>Cliquer pour afficher le code</summary>

```php
/**
 * @return Cours[] Returns an array of Cours objects
 */
public function findByDateWeekly($date) {
    $anneeChoix = $date->format('Y');
    $semChoix = $date->format('W');

    $timeStampPremierJanvier = strtotime($anneeChoix . '-01-01');
    $jourPremierJanvier = date('w', $timeStampPremierJanvier);
        
    $numSemainePremierJanvier = date('W', $timeStampPremierJanvier);
        
    $decalage = ($numSemainePremierJanvier == 1) ? $semChoix - 1 : $semChoix;

    $timeStampDate = strtotime('+' . $decalage . ' weeks', $timeStampPremierJanvier);

    $dateDebutSemaine = ($jourPremierJanvier == 1) ? date('Y-m-d', $timeStampDate) : date('Y-m-d', strtotime('last monday', $timeStampDate));
    $dateFinSemaine = ($jourPremierJanvier == 1) ? date('Y-m-d', $timeStampDate) : date('Y-m-d',strtotime('next sunday', $timeStampDate));

    return $this->createQueryBuilder('c')
        ->where('c.dateHeureDebut BETWEEN :dateDebutSemaine AND :dateFinSemaine')
        ->setParameter('dateDebutSemaine', $dateDebutSemaine . '%')
        ->setParameter('dateFinSemaine', $dateFinSemaine . '%')
        ->orderBy('c.dateHeureDebut', 'ASC')
        ->getQuery()
        ->getResult()
    ;
}
```

</details>

Cette méthode va, à partir de la date passée en paramètre, déterminer la date du premier et du dernier jour de la semaine correspondante et ainsi pouvoir rechercher en base de données les cours qui se déroulent entre ces deux dates.



### Emplois du temps des salles

Lorsque nous étions encore en DUT et en présentiel nous avions un problème à chaque fois que nous souhaitions aller 
dans une salle : est-elle libre ?

Nous avons donc décidé de proposer, depuis la page d'accueil, un accès aux emplois du temps des différentes salles.

![salles](http://testsymfonyvues.fxcj3275.odns.fr/imagesReadme/salles.PNG)

![salles](http://testsymfonyvues.fxcj3275.odns.fr/imagesReadme/salles2.PNG)


### Exportation des calendriers au format iCalendar
Nous avons ajouté la possibilité d'exporter les évènements de la semaine courante sous forme d'un fichier *iCalendar* (.ics) pouvant être importé dans n'importe quel calendrier comme Google Calendar.

Pour générer le fichier *iCalendar*, nous nous sommes servis d'une bibliothèque JavaScript trouvé sur Github accessible [ici](https://github.com/nwcell/ics.js/).

Après avoir ajouté les fichiers nécessaires dans le dossier *public/js* nous avons pu écrire notre méthode VueJS :

<details>
  <summary>Cliquer pour afficher le code</summary>

```js
exportCalendarAsICS: function () {
    let date = this.getFormattedTodaysDate();

    axios.get(this.apiBase + '/cours/weekly/' + date)
        .then(response => {
            coursSemaine = response.data;

            let cal = ics();

            coursSemaine.forEach(event => {
                cal.addEvent(`${ event.type } de ${ event.name }`, `Avec ${ event.professeur } en salle ${ event.salle }.`, 'IUT de Bayonne et du Pays Basque', event.start, event.end);
            });

            cal.download("EmploiDuTemps");
        })
        .catch(error => {
            console.log(error);
        })

},
```

</details>

Cette méthode se contente de faire un appel API précédemment décrite pour récupérer les cours de la semaine courante sous forme de tableau et va, pour chacun d'entre eux, créer un évènement.

Pour récupérer les cours de la semaine, cette méthode fait appel au point d'entrée API `/weekly/{date}`, détaillé plus haut dans la section concernant l'emploi du temps des cours par semaine.

Quand cela est fait, on propose à l'utilisateur de télécharger le fichier.

Il ne restait plus qu'à ajouter un bouton dans l'interface pour appeler cette méthode.

![home](http://testsymfonyvues.fxcj3275.odns.fr/imagesReadme/icalendar.PNG)


### Skeleton loaders
Pour rajouter un peu d'UX pour indiquer que l'application charge des données nous avons utilisé le composant 
`<v-skeleton-loader>`.

![salles](http://testsymfonyvues.fxcj3275.odns.fr/imagesReadme/skeleton1.PNG)

![salles](http://testsymfonyvues.fxcj3275.odns.fr/imagesReadme/skeleton2.PNG)


### Indicateur d'heure
Pour que l'utilisateur visualise plus facilement quel est le prochain cours nous avons mis en place un indicateur visuel 
comme sur l'image suivante.

![home](http://testsymfonyvues.fxcj3275.odns.fr/imagesReadme/indicateurHeure.PNG)


### Authentification au panneau d'administration
Enfin, nous souhaitions restreindre l'accès au panneau d'administration Easy Admin par une authentification.

Nous avons donc, avec la console Symfony, créé une classe **Admin** qui représentera nos utilisateurs avec la commande `bin/console make:user`.

Puis, nous avons généré le contrôleur et le formulaire d'authentification avec la commande `bin/console make:auth`.

En nous rendant dans le fichier *config/packages/security.yaml*, nous n'avons eu qu'à ajouter dans la section *access_control* la règle suivante :

```yaml
access_control:
    - { path: ^/admin, roles: ROLE_ADMIN }
```

Qui indique à Symfony que l'on souhaite restreindre la route */admin* aux utilisateurs possédant le rôle d'administrateur.

Après avoir mis à jour le schéma de base de données et ajouté un utilisateur (le hash de son mot de passe a été généré avec la commande `bin/console security:encode-password`), l'authentification et le formulaire associé, bien qu'ayant une esthétique sommaire, étaient fonctionnels.

Nous avons alors modifié le template du formulaire pour l'adapter au design de notre application (voir ci-dessous), ajouté un bouton de connexion (qui redirige sur la route */login*) et de déconnexion (qui redirige sur la route */logout*) et mis une condition sur l'affichage du lien vers le pannel d'administration sur la page d'accueil.

```html
{% if is_granted('ROLE_ADMIN') %}
    <v-subheader>Administration</v-subheader>
    <v-list-item-group color="primary">
        <v-list-item href="{{ path("admin") }}">
            <v-list-item-icon>
                <v-icon>mdi-cogs</v-icon>
            </v-list-item-icon>
            <v-list-item-content>
                <v-list-item-title>Administration</v-list-item-title>
            </v-list-item-content>
        </v-list-item>
    </v-list-item-group>
    <v-divider></v-divider>
{% endif %}

```

Le formulaire d'authentification :

![Formulaire d'authentification](http://testsymfonyvues.fxcj3275.odns.fr/imagesReadme/LoginForm.png)