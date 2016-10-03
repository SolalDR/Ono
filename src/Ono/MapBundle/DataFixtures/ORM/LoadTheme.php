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
    // Liste des noms de catégorie à ajouter

    $libThemes = array(
      'Alimentation',
      'Pouvoir',
      'Sociétés, Traditions, Fêtes',
      'Commerce',
      "Les nombres",
      "Sentiments",
      "Intellect",
      "Vie / Mort",
      "Sport et jeux",
      "Edifice",
      "Humain",
      "Espaces",
      "Textile",
      "Corps",
      "Science et Technique",
      "Politique",
      "Temps",
      "Transport",
      "Animation Végétaux",
      "Activité"
    );
    {

    for($i=0; $i<count($libThemes); $i++){
      // On crée la catégorie
      $theme = new Theme();
      $theme->setLibTheme($libThemes[$i]);

      // On la persiste
      $manager->persist($theme);
    }

    }
    // On déclenche l'enregistrement de toutes les catégories
    $manager->flush();
  }
}
