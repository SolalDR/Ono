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

    for ($i=0; $i<count($tab); $i++) {
      // On crée la catégorie
      $theme = new Theme();
      $theme->setLibTheme($tab[$i]["libTheme"]);
      $theme->setDescription($tab[$i]["description"]);
      $theme->setCdTheme($tab[$i]["cdTheme"]);

      // On la persiste
      $manager->persist($theme);
    }
    // On déclenche l'enregistrement de tous les thèmes
    $manager->flush();
  }

  public function getOrder() {
    return 3;
  }
}
