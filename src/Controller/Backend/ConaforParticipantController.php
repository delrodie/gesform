<?php

namespace App\Controller\Backend;

use App\Repository\ActiviteRepository;
use App\Repository\CandidaterRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/conafor/participant")
 */
class ConaforParticipantController extends AbstractController
{
	private $activiteRepository;
	private $candidaterRepository;
	
	public function __construct(ActiviteRepository $activiteRepository, CandidaterRepository $candidaterRepository)
	{
		$this->activiteRepository = $activiteRepository;
		$this->candidaterRepository = $candidaterRepository;
	}
	
    /**
     * @Route("/", name="conafor_participant_index")
     */
    public function index(Request $request): Response
    {
        return $this->render('conafor_participant/index.html.twig', [
            'activites' => $this->activiteRepository->findBy([],['id'=>"DESC"]),
	        'participants' => $this->candidaterRepository->findParticipantByActiviteOrNo()
        ]);
    }
}
