<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AgendaController extends AbstractController
{
    /**
     * @Route("/agenda", name="agenda")
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
}
