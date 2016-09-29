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


class MapController extends Controller
{
    public function indexAction()
    {
      $em = $this->getDoctrine()->getManager();
      $questionRepo = $em->getRepository("OnoMapBundle:Question");

      $questions = $questionRepo->findAll();

        return $this->render('OnoMapBundle:Map:index.html.twig', array(
          "questions" => $questions
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

    public function addResponseAction($id)
    {
      $em = $this->getDoctrine()->getManager();
      $question = $em->getRepository("OnoMapBundle:Question")->find($id);

      //Si il n'y a pas de réponse
      if($question === null){
        throw new NotFoundHttpException("La question à répondre n'existe pas.");
      }

      $response = new ResponseQ();
      $response->setQuestion($question);
      $response->setAuthor("Admin");
      $response->setDtcreation(new \DateTime);
      $response->setContent("Ceci est la réponse à la question : ".$question->getLibQuestion());

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
