<?php

namespace App\Controller;

use App\Repository\SalleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/agenda", name="agenda_")
 */
class AgendaController extends AbstractController
{
    /**
     * @Route("", name="jours")
     */
    public function index(): Response
    {
        $mobileDetector = new \Mobile_Detect();

        if ( $mobileDetector->isMobile() || $mobileDetector->isTablet() ) {
            return $this->render('agenda/mobile_agenda.html.twig', [

            ]);
        }else{
            return $this->render('agenda/agenda.html.twig', [

            ]);
        }
    }

    /**
     * @Route("/semaine", name="semaines")
     */
    public function getSemaine(): Response
    {
        return $this->render('agenda/agenda_semaines.html.twig', [

        ]);

    }

    /**
     * @Route("/salles/{numero}", name="salle")
     */
    public function getAgendaSalle($numero): Response
    {
        return $this->render('agenda/agenda_salle.html.twig', [ "salle" => $numero]);

    }

}
