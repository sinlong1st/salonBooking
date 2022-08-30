<?php

namespace App\Repository;

use App\Entity\SpecialDate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Shop;
use Doctrine\ORM\Query\Expr;

/**
 * @method SpecialDate|null find($id, $lockMode = null, $lockVersion = null)
 * @method SpecialDate|null findOneBy(array $criteria, array $orderBy = null)
 * @method SpecialDate[]    findAll()
 * @method SpecialDate[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SpecialDateRepository extends ServiceEntityRepository {

    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, SpecialDate::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(SpecialDate $entity, bool $flush = true): void {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(SpecialDate $entity, bool $flush = true): void {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function getShopDateUnAvailable(int $shop_id) {
        $today = new \DateTime();
        $today->setTime(0, 0, 0);

        $qb = $this->createQueryBuilder('d');
        $q = $qb->select('d.date')
                ->where(
                        $qb->expr()->eq('d.shop', ':shopid')
                )
                ->andWhere(
                        $qb->expr()->gte('d.date', ':fromdate')
                )
                ->andWhere(
                        $qb->expr()->eq('d.active', ':active')
                )
                ->setParameter(':shopid', $shop_id)
                ->setParameter(':fromdate', $today->format('Y-m-d'))
                ->setParameter(':active', true)
        ;

        return $q->getQuery()->getArrayResult();
    }

    public function checkDateBookingInSpecialDate($shop_id, \DateTimeInterface $date) {
        $qb = $this->createQueryBuilder('d');
        $qb->select('d')
                ->join('d.shop', 's', Expr\Join::WITH)
                ->where($qb->expr()->eq('s.id', ':shop_id'))
                ->andWhere($qb->expr()->gte('d.date', ':datefrom'))
                ->andWhere($qb->expr()->lte('d.date', ':dateto'))
                ->setParameter(':shop_id', $shop_id)
                ->setParameter(':datefrom', $date->format('Y-m-d 00:00:00'))
                ->setParameter(':dateto', $date->format('Y-m-d 23:59:59'))
                ;
        return $qb->getQuery()->getOneOrNullResult();
    }

    // /**
    //  * @return SpecialDate[] Returns an array of SpecialDate objects
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
      public function findOneBySomeField($value): ?SpecialDate
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
