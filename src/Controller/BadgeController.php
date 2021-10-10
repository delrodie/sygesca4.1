<?php

namespace App\Controller;

use App\Entity\Adherant;
use App\Entity\Membre;
use App\Utilities\GestionAdhesion;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/badge")
 */
class BadgeController extends AbstractController
{
	private $_adhesion;
	
	public function __construct(GestionAdhesion $_adhesion)
	{
		
		$this->_adhesion = $_adhesion;
	}
	
    /**
     * @Route("/", name="badge_recherche", methods={"GET","POST"})
     */
    public function index(Request $request): Response
    {
		$token = $request->get('token');
		if ($token){
			$adherant = $this->getDoctrine()->getRepository(Adherant::class)->findOneBy(['token'=>$token]);
			if ($adherant){
				$scout = $this->getDoctrine()->getRepository(Membre::class)->findOneBy(['matricule'=>$adherant->getMatricule()]);
				if ($scout){
					return  $this->render('badge/carte.html.twig',[
						'scout' => $scout
					]);
				}
			}
		}
        return $this->render('badge/index.html.twig', [
            'controller_name' => 'BadgeController',
        ]);
    }
	
	/**
	 * @Route("/recherche", name="badge_requete", methods={"GET","POST"})
	 */
	public function recherche(Request $request)
	{
		$matricule = $this->_adhesion->validForm($request->get('matricule'));
		$scout = $this->getDoctrine()->getRepository(Membre::class)->findOneBy(['matricule'=>$matricule]);
		//dd($scout);
		if (!$scout){
			$this->addFlash('danger', "Oups!! Votre carte provisoire n'est pas encore disponible");
			return $this->redirectToRoute('badge_recherche');
		}
		
		return $this->render('badge/carte.html.twig',[
			'scout' => $scout
		]);
	}
}
