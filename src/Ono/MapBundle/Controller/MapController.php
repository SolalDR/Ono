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
      //On affiche u formulaire
      $response = new ResponseQ;

      if($this->container->get('security.authorization_checker')->isGranted('ROLE_USER')){
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $response->updateUser($user);
        $form = $this->get('form.factory')->create(ResponseLogType::class, $response);
      } else {
        $form = $this->get('form.factory')->create(ResponseType::class, $response);
      }

      //Initialisation
      $em = $this->getDoctrine()->getManager();
      $questionRepo = $em->getRepository("OnoMapBundle:Question");
      $responseRepo = $em->getRepository("OnoMapBundle:Response");
      $serializer = $this->get('serializer');
      $encoder = new JsonEncoder();
      $normalizer = new ObjectNormalizer();

      //On récupère les questions et les réponses
      $questions = $questionRepo->findAll();
      $responses= $responseRepo->findBy(array("question"=>$questions[0]));

      //On Récupère tout les thèmes
      $themRepo = $em->getRepository("OnoMapBundle:Theme");
      $themes = $themRepo->findAll();

      //On prépare le json
      $normalizer->setCircularReferenceHandler(function ($responses) {
          return $responses->getId();
      });
      $serializer = new Serializer(array($normalizer), array($encoder));
      $json = $serializer->serialize($questions, 'json');

      //On retourne le tout
      return $this->render('OnoMapBundle:Map:index.html.twig', array(
        "json" =>$json,
        "themes" => $themes,
        "form" => $form->createView()
      ));
    }

    //Action mettant à jour la page d'accueil à l'aide d'une XHR et d'un retour en JSON
    public function updateAction(Request $request)
    {
      //On vérifie que la requête soit XHR
      if($request->request->get("xhr")){

        //Initialisation
        $em = $this->getDoctrine()->getManager();
        $questionRepo = $em->getRepository("OnoMapBundle:Question");
        $responseRepo = $em->getRepository("OnoMapBundle:Response");

        //Traitement des filtres
        $filters = (array) json_decode($request->request->get("json"));
        //Si on a des thèmes à filtrer on utilise cette méthode
        if(count($filters["themes"])>0){
          $questions = $questionRepo->getQuestionsWithThemes($filters["themes"]);
        //Sinon on ne fait pas de distinction
        } else {
          $questions = $questionRepo->findAll();
        }
        $responses= $responseRepo->findAll();
        for($i=0; $i<count($questions); $i++){
          if(count($questions[$i]->getResponses())<1){
            array_splice($questions, $i, 1);
          }
        }

        //Prepare json
        $serializer = $this->get('serializer');
        $encoder = new JsonEncoder();
        $normalizer = new ObjectNormalizer();
        $normalizer->setCircularReferenceHandler(function ($responses) {
            return $responses->getId();
        });
        $serializer = new Serializer(array($normalizer), array($encoder));
        $json = $serializer->serialize($questions, 'json');

        //Return response
        return new Response($json);

      } else {
        return new Response("Error");
      }
    }

    public function menuAction($route){
      $em = $this->getDoctrine()->getManager();
      $themRepo = $em->getRepository("OnoMapBundle:Theme");
      $themes = $themRepo->findAll();
      //On retourne le tout
      return $this->render('OnoMapBundle:Templates:header.html.twig', array(
        "themes" => $themes,
        "current_route" => $route
      ));
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
