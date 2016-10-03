<?php

namespace Ono\MapBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
    public function indexAction()
    {
      $em = $this->getDoctrine()->getManager();
      $questionRepo = $em->getRepository("OnoMapBundle:Question");
      $responseRepo = $em->getRepository("OnoMapBundle:Response");
      $themesRepo = $em->getRepository("OnoMapBundle:Theme");
      $serializer = $this->get('serializer');


      $questions = $questionRepo->findAll();
      $themes = $themesRepo->findAll();
      $responses= $responseRepo->findBy(array("question"=>$questions[0]));


      $encoder = new JsonEncoder();
      $normalizer = new ObjectNormalizer();

      $normalizer->setCircularReferenceHandler(function ($responses) {
          return $responses->getId();
      });
      $serializer = new Serializer(array($normalizer), array($encoder));
      $json = $serializer->serialize($questions, 'json');


      return $this->render('OnoMapBundle:Map:index.html.twig', array(
        "questions" => $questions,
        "themes" => $themes,
        "json" =>$json
      ));
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

    public function addQuestionAction()
    {
      return;
    }

    public function editQuestionAction($id)
    {
      return;
    }

    public function deleteQuestionAction($id)
    {
      return;
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

    public function editResponseAction($id)
    {
      return;
    }

    public function deleteResponseAction($id)
    {
      return;
    }


}
