<?php

namespace App\Controller;

use App\Entity\Candidat;
use App\Entity\Formation;
use App\Entity\Sygesca\Membre;
use App\Repository\FormationRepository;
use App\Utilities\GestionCandidature;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/inscription")
 */
class InscriptionController extends AbstractController
{
	private $_candidature;
	private $formationRepository;
	
	public function __construct(GestionCandidature $_candidature, FormationRepository $formationRepository)
	{
		$this->_candidature = $_candidature;
		$this->formationRepository = $formationRepository;
	}
	
    /**
     * @Route("/{slug}", name="app_inscription", methods={"GET","POST"})
     */
    public function index(Request $request, $slug): Response
    {
	    $scout = $this->getDoctrine()->getRepository(Membre::class, 'sygesca')->findOneBy(['slug'=>$slug]);
	
	    if ($request->get('scout_slug') === $slug){
			$candidat = $this->_candidature->formulaire($request, $scout);
			
			return $this->redirectToRoute('app_inscription_personnelle',[
				'slug' => $candidat->getSlug()
			]);
		}
	    return $this->render('inscription/index.html.twig', [
            'scout' => $scout,
        ]);
    }
	
	/**
	 * @Route("/{slug}/s", name="app_inscription_personnelle", methods={"GET","POST"})
	 */
	public function personnelle(Request $request, $slug)
	{
		$candidat = $this->getDoctrine()->getRepository(Candidat::class)->findOneBy(['slug'=>$slug]);
		
		if ($request->get('scout_slug') === $slug){
			$personnelle = $this->_candidature->personnelle($request, $candidat);
			
			return $this->redirectToRoute('app_inscription_formation_stagiaire',[
				'slug'=> $personnelle->getSlug()
			]);
		}
		
		return $this->render('inscription/personne.html.twig',[
			'candidat' => $candidat
		]);
	}
	
	/**
	 * @Route("/formation/{slug}/", name="app_inscription_formation_stagiaire", methods={"GET","POST"})
	 */
	public function stagiaire(Request $request, $slug)
	{
		$candidat = $this->getDoctrine()->getRepository(Candidat::class)->findOneBy(['slug'=>$slug]);
		
		if ($request->get('scout_slug') === $slug){
			$personnelle = $this->_candidature->stagiaire($request, $candidat);
		}
		
		$formations = $this->formationRepository->findBy(['candidat'=>$candidat->getId()], ['id'=>"DESC"]);
		
		return $this->render('inscription/stagiaire.html.twig',[
			'candidat' => $candidat,
			'formations' => $formations
		]);
	}
}
