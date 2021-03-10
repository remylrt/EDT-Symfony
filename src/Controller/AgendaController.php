<?php

namespace App\Controller;

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


    }

    /**
     * @Route("/salles", name="semaines")
     */
    public function getSalles(): Response
    {


    }

    /**
     * @Route("/salles/{id}", name="semaines")
     */
    public function getAgendaSalle($id): Response
    {


    }

}
