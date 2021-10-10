<?php

namespace App\Controller;

use App\Entity\Candidat;
use App\Entity\Sygesca\Membre;
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
	
	public function __construct(GestionCandidature $_candidature)
	{
		$this->_candidature = $_candidature;
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
			dd($personnelle);
		}
		
		return $this->render('inscription/personne.html.twig',[
			'candidat' => $candidat
		]);
	}
}
