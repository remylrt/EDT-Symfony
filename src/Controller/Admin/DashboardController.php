<?php

namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Professeur;
use App\Entity\Matiere;
use App\Entity\Avis;
use App\Entity\Cours;
use App\Entity\Salle;

class DashboardController extends AbstractDashboardController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        return parent::index();
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Emploi du temps');
    }

    public function configureMenuItems(): iterable
    {
        return [
            MenuItem::linktoDashboard('Dashboard', 'fa fa-home'),
            MenuItem::linkToCrud('Professeurs', 'fas fa-user', Professeur::class),
            MenuItem::linkToCrud('Matières', 'fas fa-chalkboard-teacher', Matiere::class),
            MenuItem::linkToCrud('Avis', 'fa fa-star', Avis::class),
            MenuItem::linkToCrud('Cours', 'fa fa-book-open', Cours::class),
            MenuItem::linkToCrud('Salle', 'fas fa-school', Salle::class),
        ];
    }
}
