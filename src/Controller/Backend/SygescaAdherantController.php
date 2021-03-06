<?php

namespace App\Controller\Backend;

use App\Entity\Adherant;
use App\Entity\Compte;
use App\Entity\Sygesca3\Region;
use App\Utilities\GestionCotisation;
use Doctrine\ORM\EntityManagerInterface;
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
	    $user = $this->getUser();
	    $role = $user->getRoles();
	    // Si l'utilisateur a pour role 0 User alors verifier si c'est un regional
	    if ($role[0] === 'ROLE_USER'){
		    if ($role[1] === 'ROLE_REGION'){
			    $compte = $this->getDoctrine()->getRepository(Compte::class)->findOneBy(['user'=>$user->getId()]);
						
			    //return $this->redirectToRoute('sygesca_gestion_region',['regionSlug'=>$compte->getRegion()->getSlug()]);
			    return $this->render('sygesca_adherant/region.html.twig',[
				    'adherants' => $this->_cotisation->listeAdherants($annee, $compte->getRegion()->getId()),
				    'region' => $this->getDoctrine()->getRepository(Region::class)->findOneBy(['id'=>$compte->getRegion()->getId()])
			    ]);
		    }
	    }
		
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
		$scouts = $this->_cotisation->listeAdherants($annee,$region);
		
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
					'message' => "Cet adherant a d??j?? ??t?? valid?? dans le syst??me",
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
	
	/**
	 * @Route("/{idTransaction}/delete/", name="sygesca_adherant_delete", methods={"GET","POST"})
	 */
	public function delete($idTransaction, EntityManagerInterface $entityManager)
	{
		$adherant = $entityManager->getRepository(Adherant::class)->findOneBy(['idtransaction'=>$idTransaction]);
		if ($adherant){
			if ($adherant->getStatuspaiement() === 'UNKNOW'){
				$entityManager->remove($adherant);
				$entityManager->flush();
				
				$this->addFlash('success', "Les informations de l'adh??rent ".$adherant->getNom().' '.$adherant->getPrenoms()." ont ??t?? r??initialis??es avec succ??s!");
				
			}else{
				$this->addFlash('warning', "Impossible de r??initialiser les informations de l'adh??rent ".$adherant->getNom().' '.$adherant->getPrenoms(). ". Merci de contacter les administrateurs");
			}
		}else{
			$this->addFlash('danger', "Cet adh??rent n'existe pas dans le syst??me!");
		}
		
		return $this->redirectToRoute('sygesca_adherant_index', [], Response::HTTP_SEE_OTHER);
	}
	
	/**
	 * @param $adherant
	 * @return mixed
	 */
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
