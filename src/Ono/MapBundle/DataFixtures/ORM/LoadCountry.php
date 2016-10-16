<?php

namespace Ono\MapBundle\DataFixtures\ORM;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Ono\MapBundle\Entity\Country;

class LoadCountry extends AbstractFixture implements OrderedFixtureInterface
{
  // Dans l'argument de la méthode load, l'objet $manager est l'EntityManager
  public function load(ObjectManager $manager)
  {
    $json = file_get_contents("web/json/country.json");

    $tab = (array) json_decode($json);
    for($i=0; $i<count($tab); $i++){
      $tab[$i] = (array) $tab[$i];
    }

    // Initialisation du tableau des pays
    $countries = array();

    for ($i=0; $i<count($tab); $i++) {
      // On crée la catégorie
      $country = new Country();
      $country->setCdCountry($tab[$i]["cdCountry"]);
      $country->setLibCountry($tab[$i]["country"]);
      $country->setLibCapital($tab[$i]["capital"]);
      $country->setLat((int) $tab[$i]["lat"]);
      $country->setLn((int) $tab[$i]["lon"]);

      // On la persiste
      $manager->persist($country);

      // On ajoute l'entité dans le tableau
      array_push($countries, $country);
    }
    // On déclenche l'enregistrement de toutes les catégories
    $manager->flush();

    // Mélange des pays et référencement de 4 pays aléatoires
    shuffle($countries);
    for ($i=0; $i < 5; $i++) {
      $ref = "country-" . $i;
      $this->addReference($ref, $countries[$i]);
    }
  }

  public function getOrder() {
    return 2;
  }
}
