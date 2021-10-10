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
		
		// verifier si le scout a deja postuler a un camp; Si oui voir a quel niveau
	    $existenceCandidat = $this->_candidature->existenceCandidat($scout->getMatricule());
		if ($existenceCandidat){
			return $this->redirectToRoute($existenceCandidat['route'],['slug'=>$existenceCandidat['slug']]);
		}
	
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
			$data=[
				'type' => "STAGIAIRE",
				'flag' => 3
			];
			$personnelle = $this->_candidature->formation($request, $candidat,$data);
		}
		
		$formations = $this->formationRepository->findBy(['candidat'=>$candidat->getId(), 'type'=>"STAGIAIRE"], ['id'=>"DESC"]);
		
		return $this->render('inscription/stagiaire.html.twig',[
			'candidat' => $candidat,
			'formations' => $formations
		]);
	}
	
	/**
	 * @Route("/formation/{slug}/formateur", name="app_inscription_formation_formateur", methods={"GET","POST"})
	 */
	public function formateur(Request $request, $slug)
	{
		$candidat = $this->getDoctrine()->getRepository(Candidat::class)->findOneBy(['slug'=>$slug]);
		
		if ($request->get('scout_slug') === $slug){
			$data=[
				'type' => "FORMATEUR",
				'flag' => 4
			];
			$personnelle = $this->_candidature->formation($request, $candidat,$data);
		}
		
		$formations = $this->formationRepository->findBy(['candidat'=>$candidat->getId(), 'type'=>"FORMATEUR"], ['id'=>"DESC"]);
		
		return $this->render('inscription/formateur.html.twig',[
			'candidat' => $candidat,
			'formations' => $formations
		]);
	}
}
