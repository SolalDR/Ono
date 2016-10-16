<?php

namespace Ono\MapBundle\DataFixtures\ORM;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Ono\MapBundle\Entity\Response;

class LoadResponse extends AbstractFixture implements OrderedFixtureInterface
{
  // Dans l'argument de la méthode load, l'objet $manager est l'EntityManager
  public function load(ObjectManager $manager)
  {
    $json = file_get_contents("web/json/response.json");

    $tab = (array) json_decode($json);
    for($i=0; $i<count($tab); $i++){
      $tab[$i] = (array) $tab[$i];
    }

    for ($i=0; $i<count($tab); $i++) {
      // On crée la catégorie
      $response = new Response();
      $response->setContent($tab[$i]["content"]);
      $response->setDtcreation(date_create_from_format("Y-m-d G:i:s", $tab[$i]["dtcreation"]));
      $response->setAuthor($tab[$i]["author"]);
      $response->setDtnaissance(date_create_from_format("Y-m-d", $tab[$i]["dtnaissance"]));
      $response->setPublished($tab[$i]["published"]);

      if ($i == count($tab) - 1) {
        $response->setQuestion($this->getReference("question-".($i - 1)));
      }
      else {
        $response->setQuestion($this->getReference("question-".$i));
      }

      $response->setCountry($this->getReference("country-".$i));
      // On la persiste
      $manager->persist($response);
    }
    // On déclenche l'enregistrement de toutes les catégories
    $manager->flush();
  }

  public function getOrder() {
    return 5;
  }
}
