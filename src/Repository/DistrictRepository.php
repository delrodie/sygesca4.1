<?php
	
	namespace App\Repository;
	
	use App\Entity\Sygesca3\District;
	use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
	use Doctrine\Persistence\ManagerRegistry;
	
	/**
	 * @method District|null find($id, $lockMode = null, $lockVersion = null)
	 * @method District|null findOneBy(array $criteria, array $orderBy = null)
	 * @method District[]    findAll()
	 * @method District[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
	 */
	class DistrictRepository extends ServiceEntityRepository
	{
		public function __construct(ManagerRegistry $registry)
		{
			parent::__construct($registry, District::class);
		}
		
		/**
		 * Recherche du district par l'equipe
		 *
		 * @param $region
		 * @param $equipe
		 * @return int|mixed|string
		 */
		public function findByEquipeByRegion($region, $equipe)
		{
			return $this
				->createQueryBuilder('d')
				->where('d.region = :region')
				->andWhere('d.nom LIKE :equipe')
				->setParameters([
					'region' => $region,
					'equipe' => '%'.$equipe.'%'
				])
				->getQuery()->getResult()
				;
		}
	}
