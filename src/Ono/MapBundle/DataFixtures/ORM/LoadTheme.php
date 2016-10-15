<?php

namespace Ono\MapBundle\DataFixtures\ORM;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Ono\MapBundle\Entity\Theme;

class LoadTheme implements FixtureInterface
{
  // Dans l'argument de la méthode load, l'objet $manager est l'EntityManager
  public function load(ObjectManager $manager)
  {
    $json = file_get_contents("web/theme.json");

    $tab = (array) json_decode($json);
    for($i=0; $i<count($tab); $i++){
      $tab[$i] = (array) $tab[$i];
    }

    for ($i=0; $i<count($tab); $i++) {
      // On crée la catégorie
      $theme = new Theme();
      $theme->setLibTheme($tab[$i]["libTheme"]);
      $theme->setDescription($tab[$i]["description"]);
      $theme->setCdTheme($tab[$i]["cdTheme"]);

      // On la persiste
      $manager->persist($theme);
    }
    // On déclenche l'enregistrement de toutes les catégories
    $manager->flush();
  }
}
