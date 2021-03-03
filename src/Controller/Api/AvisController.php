<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Professeur;
use App\Entity\Avis;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @Route("/api/avis", name="api_avis_")
 */
class AvisController extends AbstractController {
    
    /**
     * @Route("/{id}", name="index", methods={"GET"})
     */
    public function index(Professeur $professeur = null): JsonResponse {
        if (!$professeur) {
            return $this->json([
                'message' => 'Ce professeur est introuvable'
            ], 404);
        }

        $avis = $professeur->getAvis()->toArray();

        return $this->json($avis, 200);
    }

    /**
     * @Route("/{id}", name="create", methods={"POST"})
     */
    public function create(Request $request, Professeur $professeur = null, ValidatorInterface $validator, EntityManagerInterface $entityManager): JsonResponse {
        if (!$professeur) {
            return $this->json([
                'message' => 'Ce professeur est introuvable'
            ], 404);
        }

        $data = json_decode($request->getContent(), true);
        $data['professeur'] = $professeur;

        $avis = new Avis($data);

        $errors = $validator->validate($avis);

        if ($errors->count() > 0) {
            return $this->json($this->formatErrors($errors), 400);
        }

        $entityManager->persist($avis);
        $entityManager->flush();

        return $this->json($avis, 201);
    }

    /**
     * @Route("/{id}", name="update", methods={"PATCH"})
     */
    public function update(Request $request, Avis $avis = null, ValidatorInterface $validator, EntityManagerInterface $entityManager): JsonResponse {
        if (!$avis) {
            return $this->json([
                'message' => 'Cet avis est introuvable'
            ], 404);
        }

        $data = json_decode($request->getContent(), true);

        $errors = $avis->updateFromArray($data);

        if (count($errors) > 0) {
            return $this->json($this->formatInputErrors($errors), 400);
        }

        $errors = $validator->validate($avis);

        if ($errors->count() > 0) {
            return $this->json($this->formatErrors($errors), 400);
        }

        $entityManager->persist($avis);
        $entityManager->flush();

        return $this->json($avis, 200);
    }

    /**
     * @Route("/{id}", name="create", methods={"DELETE"})
     */
    public function delete(Avis $avis = null, EntityManagerInterface $entityManager): JsonResponse {
        if (!$avis) {
            return $this->json([
                'message' => 'Cet avis est introuvable'
            ], 404);
        }

        $entityManager->remove($avis);
        $entityManager->flush();

        return $this->json(null, 204);
    }
    
    protected function formatErrors($errors) {
        $messages = [];

        foreach ($errors as $error) {
            $messages[$error->getPropertyPath()] = $error->getMessage();
        }

        return $messages;
    }
    
    protected function formatInputErrors($errors) {
        $messages = [];

        foreach ($errors as $attribute) {
            $messages[$attribute] = 'Cet attribut n\'existe pas';
        }

        return $messages;
    }
}
