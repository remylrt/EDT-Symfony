<?php

namespace App\Repository;

use App\Entity\Cours;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Cours|null find($id, $lockMode = null, $lockVersion = null)
 * @method Cours|null findOneBy(array $criteria, array $orderBy = null)
 * @method Cours[]    findAll()
 * @method Cours[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CoursRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cours::class);
    }

    /**
     * @return Cours[] Returns an array of Cours objects
     */
    public function findByDate($date) {
        return $this->createQueryBuilder('c')
            ->where('c.dateHeureDebut LIKE :date')
            ->setParameter('date', $date->format('Y-m-d') . '%')
            ->orderBy('c.dateHeureDebut', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return Cours[] Returns an array of Cours objects
     */
    public function findByDateWeekly($date) {
        $anneeChoix = $date->format('Y');
        $semChoix = $date->format('W');

        $timeStampPremierJanvier = strtotime($anneeChoix . '-01-01');
        $jourPremierJanvier = date('w', $timeStampPremierJanvier);
        
        $numSemainePremierJanvier = date('W', $timeStampPremierJanvier);
        
        $decalage = ($numSemainePremierJanvier == 1) ? $semChoix - 1 : $semChoix;

        $timeStampDate = strtotime('+' . $decalage . ' weeks', $timeStampPremierJanvier);

        $dateDebutSemaine = ($jourPremierJanvier == 1) ? date('Y-m-d', $timeStampDate) : date('Y-m-d', strtotime('last monday', $timeStampDate));
        $dateFinSemaine = ($jourPremierJanvier == 1) ? date('Y-m-d', $timeStampDate) : date('Y-m-d',strtotime('next sunday', $timeStampDate));

        return $this->createQueryBuilder('c')
            ->where('c.dateHeureDebut BETWEEN :dateDebutSemaine AND :dateFinSemaine')
            ->setParameter('dateDebutSemaine', $dateDebutSemaine . '%')
            ->setParameter('dateFinSemaine', $dateFinSemaine . '%')
            ->orderBy('c.dateHeureDebut', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /*
    public function findOneBySomeField($value): ?Cours
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
