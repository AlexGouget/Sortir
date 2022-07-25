<?php

namespace App\Repository;

use App\Entity\Sortie;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @extends ServiceEntityRepository<Sortie>
 *
 * @method Sortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sortie[]    findAll()
 * @method Sortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }

    public function add(Sortie $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Sortie $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
/**
 * @return Sortie[] toutes les sortie ouverte
 */

    public function findSortiesOuverte($pag){
        return $this ->createQueryBuilder('s')
            ->leftJoin('s.categorie', 'c')
            ->leftJoin('s.organisateur', 'o')
            ->leftJoin('s.participant', 'p')
            ->leftJoin('s.lieu', 'l')
            ->leftJoin('l.ville', 'v')
            ->addSelect('v')
            ->addSelect('l')
            ->addSelect('p')
            ->addSelect('c')
            ->addSelect('o')
            ->andWhere('s.etat= :val')
            ->andWhere('s.dateHeureDebut <= current_date()')
            ->andWhere('s.dateLimiteInscription >= current_date()')
            ->setParameter('val', 6)
            ->orderBy('s.dateHeureDebut', 'ASC')
            ->setMaxResults($pag)
            ->getQuery()
            ->getResult();

    }


    /**
     * @return Sortie[] La recherche de sortie
     */

    public function findSortiebyName(string $query,
                                     UserInterface $user,
                                     $campus = null,
                                     $cat = null,
                                     $dateDebut = null,
                                     $dateFin = null,
                                    $CBorga = null,
                                    $CBinscrit = null,
                                    $CBnonInscrit = null,
                                    $CBfini = null

    )
    {


        $qb = $this->createQueryBuilder('s');
        $qb->leftJoin('s.categorie', 'c')
        ->leftJoin('s.organisateur', 'o')
            ->leftJoin('s.campus', 'camp')
        ->leftJoin('s.participant', 'p')
        ->leftJoin('s.lieu', 'l')
        ->leftJoin('l.ville', 'v');
        $qb ->addSelect('v')
            ->addSelect('l')
            ->addSelect('p')
            ->addSelect('c')
            ->addSelect('camp')
            ->addSelect('o');
        $qb->where(
                $qb->expr()->andX(
                    $qb->expr()->orX(
                        $qb->expr()->like('s.nom', ':query'),
                        $qb->expr()->like('v.nom', ':query'),
                        $qb->expr()->like('l.nom', ':query'),
                    ),

                )
            )
            ->setParameter('query', '%' . $query . '%');
        if($cat){
            $qb->andWhere('c = :val')
                ->setParameter('val', $cat);
        }
        if($campus){
            $qb->andWhere('camp = :val2')
                ->setParameter('val2', $campus);
        }

        if($dateDebut){
            $qb->andWhere('s.dateHeureDebut > :val3')
                ->setParameter('val3', $dateDebut);
        }
        if($dateDebut){
            $qb->andWhere('s.dateHeureDebut < :val4')
                ->setParameter('val4', $dateFin);
        }

        if($CBinscrit){
            $qb->andWhere('p = :val5')
                ->setParameter('val5', $user);
        }
        if($CBorga){
            $qb->andWhere('o = :val6')
                ->setParameter('val6', $user);
        }
        if($CBnonInscrit){
            $qb->andWhere('p != :val7')
                ->setParameter('val7', $user);
        }
        if($CBfini){
            $qb->andWhere('s.dateHeureDebut <  :val8')
                ->setParameter('val8', new \DateTime());
        }

        $qb
            ->getQuery();

        return $qb
            ->getQuery()
            ->getResult();
    }


//    /**
//     * @return Sortie[] Returns an array of Sortie objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Sortie
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
