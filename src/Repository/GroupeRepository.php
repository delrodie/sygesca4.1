<?php
	
	namespace App\Repository;
	
	use App\Entity\Sygesca3\Groupe;
	use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
	use Doctrine\Persistence\ManagerRegistry;
	
	/**
	 * @method Groupe|null find($id, $lockMode = null, $lockVersion = null)
	 * @method Groupe|null findOneBy(array $criteria, array $orderBy = null)
	 * @method Groupe[]    findAll()
	 * @method Groupe[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
	 */
	class GroupeRepository extends ServiceEntityRepository
	{
		public function __construct(ManagerRegistry $registry)
		{
			parent::__construct($registry, Groupe::class);
		}
		
		/**
		 * Recherche de l'equipe par district
		 *
		 * @param $district
		 * @param $equipe
		 * @return int|mixed|string
		 */
		public function findEquipeByDistrict($district, $equipe)
		{
			return $this
				->createQueryBuilder('g')
				->where('g.district = :district')
				->andWhere('g.paroisse LIKE :equipe')
				->setParameters([
					'district' => $district,
					'equipe' => '%'.$equipe.'%'
				])
				->getQuery()->getResult()
				;
		}
		
		/**
		 * @return int|mixed|string
		 */
		public function findList()
		{
			return $this
				->createQueryBuilder('g')
				->addSelect('d')
				->addSelect('r')
				->leftJoin('g.district', 'd')
				->leftJoin('d.region', 'r')
				->orderBy('r.id', "ASC")
				->addOrderBy('d.nom', 'ASC')
				->addOrderBy('g.paroisse', 'ASC')
				->getQuery()->getResult()
				;
		}
	}
