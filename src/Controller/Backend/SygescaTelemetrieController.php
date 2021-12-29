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
		
		$objectifs = $this->_cotisation->listeObjectifs();
		$total_inscrit = 0; $total_objectif=0;
		foreach ($objectifs as $objectif){
			$total_inscrit = $total_inscrit + $objectif['inscrit'];
			$total_objectif = $total_objectif + $objectif['valeur'];
		}
		$total_pourcentage = round($total_inscrit*100 / $total_objectif, 2);
		
        return $this->render('sygesca_telemetrie/index.html.twig', [
            'regions' => $this->_cotisation->statistiquesRegion($annee),
	        'objectifs' => $objectifs,
	        'total_pourcent' => $total_pourcentage,
	        'total_inscrit' => $total_inscrit,
	        'total_objectif' => $total_objectif
        ]);
    }
}
