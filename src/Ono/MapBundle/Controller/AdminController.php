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
use Ono\MapBundle\Entity\Country;


use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;



class AdminController extends Controller
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


      return $this->render('OnoMapBundle:Admin:index.html.twig', array(
        "questions" => $questions,
        "themes" => $themes,
        "json" =>$json
      ));
    }

    ////////////////////////////////////
    //        Thèmes
    ///////////////////////////////////
    public function addThemeAction(){
      return $this->render('OnoMapBundle:Admin:add-theme.html.twig', array());
    }

    public function listThemeAction(){
      $em = $this->getDoctrine()->getManager();
      $themes = $em->getRepository("OnoMapBundle:Theme")->findAll();

      return $this->render('OnoMapBundle:Admin:list-theme.html.twig', array(
        "themes" => $themes
      ));
    }

    ////////////////////////////////////
    //        Questions
    ///////////////////////////////////
    public function addQuestionAction(){
      return $this->render('OnoMapBundle:Admin:add-question.html.twig', array());
    }

    public function listQuestionAction(){
      $em = $this->getDoctrine()->getManager();
      $questions = $em->getRepository("OnoMapBundle:Question")->findAll();

      return $this->render('OnoMapBundle:Admin:list-question.html.twig', array(
        "questions" => $questions
      ));
    }

    ////////////////////////////////////
    //        Users
    ///////////////////////////////////
    public function addUserAction(){
      return $this->render('OnoMapBundle:Admin:add-user.html.twig', array());
    }

    public function listUserAction(){
      return $this->render('OnoMapBundle:Admin:list-user.html.twig', array());
    }

    ////////////////////////////////////
    //        Response
    ///////////////////////////////////
    public function addResponseAction(){
      return $this->render('OnoMapBundle:Admin:add-response.html.twig', array());
    }

    public function listResponseAction(){
      $em = $this->getDoctrine()->getManager();
      $responses = $em->getRepository("OnoMapBundle:Response")->findAll();

      return $this->render('OnoMapBundle:Admin:list-response.html.twig', array(
        "responses" => $responses
      ));
    }

}
