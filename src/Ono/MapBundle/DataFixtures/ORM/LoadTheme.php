<?php

namespace Ono\MapBundle\DataFixtures\ORM;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Ono\MapBundle\Entity\Theme;

class LoadTheme extends AbstractFixture implements OrderedFixtureInterface
{
  // Dans l'argument de la méthode load, l'objet $manager est l'EntityManager
  public function load(ObjectManager $manager)
  {
    $json = file_get_contents("web/json/theme.json");

    $tab = (array) json_decode($json);
    for($i=0; $i<count($tab); $i++){
      $tab[$i] = (array) $tab[$i];
    }

    // Initialisation du tableau des thèmes
    $themes = array();

    for ($i=0; $i<count($tab); $i++) {
      // On crée la catégorie
      $theme = new Theme();
      $theme->setLibTheme($tab[$i]["libTheme"]);
      $theme->setDescription($tab[$i]["description"]);
      $theme->setCdTheme($tab[$i]["cdTheme"]);

      // On la persiste
      $manager->persist($theme);

      // On ajoute l'entité dans le tableau
      array_push($themes, $theme);
    }
    // On déclenche l'enregistrement de toutes les catégories
    $manager->flush();

    // Mélange des pays et référencement de 4 thèmes aléatoires
    shuffle($themes);
    for ($i=0; $i < 5; $i++) {
      $ref = "theme-" . $i;
      $this->addReference($ref, $themes[$i]);
    }
  }

  public function getOrder() {
    return 3;
  }
}
