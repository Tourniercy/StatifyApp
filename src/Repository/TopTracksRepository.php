<?php

namespace App\Repository;

use App\Entity\TopTracks;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TopTracks|null find($id, $lockMode = null, $lockVersion = null)
 * @method TopTracks|null findOneBy(array $criteria, array $orderBy = null)
 * @method TopTracks[]    findAll()
 * @method TopTracks[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TopTracksRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TopTracks::class);
    }

    // /**
    //  * @return TopTracks[] Returns an array of TopTracks objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TopTracks
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
