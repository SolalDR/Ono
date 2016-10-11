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
      $form = $this->get('form.factory')->create(ResponseType::class, $response);


      //Initialisation
      $em = $this->getDoctrine()->getManager();
      $questionRepo = $em->getRepository("OnoMapBundle:Question");
      $responseRepo = $em->getRepository("OnoMapBundle:Response");
      $themesRepo = $em->getRepository("OnoMapBundle:Theme");
      $serializer = $this->get('serializer');
      $encoder = new JsonEncoder();
      $normalizer = new ObjectNormalizer();

      //On récupère les objets
      $questions = $questionRepo->findAll();
      $responses= $responseRepo->findBy(array("question"=>$questions[0]));
      $themes = $themesRepo->findAll();

      //On prépare le json
      $normalizer->setCircularReferenceHandler(function ($responses) {
          return $responses->getId();
      });
      $serializer = new Serializer(array($normalizer), array($encoder));
      $json = $serializer->serialize($questions, 'json');

      //On retourne le tout
      return $this->render('OnoMapBundle:Map:index.html.twig', array(
        "themes" => $themes,
        "json" =>$json,
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


    /////////////////////////////////
    //          QUESTION
    /////////////////////////////////

    public function viewQuestionAction($id)
    {
      $em = $this->getDoctrine()->getManager();
      $repoQ = $em->getRepository("OnoMapBundle:Question");
      $repoR = $em->getRepository("OnoMapBundle:Response");

      $question = $repoQ->find($id);
      if($question === null){
        throw new NotFoundHttpException("La question à afficher n'existe pas.");
      }

      $responses = $repoR->findBy(array("question"=>$question));

      return $this->render("OnoMapBundle:Map:questionView.html.twig", array(
        "question" => $question,
        "responses" =>  $responses
      ));
    }

    /////////////////////////////////
    //          RESPONSE
    /////////////////////////////////

    public function viewResponseAction($id)
    {
      $em = $this->getDoctrine()->getManager();
      $repo = $em->getRepository("OnoMapBundle:Response");

      $response = $repo->find($id);
      if($response === null){
        throw new NotFoundHttpException("La réponse à afficher n'existe pas.");
      }

      return $this->render("OnoMapBundle:Map:responseView.html.twig", array(
        "response" => $response
      ));
    }


    public function addResponseAction($id, Request $request)
    {

      $em =$this->getDoctrine()->getManager();
      $repoQuestion = $em->getRepository("OnoMapBundle:Question");
      $question = $repoQuestion->find($id);

      //On crée le formulaire d'ajout de réponse, qu'on modifiera dynamiquement après
      if($question){
        $response = new ResponseQ;
        $response->setDtcreation(new \DateTime());
        $response->setQuestion($question);
        $form = $this->get('form.factory')->create(ResponseType::class, $response);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
          //On persist la réponse
          $em->persist($response);

          //On rajoute la réponse à la question
          $question->addResponse($response);

          //On enregistre
          $em->flush();

          $request->getSession()->getFlashBag()->add('notice', 'Réponse bien enregistrée.');

          return new JsonResponse(array(
            "type"=>"notice",
            "title" => "Message bien enregistré"
          ));
        }
      } else {
        //On renvoie une erreur
      }




      $em = $this->getDoctrine()->getManager();
      $question = $em->getRepository("OnoMapBundle:Question")->find($id);
      // $country = $em->getRepository("OnoMapBundle:Country")->findOneBy(array("libCountry"=>"FRANCE"));

      //Si il n'y a pas de réponse
      if($question === null){
        throw new NotFoundHttpException("La question à répondre n'existe pas.");
      }

      //On instancie la réponse
      $response = new ResponseQ;

      //On définis la date courante et la question affilié
      $response->setDtcreation(new \DateTime());
      $response->setQuestion($question);
      $response->setDtnaissance(new \DateTime());

      //On crée le formulaire
      $form = $this->get('form.factory')->create(ResponseType::class, $response);

      if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
        $em = $this->getDoctrine()->getManager();

        //On persist la réponse
        $em->persist($response);

        //On rajoute la réponse à la question
        $question->addResponse($response);

        //On enregistre
        $em->flush();

        $request->getSession()->getFlashBag()->add('notice', 'Réponse bien enregistrée.');

        return $this->redirectToRoute('ono_map_response_view', array('id' => $response->getId()));
      }

      return $this->render('OnoMapBundle:Map:addResponse.html.twig', array(
        'form' => $form->createView(),
        'question' =>$question
      ));

      $em->persist($response);
      $em->flush();

      return $this->redirectToRoute('ono_map_response_view', array(
        "id" => $response->getId()
      ));

    }

    /////////////////////////////////
    //          LANGUAGE
    /////////////////////////////////

    public function changeLanguageAction($cdLang)
    {
      return $his->redirectToRoute('ono_map_homepage', array(
        "ids" => $ids
      ));
    }
}
