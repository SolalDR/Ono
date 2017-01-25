<?php

namespace Ono\MapBundle\Repository;

/**
 * ResponseRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ResponseRepository extends \Doctrine\ORM\EntityRepository
{
  public function getResponses($themesId, $orderByLike){
    $qb = $this->createQueryBuilder('r');

    // On fait une jointure avec l'entité Category avec pour alias « c »
    $qb
      ->innerJoin('r.question', 'q')
      ->addSelect('q')

      ->innerJoin('q.themes', 't')

      ->addSelect('t')
    ;

    // Puis on filtre sur le nom des catégories à l'aide d'un IN
    if($themesId){
      $qb->where($qb->expr()->in('r.id', $themesId));
    }

    if($orderByLike){
      $qb->orderBy('r.nbLikes', 'ASC');
    }
    // La syntaxe du IN et d'autres expressions se trouve dans la documentation Doctrine

    // Enfin, on retourne le résultat
    return $qb
      ->getQuery()
      ->getResult()
    ;
  }
}
