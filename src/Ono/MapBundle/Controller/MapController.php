<?php

namespace Ono\MapBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\sfWebRequest;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Finder\Exception\AccessDeniedException;

//Response already use;
use Ono\MapBundle\Entity\Response as ResponseQ;
use Ono\MapBundle\Entity\Question;
use Ono\MapBundle\Entity\Theme;
use Ono\MapBundle\Form\ResponseType;
use Ono\MapBundle\Form\ResponseLogType;


use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;



class MapController extends Controller
{
    //Action affichant la page d'accueil avec la carte
    public function indexAction(Request $request, $ids = null)
    {

      $manager = $this->getDoctrine()->getManager();
      $questionRepo = $manager->getRepository("OnoMapBundle:Question");
      $responseRepo = $manager->getRepository("OnoMapBundle:Response");
      $articleRepo = $manager->getRepository("OnoMapBundle:Article");
      $themRepo = $manager->getRepository("OnoMapBundle:Theme");
      $themesActive = $request->getSession()->get("themes");

      $isFirst = $request->getSession()->get("loggedVisit");
      if(!$this->container->get('security.authorization_checker')->isGranted('ROLE_USER') && !$isFirst){
        return $this->redirectToRoute("fos_user_security_login");
      }

      if(count($themesActive)>0){
        $questions = $questionRepo->getQuestionsWithThemes($themesActive);
        $articles = $articleRepo->getArticlesWithThemes($themesActive);
      //Sinon on ne fait pas de distinction
      } else {
        $questions = $questionRepo->findAll();
        $articles = $articleRepo->findAll();
      }
      if(!isset($questions) || !count($questions)){
        $questions = $questionRepo->findAll();
      }
      if(!isset($articles) || !count($articles)){
        $articles = $articleRepo->findAll();
      }


      $themes = $themRepo->findAll();


      $json = $this->manageJson($articles, $questions, $responseRepo);

      return $this->render('OnoMapBundle:Map:index.html.twig', array(
        "json" =>$json,
        "themes" => $themes
      ));
    }

    //Action mettant à jour la page d'accueil à l'aide d'une XHR et d'un retour en JSON
    public function updateAction(Request $request)
    {
      //Initialisation
      $manager = $this->getDoctrine()->getManager();
      $questionRepo = $manager->getRepository("OnoMapBundle:Question");
      $responseRepo = $manager->getRepository("OnoMapBundle:Response");
      $articleRepo = $manager->getRepository("OnoMapBundle:Article");
      $themesActive = $request->getSession()->get("themes");


      if(count($themesActive)>0){
      //Si on a des thèmes à filtrer on utilise cette méthode
        $questions = $questionRepo->getQuestionsWithThemes($themesActive);
        $articles = $articleRepo->getArticlesWithThemes($themesActive);
      //Sinon on ne fait pas de distinction
      } else {
        $questions = $questionRepo->findAll();
        $articles = $articleRepo->findAll();
      }
      if(!isset($questions) || !count($questions)){
        $questions = $questionRepo->findAll();
      }
      if(!isset($articles) || !count($articles)){
        $articles = $articleRepo->findAll();
      }


      $responses= $responseRepo->findAll();
      for($i=0; $i<count($questions); $i++){
        if(count($questions[$i]->getResponses())<1){
          array_splice($questions, $i, 1);
        }
      }

      $json = $this->manageJson($articles, $questions, $responseRepo);

      //Return response
      return new Response($json);

    }

    public function menuAction($route){
      $manager = $this->getDoctrine()->getManager();
      $themRepo = $manager->getRepository("OnoMapBundle:Theme");
      $themes = $themRepo->findAll();
      //On retourne le tout
      return $this->render('OnoMapBundle:Templates:header.html.twig', array(
        "themes" => $themes,
        "current_route" => $route
      ));
    }


    ////////////////////////////////////////////
    //
    //  JSON + FILEREADER + CIRCULARHANDLER
    //
    ///////////////////////////////////////////

    private function manageJson($articles, $questions, $responseRepo){
      if($questions){
        $circularHandler= $responseRepo->findBy(array("question"=>$questions[0]));
        $questions = $this->deleteResponsesFiles($questions);
        $json1 = $this->getJsonFor($questions, $circularHandler);
      } else {
        $json1 = "{}";
      }
      if($articles){
        $articles = $this->deleteArticlesFiles($articles);
        $articles = $this->deleteIndefs($articles);
        $json2 = $this->getJsonFor($articles, $articles);
      } else {
        $json2 = "{}";
      }
      $json= '{"articles": '.$json2.', "questions": '.$json1.'}';
      return $json;
    }

    private function deleteArticlesFiles($articles){
      if($articles) {
        for($i=0; $i<count($articles); $i++){
          $articles[$i]->temporyDeleteResource();
          $articles[$i]->temporyDeleteUser();
          // for($j=0; $j<count($resources); $j++){
          //   $resources[$j]->setFile(null);
          // }
        }
      }
      // dump($articles);
      // exit;
      return $articles;
    }

    private function deleteIndefs($articles) {
      for($i=0; $i<count($articles); $i++){
        $articles[$i]->temporyDeleteTagsIndefs();
      }
      return $articles;
    }

    private function deleteResponsesFiles($questions){

      for($i=0; $i<count($questions); $i++){
        $responses = $questions[$i]->getResponses();
        for($j=0; $j<count($responses); $j++){
          $responses[$j]->temporyDeleteResource();
          $responses[$j]->temporyDeleteUser();
        }
      }

      return $questions;
    }

    private function getJsonFor($object, $normalizerRef=null){
      $serializer = $this->get('serializer');
      $encoder = new JsonEncoder();
      $normalizer = new ObjectNormalizer();
      if($normalizerRef){
        $normalizer->setCircularReferenceHandler(function ($normalizerRef) {
            return $normalizerRef->getId();
        });
      }
      $serializer = new Serializer(array($normalizer), array($encoder));
      $json = $serializer->serialize($object, 'json');
      return $json;
    }



    /////////////////////////////////
    //          LANGUAGE
    /////////////////////////////////


    public function changeLanguageAction($cdLang)
    {
      return $this->redirectToRoute('ono_map_homepage', array(
        "ids" => $ids
      ));
    }
}
