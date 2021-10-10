<?php
	
	namespace App\Controller;
	
	use App\Entity\Adherant;
	use App\Entity\Compteur;
	use App\Entity\Membre;
	use App\Entity\Sygesca3\Region;
	use App\Entity\Sygesca3\Scout;
	use App\Utilities\GestionAdhesion;
	use App\Utilities\GestionCotisation;
	use App\Utilities\GestionScout;
	use Cocur\Slugify\Slugify;
	use Doctrine\ORM\EntityManagerInterface;
	use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\Routing\Annotation\Route;
	use Symfony\Component\Serializer\Encoder\JsonEncoder;
	use Symfony\Component\Serializer\Encoder\XmlEncoder;
	use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
	use Symfony\Component\Serializer\Serializer;
	
	/**
	 * @Route("/cinetpay")
	 */
	class CinetpayController extends AbstractController
	{
		private $_adhesion;
		private $em;
		private $_scout;
		private $_cotisation;
		
		public function __construct(GestionAdhesion $_adhesion, EntityManagerInterface $em, GestionScout $_scout, GestionCotisation $_cotisation)
		{
			$this->_adhesion = $_adhesion;
			$this->em = $em;
			$this->_scout = $_scout;
			$this->_cotisation = $_cotisation;
		}
		
		/**
		 * @Route("/", name="cinetpay_paiement", methods={"GET","POST"})
		 */
		public function paiement(Request $request)
		{
			//Initialisation
			$encoders = [new XmlEncoder(), new JsonEncoder()];
			$normalizers = [new ObjectNormalizer()];
			$serializer = new Serializer($normalizers, $encoders);
			
			// Recuperation des données transmises
			$data=[
				'token' => $request->get('token'),
				'adherant' => $request->get('adherant'),
				'response_id' => $request->get('api_response_id'),
				'url' => $request->get('url')
			];
			
			$result = $this->_adhesion->cinetpay($data);
			
			return $this->json($result);
		}
		
		/**
		 * @Route("/notify", name="cinetpay_notify", methods={"GET","POST"})
		 */
		public function notify(Request $request)
		{
			//Initialisation
			$encoders = [new XmlEncoder(), new JsonEncoder()];
			$normalizers = [new ObjectNormalizer()];
			$serializer = new Serializer($normalizers, $encoders);
			
			$cpmTransId = $request->get('cpm_trans_id');
			
			if (isset($cpmTransId)){
				try {
					$url = 'https://api-checkout.cinetpay.com/v2/payment/check';
					$apiKey = '18714242495c8ba3f4cf6068.77597603';
					$site_id = 422630;
					$plateform = "PROD"; // Valorisé à PROD si vous êtes en production
					
					// Verification du statut de l'adherant
					$adherant = $this->em->getRepository(Adherant::class)->findOneBy(['idtransaction'=>$cpmTransId]); //dd($adherant);
					if ($adherant){
						if ($adherant->getStatuspaiement() === 'VALID'){
							$data = [
								'status' => false,
								'matricule' => $adherant->getMatricule()
							];
						}else{
							$data = [
								'apikey' => $apiKey,
								'site_id' => $site_id,
								'token' => $adherant->getToken()
							];
							
							// Creation d'option
							$options = [
								'http' => [
									'method' =>"POST",
									'header' => "Content-Type: application/json\r\n",
									//'ignore_errors' => true,
									'content' => json_encode($data)
								]
							]; //dd($options);
							
							// Creation du context
							$context = stream_context_create($options); //dd($context);
							
							// Execution de la requete
							$result =  file_get_contents('https://api-checkout.cinetpay.com/v2/payment/check', false, $context);
							
							$donnee = json_decode($result);
							if ($donnee->code === '00'){
								
								// Verification du statut de l'adherant selon sa fonction
								$status = $this->_adhesion->statutAdherant($adherant);
								
								// SI le membre existe alors faire une mise a jour
								// sinon crée le nouveau membre
								$membre = $this->membre($adherant,$status);
								$this->_scout->carte($membre['scout'], $membre['region_code'], $membre['id']);
								$this->_cotisation->save($membre['scout'], $status['fonction']);
							}
						}
					}
					
				} catch (\Exception $e){
					echo "Erreur :". $e->getMessage();
					$this->addFlash('danger', "Erreur : ".$e->getMessage());
				}
			}
			
			return $this->json($data);
			
		}
		
		protected function membre(object $adherant, $statut)
		{
			$scout = $this->getDoctrine()->getRepository(Membre::class)->findOneBy(['matricule'=>$adherant->getMatricule()]);
			$region = $this->getDoctrine()->getRepository(Region::class)->findOneBy(['id' => $adherant->getGroupe()->getDistrict()->getRegion()]);
			if (!$adherant->getMatricule()){ //die('ici');
				$scout = new Membre();
				
				
				// Nombre de scout
				$compteur = $this->getDoctrine()->getRepository(Compteur::class)->findOneBy([],['id'=>'DESC'],1);
				if (!$compteur){
					$nombre = count($this->getDoctrine()->getRepository(Scout::class, 'sygesca')->findAll()) + 1;
					$compteur = new Compteur();
					$compteur->setNombre((int) $nombre);
					$this->em->persist($compteur);
					$this->em->flush();
				}else{
					$nombre = (int) $compteur->getNombre() + 1;
					$compteur->setNombre($nombre);
					$this->em->flush();
				}
				$id = $nombre;
			}elseif (!$scout){
				$scout = new Membre();
				$slug = $adherant->getSlug();
				$matricule = $adherant->getMatricule();
				$id = $adherant->getOldId();
			}else{
				$slug = $scout->getSlug();
				$matricule = $scout->getMatricule();
				$id = $adherant->getOldId();
			}
			$scout->setSlug($adherant->getSlug());
			$scout->setMatricule($adherant->getMatricule());
			$scout->setNom($adherant->getNom());
			$scout->setPrenoms($adherant->getPrenoms());
			$scout->setDateNaissance($adherant->getDateNaissance());
			$scout->setLieuNaissance($adherant->getLieuNaissance());
			$scout->setSexe($adherant->getSexe());
			$scout->setContact($adherant->getContact());
			$scout->setUrgence($adherant->getUrgence());
			$scout->setContactUrgence($adherant->getContactUrgence());
			$scout->setBranche($statut['branche']);
			$scout->setFonction($adherant->getFonction());
			$scout->setGroupe($adherant->getGroupe());
			$scout->setStatut($statut['statut_scout']->getId()); dd($scout);
			
			$this->em->persist($scout);
			$this->em->flush();
			
			return $data = [
				'scout' => $scout,
				'id' => $id,
				'region_code' => $region->getCode()
			];
		}
	}
