<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\Combat;
use App\Entity\Tournoi;
use App\Entity\Adherant;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Combat>
 */
class CombatRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Combat::class);
    }


    public function findByUserAndTournoi(User $user, Tournoi $tournoi): array
    {

        return $this->createQueryBuilder('c')
            ->where('c.combattant1 = :user OR c.combattant2 = :user')
            ->andWhere('c.tournoi = :tournoi')
            ->setParameter('user', $user)
            ->setParameter('tournoi', $tournoi)
            ->getQuery()
            ->getResult();
    }

    public function findByCombattant(Adherant $adherant)
    {
        return $this->createQueryBuilder('c')
            ->where('c.combattant1 = :adherant')
            ->orWhere('c.combattant2 = :adherant')
            ->setParameter('adherant', $adherant)
            ->getQuery()
            ->getResult();
    }


    //    /**
    //     * @return Combat[] Returns an array of Combat objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Combat
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
