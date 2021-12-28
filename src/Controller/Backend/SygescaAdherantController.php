<?php

namespace App\Controller\Backend;

use App\Entity\Sygesca3\Region;
use App\Utilities\GestionCotisation;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * @Route("/sygesca/adherant")
 */
class SygescaAdherantController extends AbstractController
{
	private $_cotisation;
	
	public function __construct(GestionCotisation $_cotisation)
	{
		$this->_cotisation = $_cotisation;
	}
	
    /**
     * @Route("/", name="sygesca_adherant_index", methods={"GET","POST"})
     */
    public function index(Request $request): Response
    {
	    $annee = $this->_cotisation->annee();
	    //$scouts = ;
		
        return $this->render('sygesca_adherant/index.html.twig', [
	        'regions' => $this->getDoctrine()->getRepository(Region::class)->findAll(),
	        'adherants' => $this->_cotisation->listeAdherants($annee)
        ]);
    }
	
	/**
	 * @Route("/ajax", name="sygesca_adherant_ajax", methods={"GET","POST"})
	 */
	public function ajax(Request $request)
	{
		//Initialisation
		$encoders = [new XmlEncoder(), new JsonEncoder()];
		$normalizers = [new ObjectNormalizer()];
		$serializer = new Serializer($normalizers, $encoders);
		
		$region = $request->get('region');
		$district = $request->get('district');
		
		$annee = $this->_cotisation->annee();
		$scouts = $this->_cotisation->listeAdherants($annee);
		
		return $this->json($scouts);
	}
}
