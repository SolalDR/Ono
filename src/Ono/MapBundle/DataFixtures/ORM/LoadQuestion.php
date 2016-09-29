<?php

namespace Ono\MapBundle\DataFixtures\ORM;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Ono\MapBundle\Entity\Question;
class LoadQuestion implements FixtureInterface
{
  // Dans l'argument de la méthode load, l'objet $manager est l'EntityManager
  public function load(ObjectManager $manager)
  {
    // Liste des noms de catégorie à ajouter
    $libQuestions = array(
      'Quel est ton nom ?',
      'Quel est ton numéro de sécu mobile ?',
      'Quel est ta couleur préféré ?'
    );

    foreach ($libQuestions as $libQuestion) {
      // On crée la catégorie
      $question = new Question();
      $question->setLibQuestion($libQuestion);
      // On la persiste
      $manager->persist($question);
    }
    // On déclenche l'enregistrement de toutes les catégories
    $manager->flush();
  }
}
