<?php

namespace Ono\MapBundle\Repository;

/**
 * ArticleRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ArticleRepository extends \Doctrine\ORM\EntityRepository
{
  public function getFromTag($tagId){
    $qb = $this->createQueryBuilder('a');

    // On fait une jointure avec l'entité Category avec pour alias « c »
    $qb
      ->innerJoin('a.tags', 't')
      ->addSelect('t');

    // Puis on filtre sur le nom des catégories à l'aide d'un IN
    $qb->where('t.id = :tagId')
    ->setParameter("tagId", $tagId);
    // La syntaxe du IN et d'autres expressions se trouve dans la documentation Doctrine

    // Enfin, on retourne le résultat
    return $qb
      ->getQuery()
      ->getResult()
    ;
  }
}
