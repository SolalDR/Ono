<?php

namespace Ono\MapBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\sfWebRequest;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Finder\Exception\AccessDeniedException;

use Ono\MapBundle\Entity\Article;
use Ono\UserBundle\Entity\User;
use Ono\MapBundle\Form\ArticleType;

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class ArticleController extends Controller
{
  public function indexAction(){
    $manager = $this->getDoctrine()->getManager();
    $repoArticle = $manager->getRepository("OnoMapBundle:Article");
    $themesRepo = $manager->getRepository("OnoMapBundle:Theme");
    $themes = $themesRepo->findAll();
    $articles = $repoArticle->findAll();
    return $this->render("OnoMapBundle:Article:index.html.twig", array(
      "articles" => $articles,
      "themes" => $themes
    ));
  }

  public function showAction($id){
    $manager = $this->getDoctrine()->getManager();
    $repoArticle = $manager->getRepository("OnoMapBundle:Article");
    $themRepo = $manager->getRepository("OnoMapBundle:Theme");

    $article = $repoArticle->find($id);
    if($article === null){
      throw new NotFoundHttpException("La réponse à afficher n'existe pas.");
    }
    $themes = $themRepo->findAll();

    return $this->render("OnoMapBundle:Article:show.html.twig", array(
      "article" => $article,
      "themes" =>$themes
    ));
  }

  public function addAction(Request $request){
    $manager =$this->getDoctrine()->getManager();
    $themRepo = $manager->getRepository("OnoMapBundle:Theme");
    $themes = $themRepo->findAll();

    $article = new Article;
    $article->setDtcreation(new \DateTime());


    if($this->container->get('security.authorization_checker')->isGranted('ROLE_USER')){
      $user = $this->get('security.token_storage')->getToken()->getUser();
      $article->setUser($user);
      $form = $this->get('form.factory')->create(ArticleType::class, $article);
    }

    if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
        //On persist la réponse
        $manager->persist($article);
        //On enregistre
        $manager->flush();
        $request->getSession()->getFlashBag()->add('notice', 'Article bien enregistrée.');
        return $this->redirectToRoute("ono_map_article_view", array(
          "id" => $article->getId(),
          "themes" => $themes
        ));
    }

    return $this->render('OnoMapBundle:Article:add.html.twig', array(
      'form' => $form->createView(),
      "themes" => $themes
    ));

  }
  public function editAction($id, Request $request){
    $manager =$this->getDoctrine()->getManager();
    $repoArticle = $manager->getRepository("OnoMapBundle:Article");
    $themRepo = $manager->getRepository("OnoMapBundle:Theme");
    $themes = $themRepo->findAll();

    $article = $repoArticle->find($id);
    $user = $this->get('security.token_storage')->getToken()->getUser();

    if($article && $user instanceof User && $user->getId() === $article->getUser()->getId()){
      if($this->container->get('security.authorization_checker')->isGranted('ROLE_USER')){
        $form = $this->get('form.factory')->create(ArticleType::class, $article);
      }

      if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
          //On persist la réponse
          $manager->persist($article);
          //On enregistre
          $manager->flush();
          $request->getSession()->getFlashBag()->add('notice', 'Article bien modifié.');
          return $this->redirectToRoute("ono_map_article_view", array(
            "id" => $article->getId(),
            "themes" => $themes
          ));
      }
      return $this->render('OnoMapBundle:Article:edit.html.twig', array(
        'form' => $form->createView(),
        "themes" => $themes,
        "article" => $article
      ));
    }
    return $this->redirectToRoute("ono_map_article_view", array(
      "id" => $id
    ));
  }
  public function likeAction($id, Request $request){
    $manager = $this->getDoctrine()->getManager();
    $repoArticle = $manager->getRepository("OnoMapBundle:Article");
    $article = $repoArticle->find($id);


    if($article){
      if($this->container->get('security.authorization_checker')->isGranted('ROLE_USER')){
        $user = $this->get('security.token_storage')->getToken()->getUser();
        if($user){
          $isLiking = $user->isLiking($article);
          if(!$isLiking){
            $user->addArticlesLiked($article);
            $article->incrementLikes();
            $manager->persist($user);
            $manager->persist($article);
            $manager->flush();
          }
          $request->getSession()->getFlashBag()->add('notice', 'La réponse est déja aimé.');
        }
      }
      return $this->redirectToRoute('ono_map_article_view', array('id' => $article->getId()));
    } else {
      $request->getSession()->getFlashBag()->add('notice', 'La réponse n\'existe pas.');
    }
    //L'utilisateur n'est pas authentifié
    return $this->redirectToRoute('ono_map_homepage');
  }

  public function unlikeAction($id, Request $request){
    $manager = $this->getDoctrine()->getManager();
    $repoArticle = $manager->getRepository("OnoMapBundle:Article");
    $article = $repoArticle->find($id);

    if($article){
      if($this->container->get('security.authorization_checker')->isGranted('ROLE_USER')){
        $user = $this->get('security.token_storage')->getToken()->getUser();
        if($user){
          $isLiking = $user->isLiking($article);
          if($isLiking){
            $user->removeArticlesLiked($article);
            $article->decrementLikes();
            $manager->persist($user);
            $manager->persist($article);
            $manager->flush();
          }
          $request->getSession()->getFlashBag()->add('notice', 'L\'article est pas aimé.');
          return $this->redirectToRoute('ono_map_article_view', array('id' => $article->getId()));
        }
        //La response n'existe pas
      }
      return $this->redirectToRoute('ono_map_article_view', array('id' => $article->getId()));
    }
    //L'utilisateur n'est pas authentifié
    return $this->redirectToRoute('ono_map_homepage');
  }
}
