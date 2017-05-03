<?php
namespace Ono\MapBundle\Repository;

use Doctrine\ORM\Query\Expr\Join;

/**
 * ArticleRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ArticleRepository extends \Doctrine\ORM\EntityRepository
{
  public function getArticlesWithThemes(array $themesId)
  {
    $qb = $this->createQueryBuilder('q');
    $qb->innerJoin('q.themes', 't')
       ->addSelect('t')
       ->where($qb->expr()->in('t.id', $themesId));
    return $qb
      ->getQuery()
      ->getResult()
    ;
  }

  public function getNbUsedCount($tagId){
    // $em = $this->getDoctrine()->getManager();
     $qb = $this->_em->createQueryBuilder();

     $result = $qb->select('COUNT(u) AS amount')
                  ->from('OnoMapBundle:Article' , 'u')
                  ->innerJoin('u.tags','t')
                  ->where('t.id = :tagId')
                  ->setParameter("tagId", $tagId);
    // La syntaxe du IN et d'autres expressions se trouve dans la documentation Doctrine

    // Enfin, on retourne le résultat
    return $qb
      ->getQuery()
      ->getResult()
    ;
  }

}
