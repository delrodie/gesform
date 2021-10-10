<?php

namespace App\Controller\Backend;

use App\Repository\RegionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
/**
 * @Route("/conafor/region")
 */
class ConaforRegionController extends AbstractController
{
    /**
     * @Route("/", name="conafor_region_index")
     */
    public function index(RegionRepository $regionRepository): Response
    {
        return $this->render('conafor_region/index.html.twig', [
            'regions' => $regionRepository->findAll(),
        ]);
    }
}
