<?php

namespace App\Controller\Backend;

use App\Entity\Adherant;
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
	
	/**
	 * @Route("/show/{idTransaction}", name="sygesca_adherant_show", methods={"GET","POST"})
	 */
	public function show($idTransaction)
	{
		$adherant = $this->getDoctrine()->getRepository(Adherant::class)->findOneBy(['idtransaction'=>$idTransaction]);
		//dd($adherant);
		if ($adherant){
			if ($adherant->getStatuspaiement() === 'VALID'){
				$data = [
					'code' => '101',
					'message' => "Cet adherant a déjà été validé dans le système",
					'data' => [],
					'api_response_id' => '',
				];
			}else{
				$data = $this->apiCinetpay($adherant);
			}
		
		}else{
			$data = [
				'code' => '100',
				'message' => "Cet id de transaction n'existe pas. Merci de contacter les administrateurs",
				'data' => [],
				'api_response_id' => '',
			];
		}
		
		return $this->render('sygesca_adherant/show.html.twig',[
			'data' => $data,
			'adherant' => $adherant
		]);
	}
	
	protected function apiCinetpay($adherant)
	{
		$parametres = [
			'apikey' => '18714242495c8ba3f4cf6068.77597603',
			'site_id' => 422630,
			'token' => $adherant->getToken()
		];
		// Creation d'option
		
		$options = [
			'http' => [
				'method' =>"POST",
				'header' => "Content-Type: application/json\r\n",
				//'ignore_errors' => true,
				'content' => json_encode($parametres)
			]
		]; //dd($options);
		
		// Creation du context
		$context = stream_context_create($options); //dd($context);
		
		// Execution de la requete
		$result =  file_get_contents('https://api-checkout.cinetpay.com/v2/payment/check', false, $context);
		
		$donnee = json_decode($result);
		
		return $donnee;
	}
}
