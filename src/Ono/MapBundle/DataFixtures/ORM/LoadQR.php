<?php

namespace Ono\MapBundle\DataFixtures\ORM;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Ono\MapBundle\Entity\Question;
use Ono\MapBundle\Entity\Response;

class LoadQR extends AbstractFixture implements OrderedFixtureInterface
{
  // Dans l'argument de la méthode load, l'objet $manager est l'EntityManager
  public function load(ObjectManager $manager)
  {
    // Récupération des repositories
    $t_repo = $manager->getRepository("OnoMapBundle:Theme");
    $c_repo = $manager->getRepository("OnoMapBundle:Country");
    $l_repo = $manager->getRepository("OnoMapBundle:Language");

    // Récupération des questions
    $ques_json = file_get_contents("web/json/question.json");
    $ques_tab = (array) json_decode($ques_json);
    for($i=0; $i<count($ques_tab); $i++){
      $ques_tab[$i] = (array) $ques_tab[$i];
    }

    // Récupération des réponses
    $resp_json = file_get_contents("web/json/response.json");
    $resp_tab = (array) json_decode($resp_json);
    for($i=0; $i<count($resp_tab); $i++){
      $resp_tab[$i] = (array) $resp_tab[$i];
    }

    for ($i=0; $i<count($ques_tab); $i++) {
      // On crée la catégorie
      $question = new Question();
      $question->setLibQuestion($ques_tab[$i]["libQuestion"]);
      $themes = $t_repo->findBy(array("cdTheme" => $ques_tab[$i]["cdThemes"]));

      // Ajout des thèmes
      for ($j=0; $j < count($themes); $j++) {
        $question->addTheme($themes[$j]);
      }

      // On la persiste
      $manager->persist($question);

      for ($j=0; $j < 3; $j++) {
          $k = 3*$i + $j;
          // Création d'une réponse associée à la question (avec paramètres)
          $response = new Response();
          $response->setContent($resp_tab[$k]["content"]);
          $response->setDtcreation(date_create_from_format("Y-m-d G:i:s", $resp_tab[$k]["dtcreation"]));
          $response->setAuthor($resp_tab[$k]["author"]);
          $response->setDtnaissance(date_create_from_format("Y-m-d", $resp_tab[$k]["dtnaissance"]));
          $response->setPublished($resp_tab[$k]["published"]);
          $country = $c_repo->findOneByCdCountry($resp_tab[$k]["cdCountry"]);
          $response->setCountry($country);

          $language = $l_repo->findOneBy(array("cdLanguage" => $resp_tab[$k]["cdLanguage"]));
          $response->setLanguage($language);
          
          // Relation Q-R côté réponse
          $response->setQuestion($question);
          $manager->persist($response);

          // Relation Q-R côté question
          $question->addResponse($response);
          $manager->persist($question);
      }
    }
    // On déclenche l'enregistrement de toutes les catégories
    $manager->flush();
  }

  public function getOrder() {
    return 5;
  }
}
