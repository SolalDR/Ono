<?php

namespace Ono\MapBundle\DataFixtures\ORM;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Ono\MapBundle\Entity\Language;

class LoadLanguage implements FixtureInterface
{
  // Dans l'argument de la méthode load, l'objet $manager est l'EntityManager
  public function load(ObjectManager $manager)
  {
    $json = file_get_contents("web/language.json");

    $tab = (array) json_decode($json);
    for($i=0; $i<count($tab); $i++){
      $tab[$i] = (array) $tab[$i];
    }

    for ($i=0; $i<count($tab); $i++) {
      // On crée la catégorie
      $language = new Language();
      $language->setCdLanguage($tab[$i]["cdLang"]);
      $language->setLibLanguageFr($tab[$i]["libLang"]);
      $language->setLibLanguageEn($tab[$i]["libLangEn"]);

      // On la persiste
      $manager->persist($language);
    }
    // On déclenche l'enregistrement de toutes les catégories
    $manager->flush();
  }
}
