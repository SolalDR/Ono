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
use Ono\MapBundle\Entity\Resource;
use Ono\UserBundle\Entity\User;
use Ono\MapBundle\Form\ArticleType;

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class ArticleController extends Controller
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
    $tagRepo = $manager->getRepository("OnoMapBundle:Tag");

    $article = $repoArticle->find($numId);
    if(isset($numTag)){
      $helper = $this->container->get('vich_uploader.templating.helper.uploader_helper');
      $tag = $tagRepo->find($numTag);
      $articles = $tag->getArticles();
      $articles = $articles->toArray();

      foreach ($articles as $key => $value) {
        if($value == $article) {
          array_splice($articles, $key, 1);
        }
      }
      if (count($articles)>0){
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

  public function likeAction(Request $request){
    $numId = (int) $request->attributes->all()["id"];
    $manager = $this->getDoctrine()->getManager();
    $repoArticle = $manager->getRepository("OnoMapBundle:Article");
    $article = $repoArticle->find($numId);

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
          if($request->isXmlHttpRequest()){
            return new Response($this->getXhrLikesResponse(true, $article->getNbLikes(), $article->getId()));
          }
        }
      }

      return $this->redirectToRoute('ono_map_article_view', array('id' => $article->getId()));
    }
    //L'utilisateur n'est pas authentifié
    return $this->redirectToRoute('ono_map_homepage');
  }

  public function unlikeAction(Request $request){
    $numId = (int) $request->attributes->all()["id"];
    $manager = $this->getDoctrine()->getManager();
    $repoArticle = $manager->getRepository("OnoMapBundle:Article");
    $article = $repoArticle->find($numId);

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
          if($request->isXmlHttpRequest()){
            return  new Response($this->getXhrLikesResponse(false, $article->getNbLikes(), $article->getId()));
          }
          return $this->redirectToRoute('ono_map_article_view', array('id' => $article->getId()));
        }
      }
      return $this->redirectToRoute('ono_map_article_view', array('id' => $article->getId()));
    }
    //L'utilisateur n'est pas authentifié
    return $this->redirectToRoute('ono_map_homepage');
  }

  public function deleteAction(Request $request)
  {
      $numId = (int) $request->attributes->all()["id"];
      $manager = $this->getDoctrine()->getManager();

      // On récupère l'annonce $numId
      $article = $manager->getRepository('OnoMapBundle:Article')->find($numId);
      // $resources = $manager->getRepository('OnoMapBundle:Resource');

      if (null === $article) {
        throw new NotFoundHttpException("L'article d'id ".$numId." n'existe pas.");
      }

      // On crée un formulaire vide, qui ne contiendra que le champ CSRF
      // Cela permet de protéger la suppression d'annonce contre cette faille
      $form = $this->createFormBuilder()->getForm();

      if ($form->handleRequest($request)->isValid()) {
        $articleResources = $article->getResources();
        $count = count($articleResources);
        for ($i = $count-1; $i >= 0; $i--) {
          $article->removeResource($articleResources[$i]);
        }

        $manager->remove($article);
        $manager->flush();


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

  public function popupAction(Request $request){
    if($this->container->get('security.authorization_checker')->isGranted('ROLE_USER')){
      $user = $this->get('security.token_storage')->getToken()->getUser();
    }

    // On récupère les paramètres de route
    $numArt = (int) $request->attributes->all()["id"];
    $numTag = (int) $request->attributes->all()["tag"];

    // On prépare le lien pour ajouter une indéfinition
    $indefPersoLink = $this->generateUrl('ono_map_indefinition_add', array(
      "article_id" => $numArt,
      "tag_id" => $numTag
    ));

    $seeMoreLink = $this->generateUrl('ono_map_tag_view', array(
      "article_id" => $numArt,
      "id" => $numTag
    ));

    // On récupère les repositories nécessaires
    $manager = $this->getDoctrine()->getManager();
    $repoArticle = $manager->getRepository("OnoMapBundle:Article");
    $repoTag = $manager->getRepository("OnoMapBundle:Tag");

    // On récupère les entités correspondantes
    $article = $repoArticle->find($numArt);
    $tag = $repoTag->find($numTag);
    if($tag){
      // On récupère tous les articles du tag en question sauf celui en cours
      $tagArticles = $tag->getArticles()->toArray();
      $key = 0;
      foreach ($tagArticles as $tagArticle) {
        if ($tagArticle == $article) {
          array_splice($tagArticles, $key, 1);
        }
        $key++;
      }

      // Randomisation & si moins de 3, on prend tous les articles, sinon les 3 premiers
      $articles = [];
      if(count($tagArticles) > 0) {
        shuffle($tagArticles);
        if(count($tagArticles) < 3) {
          $articles = $tagArticles;
        } else {
          $articles = [$tagArticles[0], $tagArticles[1], $tagArticles[2]];
        }
      }

      $indefinitions = [];
      $tagIndefs = $tag->getIndefinitions()->toArray();
      if(count($tagIndefs) > 0) {
        shuffle($tagIndefs);
        foreach ($tagIndefs as $tagIndef) {
          if (isset($user) && $user === $tagIndef->getUser()) {
            $indefEditLink = $this->generateUrl('ono_map_indefinition_edit', array(
              "article_id" => $numArt,
              "tag_id" => $numTag,
              "id" => $tagIndef->getId()
            ));
          } else {
            $indefEditLink = null;
          }
          $indefinitions[] = [
            "content" => $tagIndef->getContent(),
            "author" => $tagIndef->getAuthor(),
            "indefEditLink" => $indefEditLink
          ];
        }
      }

      if($request->isXmlHttpRequest()){
        // REPONSE XHR
        return new Response($this->getPopupResponse($tag->getLibTag(), $indefinitions, $indefPersoLink, $seeMoreLink, $articles));
      }
    }
    return $this->redirectToRoute('ono_map_article_view', array('id' => $article->getId()));
  }

  private function getPopupResponse($libTag, $indefinitions, $indefPersoLink, $seeMoreLink, $articles) {
    $helper = $this->container->get('vich_uploader.templating.helper.uploader_helper');
    // Pour chaque article, on récupère le titre, l'image et le lien
    $arts = [];
    for($i = 0 ; $i < count($articles) ; $i++) {
      $arts[$i]["link"] = $this->generateUrl('ono_map_article_view', array("id" => $articles[$i]->getId()));
      $arts[$i]["title"] = $articles[$i]->getTitle();
      $resources = $articles[$i]->getResources();
      if(count($resources) != 0) {
        $arts[$i]["image"] = $helper->asset($resources[0], 'file', 'Ono\\MapBundle\\Entity\\Resource');
      } else {
        $arts[$i]["image"] = null;
      }
    }

    // On hydrate le tableau qui sera encodé en JSON pour la réponse XHR
    $render = [];
    $render["libTag"] = $libTag;
    $render["indefinitions"] = $indefinitions;
    $render["indefPersoLink"] = $indefPersoLink;
    $render["seeMoreLink"] = $seeMoreLink;
    $render["articles"] = $arts;
    return json_encode($render);
  }

  private function getXhrLikesResponse($isLiking, $nbLikes, $id){
    $render = [];
    $render["nbLike"] = $nbLikes;
    if($isLiking){
      $render["liking"] = true;
      $render["nextRoute"] = $this->generateUrl(
            'ono_map_article_unlike',
            array('id' => $id)
        );
      return json_encode($render);
    }
    $render["liking"] = false;
    $render["nextRoute"] =  $this->generateUrl(
          'ono_map_article_like',
          array('id' => $id)
      );
    return json_encode($render);
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
