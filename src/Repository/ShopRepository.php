<?php

namespace App\Repository;

use App\Entity\Shop;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\Expr;

/**
 * @method Shop|null find($id, $lockMode = null, $lockVersion = null)
 * @method Shop|null findOneBy(array $criteria, array $orderBy = null)
 * @method Shop[]    findAll()
 * @method Shop[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ShopRepository extends ServiceEntityRepository {

    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Shop::class);
    }

    public function getTodayWorkingHours($shop_id, \DateTimeInterface $date) {
        $qb = $this->createQueryBuilder('s');
        $qb->select('s.start_time,s.end_time, d.start_time new_start_time, d.end_time new_end_time, d.active')
                ->leftJoin('s.specialDates', 'd', Expr\Join::WITH, $qb->expr()->eq('d.date', ':date'))
                ->where($qb->expr()->eq('s.id', ':id'))
                ->andWhere('s.Active = 1')
                ->setParameter(':id', $shop_id)
                ->setParameter(':date', $date)
        ;
        return $qb->getQuery()->getArrayResult();
    }

    // /**
    //  * @return Shop[] Returns an array of Shop objects
    //  */
    /*
      public function findByExampleField($value)
      {
      return $this->createQueryBuilder('s')
      ->andWhere('s.exampleField = :val')
      ->setParameter('val', $value)
      ->orderBy('s.id', 'ASC')
      ->setMaxResults(10)
      ->getQuery()
      ->getResult()
      ;
      }
     */

    /*
      public function findOneBySomeField($value): ?Shop
      {
      return $this->createQueryBuilder('s')
      ->andWhere('s.exampleField = :val')
      ->setParameter('val', $value)
      ->getQuery()
      ->getOneOrNullResult()
      ;
      }
     */
}
