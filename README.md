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
L'étape suivante était de mettra à jour l'interface d'administration Easy Admin pour y ajouter la gestion de nos entités **Cours** et **Salle** nouvellement créées.

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

![Menu Easy Admin](/readmeAssets/img/MenuEasyAdmin.PNG)

Et voici à quoi ressemblent les formulaires de création de **Cours** :

![Formulaire de création de cours](/readmeAssets/img/CreateCoursAvant.PNG)

Et de création de **Salle** :

![Formulaire de création de salle](/readmeAssets/img/CreateSalle.PNG)

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

![Formulaire de création de cours après modifications](/readmeAssets/img/CreateCoursApres.PNG)

Une fois le formulaires corrects, nous devions mettre en place des validateurs pour contrôler les données qui seraient saisies.

Nous avons donc commencé par mettre des validateurs pour vérifier qu'aucun des champs ne soient vides :

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

Cette classe doit étendre de `Symfony\Component\Validator\Constraint` et contenir un attribut *message* qui sera le message d'erreur affiché dans le formulaire lorsque la validation n'est pas passée.

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
- Que les dates de début et de fin du **Cours** soient définis, sinon on sort de la méthode et on laisse la contrainte `NotBlank` gérer ce cas;
- Que la date de fin du **Cours** soit ultérieure à la date de début;
- Que les dates de début et de fin du **Cours** soient le même jour;
- Que les dates de début et de fin du **Cours** soient espacés d'au moins 15 minutes;
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

![Déclenchement validateur](/readmeAssets/img/DeclenchementValidateur1.PNG)

![Déclenchement validateur](/readmeAssets/img/DeclenchementValidateur2.PNG)

En ayant au préalable créé un **Cours** identique :

![Déclenchement validateur](/readmeAssets/img/DeclenchementValidateur3.PNG)

## API
Il nous était demandé de créer deux points d'entrée API. L'un pour récupérer la liste des **Cours** et l'autre pour récupérer la liste des **Salles**.

Nous avons donc créé deux nouveaux contrôleurs *CoursController.php* et *SalleController.php* dans le dossier *Api* avec la commande `bin/console make:controller`.

Nous nous sommes basé sur les contrôleurs d'API que nous avions fait en TP pour créer ceux là.

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

Pour la partie front nous avons choisi d'utiliser la librairie [Vuetify](https://vuetifyjs.com/en/) pour simplifier le développement et avoir une interface complexe en très peu de lignes de code. Nous l'avons également utilisée car il est possible de l'intégrer grâce à un CDN, plus simple pour éviter tous les problème éventuels avec npm. 
<br/><br/>
Pour l'installer il faut simplement ajouter les lignes suivant dans le template de base. 
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
<br/><br/>

Nous avons fait le choix d'utiliser les templates afin d'avoir des fichier plus organisés que dans le dossier publique mais également pour le pas avoir les extensions des fichiers dans l'URL.

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
<br/><br/>
Pour indiquer à vue que nous utilisons vuetify il faut ajouter une ligne `vuetify` la déclaration.
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
<br/><br/>


#### Affichage des cours d'aujourd'hui et du plus tôt au plus tard

Pour afficher les cours nous avons utilisé le composent `<v-calendar>` de Vuetify.
Pour ajouter le calendrier il faut simplement ajouter ce code et nous avons une affichage basique 
<br/>
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
<br/><br/>
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


![simpleAgenda](http://testsymfonyvues.fxcj3275.odns.fr/imagesReadme/coursSimple.PNG)





#### Boutons jour précédent et jour suivant pour afficher le calendrier des autres jours

Pour pouvoir naviguer entre différents jours nous avons ajouté 3 boutons, un pour aller dans le passé, un dans le futur et un pour revenir à aujourd'hui. 
<br/><br/>

Pour ce faire il faut ajouter le code suivant à l'intérieur des balises de `<v-calendar> </v-calendar>`

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
<br/><br/>
Il faut également ajouter les méthodes suivante dans le fichier javascript 
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
<br/><br/>

![image-20210311164150490](http://testsymfonyvues.fxcj3275.odns.fr/imagesReadme/boutonsNav.PNG)
<br/><br/>
#### Pour chaque cours affichage de l'heure de début, de fin, le type, la salle, la matière et le professeur
<br/><br/>
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
<br/><br/>

Nous avons également décidé d'ajouter un modal pour faire apparaitre plus clairement les information si l'écran est trop petit pour tout afficher.
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
<br/><br/>

Ce code génère donc un modal qui est affiché dès qu'on clique sur un cours grâce à la méthode `showEventDetails`
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
<br/><br/>

![modalDetailsCours](http://testsymfonyvues.fxcj3275.odns.fr/imagesReadme/detailCoursModal.PNG)


## Améliorations apportées 


<br/><br/>

### Intégration de "Note ton prof!"

Nous avons intégré le travail réalisé durant le module mais uniquement sur la page permetant de consulter l'EDT du jour. Etant donné que ce serait un peu stupide d'afficher à chaque fois tous les enseignants nous n'affichons que les enseignants qui ont un cours durant le jour sélectionné.
![notetonprof](http://testsymfonyvues.fxcj3275.odns.fr/imagesReadme/notetonprrof.PNG)

<br/><br/>

Nous avons également modifié l'interface pour consulter les avis et pour donner son avis sur un enseignant. 
![noter](http://testsymfonyvues.fxcj3275.odns.fr/imagesReadme/donnercoms.PNG)
![comms](http://testsymfonyvues.fxcj3275.odns.fr/imagesReadme/commss.PNG)
<br/><br/>

### Création d'une page d'accueil

Afin d'avoir une page centrale lorsqu'un utilisateur arrive sur le site nous avons créé une page d'accueil qui centralise les lien utiles et qui propore également les derniers articles de l'IUT.
![home](http://testsymfonyvues.fxcj3275.odns.fr/imagesReadme/home.PNG)
<br/><br/>

### Récupération des articles de l'IUT de puis le flux RSS de l'IUT
Pour afficher les dernis articles de l'IUT nous avons utilisé le flux RSS de l'IUT mais nous avons rencontré quelque problèmes car il renvoie du HTML déjà formaté, chose que nous ne voulions pas. Nous avons donc fait un traitement avant d'afficher la page pour récupérer uniquement les informations que nous voulions sans balise html.

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
<br/><br/>

### Emplois du temps de la semaine 

Nous pensions qu'il était important de proposer un affichage de l'EDT par semaine. Pour se faire nous avons créer une 
nouvelle page similaire à la page pour l'EDT du jour. Nous avons retiré la partie note ton prof et avons changé les 
attribut du `<v-calendar>` pour avoir un affichage par semaine. Le fonctionnement des détails des cours est identique.

![home](http://testsymfonyvues.fxcj3275.odns.fr/imagesReadme/edtSemaine.PNG)
<br/><br/>

### Emplois du temps des salles


![home](http://testsymfonyvues.fxcj3275.odns.fr/imagesReadme/edtSemaine.PNG)
<br/><br/>

### Exportation des calendriers au format ICalendar


![home](http://testsymfonyvues.fxcj3275.odns.fr/imagesReadme/icalendar.PNG)
<br/><br/>

### Skeleton loaders


<br/><br/>

### Indicateur d'heure

![home](http://testsymfonyvues.fxcj3275.odns.fr/imagesReadme/indicateurHeure.PNG)
<br/><br/>

