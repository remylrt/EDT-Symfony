<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

use App\Repository\SalleRepository;

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

    /**
     * @Route("/{id}", name="show", methods={"GET"})
     */
    public function show($id, SalleRepository $salleRepository): JsonResponse {
        $salles = $salleRepository->find($id);

        return $this->json($salles, 200);
    }

}
