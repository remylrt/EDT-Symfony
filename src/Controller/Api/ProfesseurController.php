<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Professeur;
use App\Repository\ProfesseurRepository;

/**
 * @Route("/api/professeurs", name="api_professeurs_")
 */
class ProfesseurController extends AbstractController {

    /**
     * @Route("", name="index", methods={"GET"})
     */
    public function index(ProfesseurRepository $professeurRepository): JsonResponse {
        $professeurs = $professeurRepository->findAll();

        return $this->json($professeurs, 200);
    }

    /**
     * @Route("/{id}", name="show", methods={"GET"})
     */
    public function show(Professeur $professeur = null): JsonResponse {
        if (!$professeur) {
            return $this->json([
                'message' => 'Ce professeur est introuvable'
            ], 404);
        }

        return $this->json($professeur, 200);
    }

    /**
     * @Route("/daily/{date}", name="show", methods={"GET"})
     */
    public function showDaily($date, ProfesseurRepository $professeurRepository): JsonResponse {
        $dateCours = \DateTime::createFromFormat('Y-m-d', $date);

        if (!$dateCours) {
            return $this->json([
                'message' => 'Le format de la date est invalide. Format accepté: AAAA-MM-JJ'
            ], 404);
        }

        $professeurs = $professeurRepository->findByDateCours($dateCours);

        return $this->json($professeurs, 200);
    }
}
