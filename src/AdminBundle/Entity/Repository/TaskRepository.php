<?php

namespace AdminBundle\Entity\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

/**
 * Class TaskRepository
 * @package AdminBundle\Entity\Repository
 */
class TaskRepository extends EntityRepository
{
    public function getPagedTask($limit = 10, $offset = 0)
    {
        if ($offset > 0) {
            $offset -= 1;
        }

        $qb = $this->createQueryBuilder('t');

        return $qb
            ->getQuery()
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->getResult();
    }
}