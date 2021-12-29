<?php

namespace App\Controller\Backend;

use App\Utilities\GestionCotisation;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
/**
 * @Route("/sygesca/telemetrie", name="sygesca_telemetrie_")
 */
class SygescaTelemetrieController extends AbstractController
{
	private $_cotisation;
	
	public function __construct(GestionCotisation $_cotisation)
	{
		$this->_cotisation = $_cotisation;
	}
	
    /**
     * @Route("/", name="index")
     */
    public function index(): Response
    {
		$annee = $this->_cotisation->annee();
        return $this->render('sygesca_telemetrie/index.html.twig', [
            'regions' => $this->_cotisation->statistiquesRegion($annee),
        ]);
    }
}
