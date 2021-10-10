<?php

namespace App\Controller\Backend;

use App\Entity\Compte;
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
	    $user = $this->getUser();
	    $role = $user->getRoles();
	    // Si l'utilisateur a pour role 0 User alors verifier si c'est un regional
	    if ($role[0] === 'ROLE_USER'){
		    if ($role[1] === 'ROLE_REGION'){
			    $compte = $this->getDoctrine()->getRepository(Compte::class)->findOneBy(['user'=>$user->getId()]);
			
			    //return $this->redirectToRoute('sygesca_gestion_region',['regionSlug'=>$compte->getRegion()->getSlug()]);
			    return $this->render('sygesca_dashboard/region.html.twig',[
				    'regions' => $this->_cotisation->statistiquesRegion($annee, $compte->getRegion()->getId()),
				    'branche' => $this->_scout->branche($annee,'Jeune',null,$compte->getRegion()->getId())
			    ]);
		    }
	    }
		
        return $this->render('sygesca_dashboard/index.html.twig', [
	        'regions' => $this->_cotisation->statistiquesRegion($annee),
	        'branche' => $this->_scout->branche($annee,'Jeune'),
        ]);
    }
}
