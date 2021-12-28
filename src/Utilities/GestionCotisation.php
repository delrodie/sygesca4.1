<?php
	
	namespace App\Utilities;
	
	use App\Entity\Adherant;
	use App\Entity\Cotisation;
	use App\Entity\Sygesca3\District;
	use App\Entity\Sygesca3\Region;
	use App\Repository\CotisationRepository;
	use Doctrine\ORM\EntityManagerInterface;
	
	class GestionCotisation
	{
		private $em;
		private $cotisationRepository;
		
		public function __construct(EntityManagerInterface $em, CotisationRepository $cotisationRepository)
		{
			$this->em = $em;
			$this->cotisationRepository = $cotisationRepository;
		}
		
		/**
		 * @param object $scout
		 * @param object $fonction
		 * @param int|null $montant
		 * @param string|null $phone
		 * @return bool
		 */
		public function save(object $scout, object $fonction, int $montant=null, string $phone=null): bool
		{
			$cotisation = new Cotisation();
			$annee = $this->annee();
			$cotisation->setAnnee($annee);
			$cotisation->setFonction($fonction->getLibelle());
			$cotisation->setCarte($scout->getCarte());
			$cotisation->setMontant($montant);
			$cotisation->setMontantSansFrais($fonction->getMontant());
			$cotisation->setRistourne($fonction->getRistourne());
			$cotisation->setMembre($scout);
			$cotisation->setTelephone($phone);
			
			$scout->setCotisation($annee);
			
			$this->em->persist($cotisation);
			$this->em->flush();
			
			return true;
		}
		
		
		public function statistiquesRegion($annee, $region=null, $district=null): array
		{
			$lists=[];$i=0;
			
			if ($region){
				$resultats = $this->em->getRepository(District::class)->findBy(['region'=>$region],['nom'=>"ASC"]);
				foreach($resultats as $resultat){
					$lists[$i++] = [
						'nom' => $resultat->getNom(),
						'total' => count($this->cotisationRepository->findList($annee,null, $resultat->getId())),
						'jeune' => count($this->cotisationRepository->findByStatut($annee,'Jeune',null, $resultat->getId())),
						'adulte' => count($this->cotisationRepository->findByStatut($annee,'Adulte',null, $resultat->getId())),
						'homme' => count($this->cotisationRepository->findBySexe($annee,'HOMME',null, $resultat->getId())),
						'femme' => count($this->cotisationRepository->findBySexe($annee,'FEMME',null, $resultat->getId())),
						'louveteau' => count($this->cotisationRepository->findByBranche($annee,'Jeune','LOUVETEAU',null, $resultat->getId())),
						'eclaireur' => count($this->cotisationRepository->findByBranche($annee,'Jeune','ECLAIREUR',null, $resultat->getId())),
						'cheminot' => count($this->cotisationRepository->findByBranche($annee,'Jeune','CHEMINOT',null, $resultat->getId())),
						'routier' => count($this->cotisationRepository->findByBranche($annee,'Jeune','ROUTIER',null, $resultat->getId())),
					];
				}
			}
			elseif($district)
				$resultats=[];
			else{
				$resultats = $this->em->getRepository(Region::class)->findAll();
				foreach($resultats as $resultat){
					$lists[$i++] = [
						'nom' => $resultat->getNom(),
						'total' => count($this->cotisationRepository->findList($annee, $resultat->getId())),
						'jeune' => count($this->cotisationRepository->findByStatut($annee,'Jeune', $resultat->getId())),
						'adulte' => count($this->cotisationRepository->findByStatut($annee,'Adulte', $resultat->getId())),
						'homme' => count($this->cotisationRepository->findBySexe($annee,'HOMME', $resultat->getId())),
						'femme' => count($this->cotisationRepository->findBySexe($annee,'FEMME', $resultat->getId())),
						'louveteau' => count($this->cotisationRepository->findByBranche($annee,'Jeune','LOUVETEAU', $resultat->getId())),
						'eclaireur' => count($this->cotisationRepository->findByBranche($annee,'Jeune','ECLAIREUR', $resultat->getId())),
						'cheminot' => count($this->cotisationRepository->findByBranche($annee,'Jeune','CHEMINOT', $resultat->getId())),
						'routier' => count($this->cotisationRepository->findByBranche($annee,'Jeune','ROUTIER', $resultat->getId())),
					];
				}
			}
			
			return $lists;
		}
		
		public function listeAdherants($annee, $region=null, $district=null)
		{
			$adherants = $this->em->getRepository(Adherant::class)->findListNonValid();
			$list=[]; $i=0;
			foreach ($adherants as $adherant){
				$list[$i++]=[
					'loop_index' => $i,
					'region' => $adherant->getGroupe()->getDistrict()->getRegion()->getNom(),
					'district' => $adherant->getGroupe()->getDistrict()->getNom(),
					'groupe' => $adherant->getGroupe()->getParoisse(),
					'matricule' => $adherant->getMatricule(),
					'nom' => $adherant->getNom(),
					'prenoms' => $adherant->getPrenoms(),
					'identite_civile' => $adherant->getNom().' '.$adherant->getPrenoms(),
					'date_naissance' => $adherant->getDateNaissance(),
					'lieu_naissance' => $adherant->getLieuNaissance(),
					'sexe' => $adherant->getSexe(),
					'contact' => $adherant->getContact(),
					'carte' => $adherant->getCarte(),
					'urgence' => $adherant->getContactUrgence(),
					'fonction' => $adherant->getFonction(),
					'branche' => $adherant->getBranche(),
					'token' => $adherant->getToken(),
					'url' => $adherant->getUrl(),
					'response_id' => $adherant->getResponseId(),
					'created_at' => $adherant->getCreatedAt(),
					'transaction_id' => $adherant->getIdTransaction(),
					'show_url' => '<a href="show/'.$adherant->getIdTransaction().'" target="_blank" title="Clique ici">'.$adherant->getIdTransaction().'</a>'
				];
			}
			
			return $list;
		}
		
		/**
		 * @return string
		 */
		public function annee(): string
		{
			$mois_encours = Date('m', time());
			if ($mois_encours > 9){
				$debut_annee = Date('Y', time());
				$fin_annee = Date('Y', time())+1;
				//$an = Date('y', time())+1;
			}else{
				$debut_annee = Date('Y', time())-1;
				$fin_annee = Date('Y', time());
				//$an = Date('y', time());
			}
			
			$annee = $debut_annee.'-'.$fin_annee;
			
			return $annee;
		}
		
		public function branche(): array
		{
			$branche = [
				'meute' => 'LOUVETEAU',
				'troupe' => 'ECLAIREUR',
				'cheminot' => 'CHEMINOT',
				'routier' => 'ROUTIER'
			];
			
			return $branche;
		}
	}
