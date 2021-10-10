<?php

namespace App\Controller\Backend;

use App\Repository\RegionRepository;
use App\Utilities\GestionCotisation;
use App\Utilities\GestionScout;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * @Route("/sygesca/gestion")
 */
class SygescaGestionController extends AbstractController
{
	private $_scout;
	private $_cotisation;
	private $regionRepository;
	
	public function __construct(GestionScout $_scout, GestionCotisation $_cotisation, RegionRepository $regionRepository)
	{
		$this->_scout = $_scout;
		$this->_cotisation = $_cotisation;
		$this->regionRepository = $regionRepository;
	}
	
    /**
     * @Route("/", name="sygesca_gestion_liste", methods={"GET","POST"})
     */
    public function index(Request $request): Response
    {
		$region = null;
	    // Request
	    $regionID = $request->get('region'); //dd($regionID);
	
	    if ($regionID){
		    $region = $this->regionRepository->findOneBy(['id'=>$regionID]);
		    $template = 'sygesca_gestion/region.html.twig';
	    }else{
		    $template = 'sygesca_gestion/index.html.twig';
	    }
	
	    return $this->render($template, [
		    'regions' => $this->regionRepository->findAll(),
		    'region' => $region
	    ]);
    }
	/**
	 * @Route("/ajax", name="sygesca_gestion_ajax", methods={"GET","POSt"})
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
		$scouts = $this->_scout->getListScout($annee,$region,$district);
		
		return $this->json($scouts);
		
	}
}
