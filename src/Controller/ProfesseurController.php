<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Professeur;
use App\Repository\ProfesseurRepository;
use App\Form\ProfesseurType;

/**
 * @Route("/professeurs", name="professeurs_")
 */
class ProfesseurController extends AbstractController {

    /**
     * @Route("", name="index", methods={"GET"})
     */
    public function index(ProfesseurRepository $professeurRepository): Response {
        $professeurs = $professeurRepository->findAll();

        return $this->render('professeur/index.html.twig', [
            'professeurs' => $professeurs
        ]);
    }

    /**
     * @Route("/create", name="create", methods={"GET", "POST"})
     */
    public function create(Request $request): Response {
        $professeur = new Professeur();

        $form = $this->createForm(ProfesseurType::class, $professeur);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $professeur = $form->getData();

            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->persist($professeur);
            $entityManager->flush();

            return $this->redirectToRoute('professeurs_index');
        }

        return $this->render('professeur/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/update/{id}", name="update", methods={"GET", "POST"})
     */
    public function update($id, ProfesseurRepository $professeurRepository, Request $request): Response {
        $professeur = $professeurRepository->find($id);

        $form = $this->createForm(ProfesseurType::class, $professeur);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $professeur = $form->getData();

            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->persist($professeur);
            $entityManager->flush();

            return $this->redirectToRoute('professeurs_index');
        }

        return $this->render('professeur/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/delete/{id}", name="delete", methods={"GET"})
     */
    public function delete($id, ProfesseurRepository $professeurRepository): Response {
        $professeur = $professeurRepository->find($id);

        $entityManager = $this->getDoctrine()->getManager();

        $entityManager->remove($professeur);
        $entityManager->flush();

        return $this->redirectToRoute('professeurs_index');
    }
}
