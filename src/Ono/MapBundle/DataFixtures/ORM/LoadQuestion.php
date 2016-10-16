<?php

namespace Ono\MapBundle\DataFixtures\ORM;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Ono\MapBundle\Entity\Question;

class LoadQuestion extends AbstractFixture implements OrderedFixtureInterface
{
  // Dans l'argument de la méthode load, l'objet $manager est l'EntityManager
  public function load(ObjectManager $manager)
  {
    $json = file_get_contents("web/json/question.json");

    $tab = (array) json_decode($json);
    for($i=0; $i<count($tab); $i++){
      $tab[$i] = (array) $tab[$i];
    }

    // Initialisation du tableau des questions
    $questions = array();

    for ($i=0; $i<count($tab); $i++) {
      // On crée la catégorie
      $question = new Question();
      $question->setLibQuestion($tab[$i]["libQuestion"]);
      $question->addTheme($this->getReference("theme-".$i));
      if ($i == count($tab) - 1) {
        $j = $i + 1;
        $question->addTheme($this->getReference("theme-".$j));
      }

      // On la persiste
      $manager->persist($question);

      // On ajoute l'entité dans le tableau
      array_push($questions, $question);
    }
    // On déclenche l'enregistrement de toutes les catégories
    $manager->flush();

    for ($i=0; $i<count($questions); $i++) {
      $ref = "question-".$i;
      $this->addReference($ref, $questions[$i]);
    }
  }

  public function getOrder() {
    return 4;
  }
}
