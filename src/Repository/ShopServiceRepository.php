<?php

namespace App\Repository;

use App\Entity\ShopService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\Query\Expr;

/**
 * @method ShopService|null find($id, $lockMode = null, $lockVersion = null)
 * @method ShopService|null findOneBy(array $criteria, array $orderBy = null)
 * @method ShopService[]    findAll()
 * @method ShopService[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ShopServiceRepository extends ServiceEntityRepository {

    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, ShopService::class);
    }

    public function getShopServices($id) {
        $query = $this->getEntityManager()->getConnection()->executeQuery('SELECT id,price,shop_id,service_id  FROM shop_service WHERE shop_id=?', [$id]);

        $rs = $query->fetchAllAssociative();
        if (!empty($rs)) {

            return array_column($rs, null, 'service_id');
        }
        return [];
    }

    public function removeListID(array $ids) {
        $this->getEntityManager()->getConnection()->executeStatement("DELETE FROM  shop_service WHERE id IN ('" . implode("','", $ids) . "')");
    }

    public function LoadServices($id) {
        $query = $this->getEntityManager()->getConnection()->executeQuery('SELECT a.name, a.thumbnail, a.description, b.price, b.service_time, b.shop_id, b.service_id 
        FROM services a INNER JOIN shop_service b ON a.id = b.service_id
        WHERE a.active = 1 AND b.shop_id = ?', [$id]);

        $rs = $query->fetchAllAssociative();
        return $rs;
    }

    public function getShopServiceTime($shop_id, $service_id) {
        $qb = $this->createQueryBuilder('s');
        $qb->select('s.service_time')
                ->join('s.Shop', 'sh', Expr\Join::WITH)
                ->join('s.Service', 'sv', Expr\Join::WITH)
                ->where($qb->expr()->eq('sh.id', ':shop_id'))
                ->andwhere($qb->expr()->eq('sv.id', ':service_id'))
                ->andwhere($qb->expr()->eq('sv.Active', '1'))
                ->setParameter(':shop_id', $shop_id)
                ->setParameter(':service_id', $service_id);
        return $qb->getQuery()->getOneOrNullResult();
    }

    public function LoadAvaiTime($shop_id, $service_id, \DateInterval $date) {
        $sql = 'SELECT * ';
        $sql .= 'FROM shop_service t1 INNER JOIN booking t2 ON t1.id = t2.shop_service_id ';
        $sql .= 'WHERE t2.date = ' . $date->format('Y-m-d');
        $query = $this->getEntityManager()->getConnection()->executeQuery($sql);

        $rs = $query->fetchAllAssociative();
        return $rs;
    }

    // /**
    //  * @return ShopService[] Returns an array of ShopService objects
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
      public function findOneBySomeField($value): ?ShopService
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
