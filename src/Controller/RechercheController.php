<?php

namespace App\Controller;

use App\Entity\Sygesca3\District;
use App\Entity\Sygesca3\Fonctions;
use App\Entity\Sygesca3\Region;
use App\Entity\Sygesca3\Scout;
use App\Utilities\GestionScout;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/recherche")
 */
class RechercheController extends AbstractController
{
	private $_scout;
	
	public function __construct(GestionScout $_scout)
	{
		$this->_scout = $_scout;
	}
	
	/**
	 * @Route("/", name="nouveau_scout")
	 */
	public function nouveau()
	{
		return $this->render('recherche/nouveau.html.twig',[
			'regions' => $this->getDoctrine()->getRepository(Region::class)->findAll(),
			'fonctions' => $this->getDoctrine()->getRepository(Fonctions::class)->findAll()
		]);
	}
	
    /**
     * @Route("/{slug}", name="recherche_matricule")
     */
    public function index(Request $request, $slug): Response
    {
		$scout = $this->getDoctrine()->getRepository(Scout::class, 'sygesca')->findOneBy(['slug'=>$slug]);
        //$district = $this->getDoctrine()->getRepository(District::class)->findByEquipeByRegion(5,'EQUIPE REGIONALE');
		
		return $this->render('recherche/index.html.twig', [
            'scout' => $scout,
	        'fonctions' => $this->_scout->getFonctionByAge($scout->getDatenaiss()),
	        'regions' => $this->getDoctrine()->getRepository(Region::class)->findAll()
        ]);
    }
}
