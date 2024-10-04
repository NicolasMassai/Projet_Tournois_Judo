<?php

namespace App\Repository;

use App\Entity\Adherant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Adherant>
 */
class AdherantRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Adherant::class);
    }

    public function findAdherantsInscritsDansTournoi(int $tournoiId, int $clubId = null): array
    {
        $qb = $this->createQueryBuilder('a')
            ->join('a.tournois', 't')
            ->where('t.id = :tournoiId')
            ->setParameter('tournoiId', $tournoiId);

        if ($clubId !== null) {
            $qb->andWhere('a.club = :clubId')
               ->setParameter('clubId', $clubId);
        }

        return $qb->getQuery()->getResult();
    }

    //    /**
    //     * @return Adherant[] Returns an array of Adherant objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('a.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Adherant
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
