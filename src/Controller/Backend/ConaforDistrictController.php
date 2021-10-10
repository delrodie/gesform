<?php

namespace App\Controller\Backend;

use App\Entity\Sygesca\District;
use App\Form\Sygesca\DistrictType;
use App\Repository\DistrictRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/conafor/district")
 */
class ConaforDistrictController extends AbstractController
{
    /**
     * @Route("/", name="conafor_district_index", methods={"GET","POST"})
     */
    public function index(Request $request, DistrictRepository $districtRepository, EntityManagerInterface $entityManager): Response
    {
	    $district = new District();
	    $form = $this->createForm(DistrictType::class, $district);
	    $form->handleRequest($request);
	
	    if ($form->isSubmitted() && $form->isValid()) {
		    $entityManager->persist($district);
		    $entityManager->flush();
		
		    return $this->redirectToRoute('conafor_district_index', [], Response::HTTP_SEE_OTHER);
	    }
		
        return $this->renderForm('conafor_district/index.html.twig', [
            'districts' => $districtRepository->findAll(),
	        'district' => $district,
	        'form' => $form,
        ]);
    }

    /**
     * @Route("/new", name="conafor_district_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $district = new District();
        $form = $this->createForm(DistrictType::class, $district);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($district);
            $entityManager->flush();

            return $this->redirectToRoute('conafor_district_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('conafor_district/new.html.twig', [
            'district' => $district,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="conafor_district_show", methods={"GET"})
     */
    public function show(District $district): Response
    {
        return $this->render('conafor_district/show.html.twig', [
            'district' => $district,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="conafor_district_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, District $district, EntityManagerInterface $entityManager, DistrictRepository $districtRepository): Response
    {
        $form = $this->createForm(DistrictType::class, $district);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('conafor_district_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('conafor_district/edit.html.twig', [
            'district' => $district,
            'form' => $form,
	        'districts' => $districtRepository->findAll(),
        ]);
    }

    /**
     * @Route("/{id}", name="conafor_district_delete", methods={"POST"})
     */
    public function delete(Request $request, District $district, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$district->getId(), $request->request->get('_token'))) {
            $entityManager->remove($district);
            $entityManager->flush();
        }

        return $this->redirectToRoute('conafor_district_index', [], Response::HTTP_SEE_OTHER);
    }
}
