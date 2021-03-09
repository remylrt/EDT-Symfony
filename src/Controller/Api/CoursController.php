<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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

}
