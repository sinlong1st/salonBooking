<?php

namespace App\Repository;

use App\Entity\Booking;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\Expr;

/**
 * @method Booking|null find($id, $lockMode = null, $lockVersion = null)
 * @method Booking|null findOneBy(array $criteria, array $orderBy = null)
 * @method Booking[]    findAll()
 * @method Booking[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookingRepository extends ServiceEntityRepository {

    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Booking::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Booking $entity, bool $flush = true): void {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Booking $entity, bool $flush = true): void {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function getShopBookingByDate(int $shop_id, \DateTimeInterface $dateFrom, \DateTimeInterface $dateTo) {

        $qb = $this->createQueryBuilder('b');
        $qb->select('b.start_time,b.end_time, b.date, sh.id')
                ->leftJoin('b.ShopService', 's', Expr\Join::WITH)
                ->leftJoin('s.Shop', 'sh', Expr\Join::WITH)
                ->where($qb->expr()->eq('sh.id', ':id'))
                ->andWhere($qb->expr()->lte('b.date', ':dateto'))
                ->andWhere($qb->expr()->gte('b.date', ':datefrom'))
                ->setParameter(':id', $shop_id)
                ->setParameter(':datefrom', $dateFrom->format('Y-m-d H:i:s'))
                ->setParameter(':dateto', $dateTo->format('Y-m-d H:i:s'))
        ;

        return $qb->getQuery()->getArrayResult();
    }

    public function getCustomerBooking($customner_id) {
        $qb = $this->createQueryBuilder('b');
        $qb->select('b.id,b.start_time,b.end_time, b.date, sh.Name ShopName,sv.Name ServiceName')
                ->leftJoin('b.ShopService', 's', Expr\Join::WITH)
                ->leftJoin('s.Service','sv', Expr\Join::WITH)
                ->leftJoin('s.Shop', 'sh', Expr\Join::WITH)
                ->where($qb->expr()->eq('b.customer_id', ':customer_id'))
                ->setParameter(':customer_id', $customner_id)
                 ->orderBy('b.date', 'DESC')
        ;

        return $qb->getQuery()->getArrayResult();
    }

    // /**
    //  * @return Booking[] Returns an array of Booking objects
    //  */
    /*
      public function findByExampleField($value)
      {
      return $this->createQueryBuilder('b')
      ->andWhere('b.exampleField = :val')
      ->setParameter('val', $value)
      ->orderBy('b.id', 'ASC')
      ->setMaxResults(10)
      ->getQuery()
      ->getResult()
      ;
      }
     */

    /*
      public function findOneBySomeField($value): ?Booking
      {
      return $this->createQueryBuilder('b')
      ->andWhere('b.exampleField = :val')
      ->setParameter('val', $value)
      ->getQuery()
      ->getOneOrNullResult()
      ;
      }
     */
}
