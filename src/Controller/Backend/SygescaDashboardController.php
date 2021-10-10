<?php

namespace App\Controller\Backend;

use App\Utilities\GestionCotisation;
use App\Utilities\GestionScout;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/sygesca")
 */
class SygescaDashboardController extends AbstractController
{
	private $_cotisation;
	private $_scout;
	
	public function __construct(GestionCotisation $_cotisation, GestionScout $_scout)
	{
		$this->_cotisation = $_cotisation;
		$this->_scout = $_scout;
	}
	
    /**
     * @Route("/", name="sygesca_dashboard")
     */
    public function index(): Response
    {
		$annee = $this->_cotisation->annee();
		
        return $this->render('sygesca_dashboard/index.html.twig', [
	        'regions' => $this->_cotisation->statistiquesRegion($annee),
	        'branche' => $this->_scout->branche($annee,'Jeune'),
        ]);
    }
}
