<?php

namespace Ono\MapBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\sfWebRequest;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Finder\Exception\AccessDeniedException;

//Response already use;
use Ono\MapBundle\Entity\Response as ResponseQ;
use Ono\MapBundle\Entity\Question;

class QuestionController extends Controller
{
    //Action affichant la page d'accueil avec la carte
    public function indexAction(Request $request, $ids = null)
    {
      //Initialisation
      $em = $this->getDoctrine()->getManager();
      $questionRepo = $em->getRepository("OnoMapBundle:Question");
      $themRepo = $em->getRepository("OnoMapBundle:Theme");

      //On récupère les objets
      $questions = $questionRepo->findAll();
      $themes = $themRepo->findAll();

      //On retourne le tout
      return $this->render('OnoMapBundle:Question:index.html.twig', array(
        "questions" => $questions,
        "themes" => $themes
      ));
    }



    public function viewAction($id)
    {
      $em = $this->getDoctrine()->getManager();
      $repoQ = $em->getRepository("OnoMapBundle:Question");
      $repoR = $em->getRepository("OnoMapBundle:Response");
      $themRepo = $em->getRepository("OnoMapBundle:Theme");

      $question = $repoQ->find($id);
      if($question === null){
        throw new NotFoundHttpException("La question à afficher n'existe pas.");
      }
      $responses = $repoR->findBy(array("question"=>$question));
      $themes = $themRepo->findAll();

      return $this->render("OnoMapBundle:Question:view.html.twig", array(
        "question" => $question,
        "responses" =>  $responses,
        "themes" => $themes
      ));
    }

}
