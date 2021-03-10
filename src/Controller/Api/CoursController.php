<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

use App\Repository\CoursRepository;

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

    /**
     * @Route("/{date}", name="showDaily", methods={"GET"})
     */
    public function showDaily($date, CoursRepository $coursRepository): JsonResponse {
        $dateCours = \DateTime::createFromFormat('Y-m-d', $date);

        if (!$dateCours) {
            return $this->json([
                'message' => 'Le format de la date est invalide. Format accepté: AAAA-MM-JJ'
            ], 404);
        }
        
        $cours = $coursRepository->findByDate($dateCours);

        return $this->json($cours, 200);
    }

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

}
