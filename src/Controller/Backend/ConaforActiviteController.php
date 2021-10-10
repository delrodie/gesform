<?php

namespace App\Controller\Backend;

use App\Entity\Activite;
use App\Form\ActiviteType;
use App\Repository\ActiviteRepository;
use Cocur\Slugify\Slugify;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/conafor/activite")
 */
class ConaforActiviteController extends AbstractController
{
    /**
     * @Route("/", name="conafor_activite_index", methods={"GET","POST"})
     */
    public function index(Request $request, EntityManagerInterface $entityManager, ActiviteRepository $activiteRepository): Response
    {
	    $activite = new Activite();
	    $form = $this->createForm(ActiviteType::class, $activite);
	    $form->handleRequest($request);
	
	    if ($form->isSubmitted() && $form->isValid()) {
			$slugify = new Slugify();
			$slug = $slugify->slugify($activite->getNom().'-'.$activite->getCode());
			$activite->setSlug($slug);
			$activite->setNom(strtoupper($activite->getNom()));
		    $entityManager->persist($activite);
		    $entityManager->flush();
			
			$this->addFlash('success', "l'Activité a été ajoutée avec succès");
		
		    return $this->redirectToRoute('conafor_activite_index', [], Response::HTTP_SEE_OTHER);
	    }
		
        return $this->renderForm('conafor_activite/index.html.twig', [
            'activites' => $activiteRepository->findBy([],['id'=>"DESC"]),
	        'activite' => $activite,
	        'form' => $form,
        ]);
    }

    /**
     * @Route("/new", name="conafor_activite_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $activite = new Activite();
        $form = $this->createForm(ActiviteType::class, $activite);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($activite);
            $entityManager->flush();

            return $this->redirectToRoute('conafor_activite_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('conafor_activite/new.html.twig', [
            'activite' => $activite,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="conafor_activite_show", methods={"GET"})
     */
    public function show(Activite $activite): Response
    {
        return $this->render('conafor_activite/show.html.twig', [
            'activite' => $activite,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="conafor_activite_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Activite $activite, EntityManagerInterface $entityManager, ActiviteRepository $activiteRepository): Response
    {
        $form = $this->createForm(ActiviteType::class, $activite);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
	        $slugify = new Slugify();
	        $slug = $slugify->slugify($activite->getNom().'-'.$activite->getCode());
	        $activite->setSlug($slug);
	        $activite->setNom(strtoupper($activite->getNom()));
            $entityManager->flush();
	
	        $this->addFlash('success', "l'Activité a été modifée avec succès");

            return $this->redirectToRoute('conafor_activite_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('conafor_activite/edit.html.twig', [
            'activite' => $activite,
            'form' => $form,
	        'activites' => $activiteRepository->findBy([],['id'=>"DESC"]),
        ]);
    }

    /**
     * @Route("/{id}", name="conafor_activite_delete", methods={"POST"})
     */
    public function delete(Request $request, Activite $activite, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$activite->getId(), $request->request->get('_token'))) {
            $entityManager->remove($activite);
            $entityManager->flush();
        }

        return $this->redirectToRoute('conafor_activite_index', [], Response::HTTP_SEE_OTHER);
    }
}
