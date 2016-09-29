<?php

namespace Ono\MapBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Finder\Exception\AccessDeniedException;


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

    public function viewQuestionAction($idQuestion)
    {
      return;
    }

    public function addQuestionAction()
    {
      return;
    }

    public function editQuestionAction($idQuestion)
    {
      return;
    }

    public function deleteQuestionAction($idQuestion)
    {
      return;
    }

    /////////////////////////////////
    //          RESPONSE
    /////////////////////////////////

    public function viewResponseAction($idResponse)
    {
      return;
    }

    public function addResponseAction()
    {
      return;
    }

    public function editResponseAction($idResponse)
    {
      return;
    }

    public function deleteResponseAction($idResponse)
    {
      return;
    }


}
