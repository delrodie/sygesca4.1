<?php
	
	namespace App\Utilities;
	
	use App\Entity\Adherant;
	use App\Entity\Anomalie;
	use App\Entity\Sygesca3\Fonctions;
	use App\Entity\Sygesca3\Groupe;
	use App\Entity\Sygesca3\Statut;
	use Cocur\Slugify\Slugify;
	use Doctrine\ORM\EntityManagerInterface;
	
	class GestionAdhesion
	{
		private $em;
		private $_scout;
		
		public function __construct(EntityManagerInterface $em, GestionScout $_scout)
		{
			$this->em = $em;
			$this->_scout = $_scout;
		}
		
		public function formulaire($request)
		{
			$adherant = [
				'nom' => strtoupper($this->validForm($request->get('scout_nom'))),
				'prenoms' => strtoupper($this->validForm($request->get('scout_prenoms'))),
				'date_naissance' => $this->validForm($request->get('scout_date_naissance')),
				'lieu_naissance' => $this->validForm($request->get('scout_lieu_naissance')),
				'sexe' => $this->validForm($request->get('scout_sexe')),
				'contact' => $this->validForm($request->get('scout_contact')),
				'urgence' => $this->validForm($request->get('scout_urgence')),
				'contact_urgence' => $this->validForm($request->get('scout_contact_urgence')),
				'old_id' => $this->validForm($request->get('scout_id')),
				'matricule' => $this->validForm($request->get('scout_matricule')),
				'fonction' => $this->em->getRepository(Fonctions::class)->findOneBy(['id'=>$this->validForm($request->get('scout_fonction'))]),
				'groupe' => $this->em->getRepository(Groupe::class)->findOneBy(['id'=>$this->validForm($request->get('scout_groupe'))]),
				'slug' => $this->validForm($request->get('scout_slug')),
				'branche' => $this->validForm($request->get('scout_branche')),
			];
			
			// Verification de l'existence du matricule sinon en generer
			if (!$adherant['matricule'])
				$matricule = $this->_scout->matricule($adherant['groupe']->getDistrict()->getRegion()->getId());
			else
				$matricule = $adherant['matricule'];
			
			if (!$adherant['slug']) {
				$slugify = new Slugify();
				$slug = $slugify->slugify($adherant['nom'].'-'.$adherant['prenoms'].'-'.$matricule);
			}else
				$slug = $adherant['slug'];
			
			// Verification de l'existence de l'adherant dans le système
			/*$verifAdherant = $this->em->getRepository(Adherant::class)->findOneBy([
				'nom' => $adherant['nom'],
				'prenoms' => $adherant['prenoms'],
				'dateNaissance' => $adherant['date_naissance'],
				'lieuNaissance' => $adherant['lieu_naissance'],
				'contact' => $adherant['contact'],
				'contactUrgence' => $adherant['contact_urgence']
			]);*/
			$verifAdherant = $this->em->getRepository(Adherant::class)->findByForm($adherant);
			
			$id_transaction = time().''.substr(uniqid("",true), -9, 9);
			$status_paiement = "UNKNOW";
			
			// Si l'adherent n'existe pas alors enregistrer le dans le système
			// Sinon la mise a jour de ses informations
			if (!$verifAdherant){
				$scout = new Adherant();
				$scout->setMatricule($matricule);
				$scout->setNom($adherant['nom']);
				$scout->setPrenoms($adherant['prenoms']);
				$scout->setDateNaissance($adherant['date_naissance']);
				$scout->setLieuNaissance($adherant['lieu_naissance']);
				$scout->setSexe($adherant['sexe']);
				$scout->setContact($adherant['contact']);
				$scout->setUrgence($adherant['urgence']);
				$scout->setContactUrgence($adherant['contact_urgence']);
				$scout->setBranche($adherant['branche']);
				$scout->setFonction($adherant['fonction']->getLibelle());
				$scout->setSlug($slug);
				$scout->setOldId($adherant['old_id']);
				$scout->setIdTransaction($id_transaction);
				$scout->setStatusPaiement($status_paiement);
				$scout->setGroupe($adherant['groupe']); //dd($scout);
				
				$this->em->persist($scout);
				$this->em->flush();
				
				
				
				$montant = $adherant['fonction']->getMontant();
				$am = (int) $montant/(1 - 0.035);
				$am = $this->arrondiSuperieur($am, 5);
				
				$data = [
					'transaction_id' => $id_transaction,
					'montant' => $am,
					'matricule' => $scout->getMatricule(),
					'nom' => $scout->getNom(),
					'prenom' => $scout->getPrenoms(),
					'id' => $scout->getId(),
					'region' => $adherant['groupe']->getDistrict()->getRegion()->getNom(),
					'description' => "Adhesion effective, votre matricule: ".$scout->getMatricule(),
				];
			}elseif($verifAdherant->getStatusPaiement()!== 'VALID'){
				
				$montant = $adherant['fonction']->getMontant();
				$am = (int) $montant/(1 - 0.035);
				$am = $this->arrondiSuperieur($am, 5);
				
				$data = [
					'transaction_id' => $verifAdherant->getIdTransaction(),
					'montant' => $am,
					'matricule' => $verifAdherant->getMatricule(),
					'nom' => $verifAdherant->getNom(),
					'prenom' => $verifAdherant->getPrenoms(),
					'id' => $verifAdherant->getId(),
					'region' => $adherant['groupe']->getDistrict()->getRegion()->getNom(),
					'description' => "Adhesion effective, votre matricule: ".$verifAdherant->getMatricule(),
				];
			}else{
				$data=[
					'code' => 1,
					'matricule' => $verifAdherant->getMatricule()
				];
			}
			
			return $data;
		}
		
		/**
		 * Mise a jour de la table adherant avec données cinetpay
		 *
		 * @param $data
		 * @return Adherant|mixed|object|null
		 */
		public function cinetpay($data)
		{
			$adherant = $this->em->getRepository(Adherant::class)->findOneBy(['id'=>$data['adherant']]);
			$adherant->setUrl($data['url']);
			$adherant->setToken($data['token']);
			$adherant->setResponseId($data['response_id']);
			$this->em->flush();
			
			return $adherant;
		}
		
		/**
		 * Determination du statut de l'adherant
		 *
		 * @param $adherant
		 * @return array
		 */
		public function statutAdherant($adherant): array
		{
			$fonction = $this->em->getRepository(Fonctions::class)->findOneBy(['libelle'=>$adherant->getFonction()]);
			//dd($fonction);
			if ($fonction->getId() < 5){
				$branche = $fonction->getLibelle();
				$scout_statut = $this->em->getRepository(Statut::class)->findOneBy(['id'=>1]);
			}else{
				$branche = $adherant->getBranche();
				$scout_statut = $this->em->getRepository(Statut::class)->findOneBy(['id'=>2]);
			}
			
			$data = [
				'branche' => $branche,
				'scout_statut' => $scout_statut,
				'fonction' => $fonction
			];
			
			return $data;
		}
		
		public function errorCinetpay($donne)
		{
			$adherant = $this->em->getRepository(Anomalie::class)->findOneBy(['adherant'=>$donne->data->metadata]);
			if ($adherant){
				$anomalie = new Anomalie();
				$anomalie->setCode($donne->code);
				$anomalie->setMessage($donne->message);
				$anomalie->setMontant($donne->data->amount);
				$anomalie->setStatus($donne->data->status);
				$anomalie->setDescription($donne->data->description);
				$anomalie->setAdherant($adherant);
				$anomalie->setPaiementDate($donne->data->payment_date);
				$anomalie->setResponseId($donne->api_response_id);
				
				$this->em->persist($anomalie);
				$this->em->flush();
			}
			
			return true;
		}
		
		/**
		 * Fonction pour arrondir au supérieur
		 *
		 * @param $nombre
		 * @param $arrondi
		 * @return float|int
		 */
		public function arrondiSuperieur($nombre, $arrondi)
		{
			return ceil($nombre / $arrondi) * $arrondi;
		}
		
		/**
		 * fonction verification des valeurs
		 *
		 * @param $donnee
		 * @return string
		 */
		public function validForm($donnee)
		{
			$result = htmlspecialchars(stripslashes(trim($donnee)));
			
			return $result;
		}
	}
