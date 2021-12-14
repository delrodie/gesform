<?php

namespace App\Controller\Backend;

use App\Entity\Activite;
use App\Entity\Candidater;
use App\Entity\Formation;
use App\Repository\CandidaterRepository;
use App\Utilities\GestionMail;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/conafor/candidat")
 */
class ConaforCandidatController extends AbstractController
{
	private $_mail;
	
	public function __construct(GestionMail $_mail)
	{
		$this->_mail = $_mail;
	}
	
    /**
     * @Route("/", name="conafor_candidat_index", methods={"GET","POST"})
     */
    public function index(Request $request, CandidaterRepository $candidaterRepository): Response
    {
		//dd($request);
		$req = $request->get('search_activite');
		$req_validation = $request->get('search_validation');
		if (!$req_validation) $validation = false;
		else $validation = 1;
		if ($req){ //dd($validation);
			$candidaters = $candidaterRepository->findByValidationAndActiviteOrNo($validation, $req);
		}else{
			$candidaters = $candidaterRepository->findByValidationAndActiviteOrNo();
		}
		//dd($candidaters);
        return $this->render('conafor_candidat/index.html.twig', [
            'activites' => $this->getDoctrine()->getRepository(Activite::class)->findBy([],['id'=>"DESC"]),
	        'candidaters' => $candidaters
        ]);
    }
	
	/**
	 * @Route("/{id}", name="conafor_candidat_show", methods={"GET","POST"})
	 */
	public function show(Request $request, $id, CandidaterRepository $candidaterRepository)
	{
		$candidater = $candidaterRepository->findOneById($id); //dd($candidater);
		
		return $this->render('conafor_candidat/edit.html.twig',[
			'candidater' => $candidater,
			'formations' => $this->getDoctrine()->getRepository(Formation::class)->findBy(['candidat'=>$candidater->getCandidat()->getId()],['date'=>"DESC"])
		]);
	}
	
	/**
	 * @Route("/{id}/{mention}", name="confor_candidat_edit", methods={"GET","POST"})
	 */
	public function edit(Request $request, $id, $mention, CandidaterRepository $candidaterRepository, EntityManagerInterface $entityManager)
	{
		$candidater = $candidaterRepository->findOneById($id);
		if ($mention === 'VALIDER'){
			$candidater->setValidation(true);
			$candidater->setMention($mention);
			$this->_mail->candidature($candidater);
			$this->addFlash('success', "La candidature de ".$candidater->getCandidat()->getNom()." a été validée avec succès.");
		}else{
			$candidater->setMention($mention);
			$candidater->setValidation(false);
			if ($mention==='REJETER')
				$this->addFlash('danger', "La candidature de ".$candidater->getCandidat()->getNom()." a été rejetée avec succès.");
			else
				$this->addFlash('danger', "La candidature de ".$candidater->getCandidat()->getNom()." a été invalidée pour dossier incomplet");
		}
		
		$entityManager->flush();
		
		return $this->redirectToRoute('conafor_candidat_index');
		
	}
}
