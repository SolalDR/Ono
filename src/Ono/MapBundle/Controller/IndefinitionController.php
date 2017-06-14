<?php

namespace Ono\MapBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Ono\MapBundle\Entity\Indefinition;
use Ono\MapBundle\Form\IndefinitionType;
use Ono\MapBundle\Form\IndefinitionLogType;


class IndefinitionController extends Controller
{

  public function addAction(Request $request)
  {
    if($this->container->get('security.authorization_checker')->isGranted('ROLE_USER')){
      $user = $this->container->get('security.token_storage')->getToken()->getUser();
      $userName = $user->getName();
      $userFirstname = $user->getFirstname();
      $hasName = false;
      if (($userName && trim($userName) != "") || ($userFirstname && trim($userFirstname) != "")) {
        $hasName = true;
      }
    }

    $manager = $this->getDoctrine()->getManager();
    $indefinition = new Indefinition;
    if (isset($user) && $hasName) {
      $indefinition->updateUser($user);
      $form = $this->get('form.factory')->create(IndefinitionLogType::class, $indefinition);
    } else {
      $form = $this->get('form.factory')->create(IndefinitionType::class, $indefinition);
    }
    $tagId = (int) $request->attributes->all()["tag_id"];
    $tagRepo = $manager->getRepository("OnoMapBundle:Tag");
    $tag = $tagRepo->find($tagId);

    if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
      if(isset($user) && $hasName) {
        $indefinition->setUser($user);
      }
      $indefinition->setTag($tag);
      $manager->persist($indefinition);
      $manager->flush();
      $request->getSession()->getFlashBag()->add('notice', 'Indéfinition bien enregistrée.');
      $articleId = (int) $request->attributes->all()["article_id"];
      return $this->redirectToRoute("ono_map_tag_view", array("article_id" => $articleId, "id" => $tagId));
    }

    $themRepo = $manager->getRepository("OnoMapBundle:Theme");
    $themes = $themRepo->findAll();

    return $this->render('OnoMapBundle:Indefinition:add.html.twig', array(
      "form" => $form->createView(),
      "tag" => $tag,
      "themes" => $themes
    ));
  }

  public function editAction(Request $request)
  {
    $manager = $this->getDoctrine()->getManager();
    if($this->container->get('security.authorization_checker')->isGranted('ROLE_USER')){
      $user = $this->container->get('security.token_storage')->getToken()->getUser();
      $userName = $user->getName();
      $userFirstname = $user->getFirstname();
      $hasName = false;
      if (($userName && trim($userName) != "") || ($userFirstname && trim($userFirstname) != "")) {
        $hasName = true;
      }
    } else {
      return $this->redirectToRoute("ono_map_homepage");
    }

    $numArt = (int) $request->attributes->all()["article_id"];
    $numTag = (int) $request->attributes->all()["tag_id"];
    $numId = (int) $request->attributes->all()["id"];

    if ($numArt == 0) {
      $fromProfile = true;
    } else {
      $fromProfile = false;
    }

    $article = $manager->getRepository("OnoMapBundle:Article")->find($numArt);
    if (!$article && !$fromProfile) {
      return $this->redirectToRoute("ono_map_homepage");
    }

    $tag = $manager->getRepository("OnoMapBundle:Tag")->find($numTag);
    if(!$tag) {
      if ($fromProfile) {
        return $this->redirectToRoute("fos_user_profile_show");
      } else {
        return $this->redirectToRoute("ono_map_article_view", array("id" => $numArt));
      }
    }

    $indefinition = $manager->getRepository("OnoMapBundle:Indefinition")->find($numId);
    if (!$indefinition) {
      if ($fromProfile) {
        return $this->redirectToRoute("ono_map_tag_view", array("article_id" => 0, "id" => $numTag));
      } else {
        return $this->redirectToRoute("ono_map_tag_view", array("art_id" => $numArt, "id" => $numId));
      }
    }

    if ($indefinition->getTag() === $tag) {
      if (isset($user) && $hasName) {
        if ($indefinition->testUser($user)) {
          $form = $this->get('form.factory')->create(IndefinitionLogType::class, $indefinition);
        } else {
          if ($fromProfile) {
            return $this->redirectToRoute("ono_map_tag_view", array("article_id" => 0, "id" => $numTag));
          } else {
            return $this->redirectToRoute("ono_map_tag_view", array("article_id" => $numArt, "id" => $numId));
          }
        }
      } else {
        $form = $this->get('form.factory')->create(IndefinitionType::class, $indefinition);
      }
    } else {
      if ($fromProfile) {
        return $this->redirectToRoute("ono_map_tag_view", array("article_id" => 0, "id" => $numTag));
      } else {
        return $this->redirectToRoute("ono_map_tag_view", array("article_id" => $numArt, "id" => $numId));
      }
    }

    if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
      $manager->persist($indefinition);
      $manager->flush();

      $request->getSession()->getFlashBag()->add('notice', 'Indéfinition bien modifiée.');

      if ($fromProfile) {
        return $this->redirectToRoute("ono_map_tag_view", array("article_id" => 0, "id" => $numTag));
      } else {
        return $this->redirectToRoute("ono_map_tag_view", array("article_id" => $numArt, "id" => $numId));
      }
    }

    $themRepo = $manager->getRepository("OnoMapBundle:Theme");
    $themes = $themRepo->findAll();

    return $this->render('OnoMapBundle:Indefinition:edit.html.twig', array(
      "form" => $form->createView(),
      "indefinition" => $indefinition,
      "tag" => $tag,
      "themes" => $themes
    ));
  }

  public function deleteAction(Request $request) {
      $numArt = (int) $request->attributes->all()["article_id"];
      $numTag = (int) $request->attributes->all()["tag_id"];
      $numId = (int) $request->attributes->all()["id"];

      if ($numArt == 0) {
        $fromProfile = true;
      } else {
        $fromProfile = false;
      }

      if($this->container->get('security.authorization_checker')->isGranted('ROLE_USER')){
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
      } else {
        return $this->redirectToRoute("ono_map_homepage");
      }

      $manager = $this->getDoctrine()->getManager();

      $article = $manager->getRepository("OnoMapBundle:Article")->find($numArt);
      if (!$article && !$fromProfile) {
        return $this->redirectToRoute("ono_map_homepage");
      }

      $tag = $manager->getRepository("OnoMapBundle:Tag")->find($numTag);
      if(!$tag) {
        if ($fromProfile) {
          return $this->redirectToRoute("fos_user_profile_show");
        } else {
          return $this->redirectToRoute("ono_map_article_view", array("id" => $numArt));
        }
      }

      $indefinition = $manager->getRepository("OnoMapBundle:Indefinition")->find($numId);
      if (!$indefinition) {
        if ($fromProfile) {
          return $this->redirectToRoute("ono_map_tag_view", array("article_id" => 0, "id" => $numTag));
        } else {
          return $this->redirectToRoute("ono_map_tag_view", array("article_id" => $numArt, "id" => $numTag));
        }
      }

      if (!$user || $user !== $indefinition->getUser()) {
        if ($fromProfile) {
          return $this->redirectToRoute("ono_map_tag_view", array("article_id" => 0, "id" => $numTag));
        } else {
          return $this->redirectToRoute("ono_map_tag_view", array("article_id" => $numArt, "id" => $numTag));
        }
      }

      // On crée un formulaire vide, qui ne contiendra que le champ CSRF
      // Cela permet de protéger la suppression d'annonce contre cette faille
      $form = $this->createFormBuilder()->getForm();

      if ($form->handleRequest($request)->isValid()) {
        $manager->remove($indefinition);
        $manager->flush();

        $request->getSession()->getFlashBag()->add('info', "L'indéfinition a bien été supprimée.");

        if ($fromProfile) {
          return $this->redirectToRoute("ono_map_tag_view", array("article_id" => 0, "id" => $numTag));
        } else {
          return $this->redirectToRoute("ono_map_tag_view", array("article_id" => $numArt, "id" => $numTag));
        }
      }

      $themes = $manager->getRepository('OnoMapBundle:Theme')->findAll();
      // Si la requête est en GET, on affiche une page de confirmation avant de supprimer
      return $this->render('OnoMapBundle:Indefinition:delete.html.twig', array(
        'indefinition' => $indefinition,
        'libTag' => $tag->getLibTag(),
        'themes' => $themes,
        'artId' => $numArt,
        'tagId' => $numTag,
        'pathDelete' => "ono_map_indefinition_delete",
        'form'   => $form->createView()
      ));
  }
}
