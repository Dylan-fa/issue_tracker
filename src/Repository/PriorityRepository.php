<?php

namespace App\Repository;

use App\Entity\Priority;
use App\Repository\IssueAttributeRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class PriorityRepository extends ServiceEntityRepository implements IssueAttributeRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Priority::class);
    }

    public function save(Priority $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Priority $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findNumberOfUses()
    {
        return $this->createQueryBuilder('p')
            ->select('p.name AS name, p.color AS color, count(pi) AS number')
            ->innerJoin('p.issues', 'pi')
            ->groupBy('name')
            ->getQuery()
            ->getResult()
        ;
    }
}
