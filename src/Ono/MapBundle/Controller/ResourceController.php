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
use Ono\MapBundle\Entity\Tag;
use Ono\UserBundle\Entity\User;
use Ono\MapBundle\Form\ArticleType;

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class Resource extends Controller
{
  public function indexAction(Request $request){
    $manager = $this->getDoctrine()->getManager();
    $repoArticle = $manager->getRepository("OnoMapBundle:Article");
    $themesRepo = $manager->getRepository("OnoMapBundle:Theme");
    $themes = $themesRepo->findAll();
    $articles = $repoArticle->findAll();

    $routeName = $request->get('_route');
    if($routeName === "ono_admin_list_articles") {
      return $this->render('OnoMapBundle:Admin:list-articles.html.twig', array(
        "articles" => $articles
      ));
    }

    return $this->render("OnoMapBundle:Article:index.html.twig", array(
      "articles" => $articles,
      "themes" => $themes
    ));
  }

  public function showAction(Request $request){
    $parameters = $request->attributes->all();
    $numId = (int) $parameters["id"];
    if(isset($parameters["tag"])){
      $numTag = (int) $parameters["tag"];
    }
    $manager = $this->getDoctrine()->getManager();
    $repoArticle = $manager->getRepository("OnoMapBundle:Article");
    $themRepo = $manager->getRepository("OnoMapBundle:Theme");

    $article = $repoArticle->find($numId);
    if(isset($numTag)){
      $articles = $repoArticle->getFromTag($numTag);
      if (count($articles)>1){
        $article = $articles[floor(rand(0, count($articles)-1))];
      }
    }

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
    $tagRepo = $manager->getRepository("OnoMapBundle:Tag");
    $articleRepo = $manager->getRepository("OnoMapBundle:Article");
    $themes = $themRepo->findAll();

    $article = new Article;
    $article->setDtcreation(new \DateTime());



    if($this->container->get('security.authorization_checker')->isGranted('ROLE_EDITOR')){
      $user = $this->get('security.token_storage')->getToken()->getUser();
      $article->setUser($user);
      $form = $this->get('form.factory')->create(ArticleType::class, $article);

      if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {

          $article = $this->manageTagsType($article);

          $manager->persist($article);

          //On enregistre
          $manager->flush();
          $request->getSession()->getFlashBag()->add('notice', 'Article bien enregistrée.');
          return $this->redirectToRoute("ono_map_article_view", array(
            "id" => $article->getId(),
            "themes" => $themes
          ));
      }

      $routeName = $request->get('_route');
      if($routeName === "ono_admin_add_article") {
        return $this->render('OnoMapBundle:Admin:add-article.html.twig', array(
          "article" => $article,
          "form" => $form->createView()
        ));
      }

      return $this->render('OnoMapBundle:Article:add.html.twig', array(
        'form' => $form->createView(),
        "themes" => $themes
      ));
    }

    return $this->redirectToRoute("ono_map_article_index");
  }
  public function editAction(Request $request){
    $numId = (int) $request->attributes->all()["id"];
    $manager =$this->getDoctrine()->getManager();
    $repoArticle = $manager->getRepository("OnoMapBundle:Article");
    $themRepo = $manager->getRepository("OnoMapBundle:Theme");
    $tagRepo = $manager->getRepository("OnoMapBundle:Tag");
    $themes = $themRepo->findAll();

    $article = $repoArticle->find($numId);
    $user = $this->get('security.token_storage')->getToken()->getUser();

    if($article && $user instanceof User && $user->getId() === $article->getUser()->getId()){
      if($this->container->get('security.authorization_checker')->isGranted('ROLE_EDITOR')){
        $form = $this->get('form.factory')->create(ArticleType::class, $article);
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
          $article = $this->manageTagsType($article);
          $manager->persist($article);
          //On enregistre
          $manager->flush();
          $request->getSession()->getFlashBag()->add('notice', 'Article bien modifié.');
          return $this->redirectToRoute("ono_map_article_view", array(
            "id" => $article->getId(),
            "themes" => $themes
          ));
        }

        $routeName = $request->get('_route');
        if($routeName === "ono_admin_edit_article") {
          return $this->render('OnoMapBundle:Admin:edit-article.html.twig', array(
            "article" => $article,
            "form" => $form->createView()
          ));
        }
        return $this->render('OnoMapBundle:Article:edit.html.twig', array(
          'form' => $form->createView(),
          "themes" => $themes,
          "article" => $article
        ));
      }
    }

    return $this->redirectToRoute("ono_map_article_view", array(
      "id" => $numId
    ));
  }



  public function deleteAction(Request $request)
  {
      $numId = (int) $request->attributes->all()["id"];
      $manager = $this->getDoctrine()->getManager();

      // On récupère l'annonce $numId
      $article = $manager->getRepository('OnoMapBundle:Article')->find($numId);

      if (null === $article) {
        throw new NotFoundHttpException("Le pays d'id ".$numId." n'existe pas.");
      }

      // On crée un formulaire vide, qui ne contiendra que le champ CSRF
      // Cela permet de protéger la suppression d'annonce contre cette faille
      $form = $this->createFormBuilder()->getForm();

      if ($form->handleRequest($request)->isValid()) {
        $manager->remove($article);
        $manager->flush();

        $request->getSession()->getFlashBag()->add('info', "Le pays a bien été supprimée.");

        return $this->redirect($this->generateUrl('ono_admin_list_articles'));
      }

      // Si la requête est en GET, on affiche une page de confirmation avant de supprimer
      return $this->render('OnoMapBundle:Admin:delete.html.twig', array(
        'object' => $article,
        'title' => $article->getTitle(),
        'pathDelete' => "ono_admin_delete_article",
        'form'   => $form->createView()
      ));
  }

  private function manageTagsType($article){
    $manager = $this->getDoctrine()->getManager();
    $tagRepo = $manager->getRepository("OnoMapBundle:Tag");
    $repoArticle = $manager->getRepository("OnoMapBundle:Article");

    //Stocke les tags
    $tags=$article->getTags();

    //Supprime les tags actuel de l'article
    $article->removeTags();

    for($i=0; $i<count($tags); $i++){
      //Détache du persistage les tags édités du formulaire
      dump($tags);
      // exit;
      $manager->detach($tags[$i]);

      //Initialise un tag
      $tagSearch = null;

      //Test si le libellé existe dans la bdd
      $tagSearch = $tagRepo->findOneBy(array(
        "libTag" => $tags[$i]->getlibTag()
      ));

      //Si il n'y a pas de tag dans la bdd on le crée
      if(!$tagSearch){
        $tagSearch = new Tag;
        $tagSearch->setLibTag($tags[$i]->getlibTag());
        $tagSearch->setUsedCount(1);
      }

      //Si le tag est déja dans la bdd, on met à jour le comptage
      if($tagSearch->getId()){
        $amount = $repoArticle->getNbUsedCount($tagSearch->getId())[0]["amount"];
        $tagSearch->setUsedCount($amount+1);
      }

      //On rajoute le nouveau tag à l'article
      $article->addTag($tagSearch);
    }

    return $article;
  }
}
