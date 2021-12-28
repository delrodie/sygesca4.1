<?php
	
	namespace App\Repository;
	
	use App\Entity\Adherant;
	use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
	use Doctrine\Persistence\ManagerRegistry;
	
	/**
	 * @method Adherant|null find($id, $lockMode = null, $lockVersion = null)
	 * @method Adherant|null findOneBy(array $criteria, array $orderBy = null)
	 * @method Adherant[]    findAll()
	 * @method Adherant[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
	 */
	class AdherantRepository extends ServiceEntityRepository
	{
		public function __construct(ManagerRegistry $registry)
		{
			parent::__construct($registry, Adherant::class);
		}
		
		public function findByForm($adherant)
		{
			return $this
				->createQueryBuilder('a')
				->where('a.nom = :nom')
				->andWhere('a.prenoms = :prenoms')
				->andWhere('a.datenaissance = :dateNaissance')
				->andWhere('a.lieunaissance = :lieuNaissance')
				->andWhere('a.contact = :contact')
				->andWhere('a.contacturgence = :urgence')
				->orderBy('a.id', 'DESC')
				->setMaxResults(1)
				->setParameters([
					'nom' => $adherant['nom'],
					'prenoms' => $adherant['prenoms'],
					'dateNaissance' => $adherant['date_naissance'],
					'lieuNaissance' => $adherant['lieu_naissance'],
					'contact' => $adherant['contact'],
					'urgence' => $adherant['contact_urgence']
				])
				->getQuery()->getOneOrNullResult()
				;
		}
		
		public function findListNonValid($region=null)
		{
			return $this
				->createQueryBuilder('a')
				->addSelect('g')
				->addSelect('d')
				->addSelect('r')
				->leftJoin('a.groupe', 'g')
				->leftJoin('g.district', 'd')
				->leftJoin('d.region', 'r')
				->where('a.statuspaiement = :status')
				->orderBy('a.createdat', 'DESC')
				->setParameter('status', "UNKNOW")
				->getQuery()->getResult()
				;
		}
		
		/**
		 * @param $id
		 * @return int|mixed|string
		 */
		public function findListNonValidProcessus($date)
		{
			return $this
				->createQueryBuilder('a')
				->addSelect('g')
				->addSelect('d')
				->addSelect('r')
				->leftJoin('a.groupe', 'g')
				->leftJoin('g.district', 'd')
				->leftJoin('d.region', 'r')
				->where('a.statuspaiement = :status')
				->andWhere('a.createdat < :date')
				->orderBy('a.createdat', 'DESC')
				->setParameters([
					'status' => 'UNKNOW',
					'date' => $date
				])
				->getQuery()->getResult()
				;
		}
	}
