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

use Ono\MapBundle\Form\CountryType;
use Ono\MapBundle\Form\ResponseType;
use Ono\MapBundle\Form\ResponseAdminType;
use Ono\MapBundle\Form\QuestionType;
use Ono\MapBundle\Form\ThemeType;



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
    public function addThemeAction(Request $request){
      $em = $this->getDoctrine()->getManager();
      $theme = new Theme;

      $form = $this->get('form.factory')->create(ThemeType::class, $theme);
      if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
        $em->persist($theme);
        $em->flush();

        $request->getSession()->getFlashBag()->add('notice', 'Thème bien enregistrée.');

        return $this->redirectToRoute("ono_admin_list_theme");
      }
      return $this->render('OnoMapBundle:Admin:add-theme.html.twig', array(
        "form" => $form->createView()
      ));
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
    public function addQuestionAction(Request $request){
      $em = $this->getDoctrine()->getManager();
      $question = new Question;

      $form = $this->get('form.factory')->create(QuestionType::class, $question);
      if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
        $em->persist($question);
        $em->flush();

        $request->getSession()->getFlashBag()->add('notice', 'Question bien enregistrée.');

        return $this->redirectToRoute("ono_admin_list_question");
      }
      return $this->render('OnoMapBundle:Admin:add-question.html.twig', array(
        "form" => $form->createView()
      ));
    }

    public function listQuestionAction(){
      $em = $this->getDoctrine()->getManager();
      $questions = $em->getRepository("OnoMapBundle:Question")->findAll();

      return $this->render('OnoMapBundle:Admin:list-question.html.twig', array(
        "questions" => $questions
      ));
    }

    ////////////////////////////////////
    //        Response
    ///////////////////////////////////
    public function addResponseAction(Request $request){
      $em = $this->getDoctrine()->getManager();
      $response = new ResponseQ;

      $form = $this->get('form.factory')->create(ResponseAdminType::class, $response);
      if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
        $em->persist($response);
        $em->flush();

        $request->getSession()->getFlashBag()->add('notice', 'Réponse bien enregistrée.');

        return $this->redirectToRoute("ono_admin_list_response");
      }
      return $this->render('OnoMapBundle:Admin:add-response.html.twig', array(
        "form" => $form->createView()
      ));
      return $this->render('OnoMapBundle:Admin:add-response.html.twig', array());
    }

    public function listResponseAction(){
      $em = $this->getDoctrine()->getManager();
      $responses = $em->getRepository("OnoMapBundle:Response")->findAll();

      return $this->render('OnoMapBundle:Admin:list-response.html.twig', array(
        "responses" => $responses
      ));
    }

    ////////////////////////////////////
    //        Country
    ///////////////////////////////////
    public function addCountryAction(Request $request){
      $em = $this->getDoctrine()->getManager();
      $country = new Country;

      $form = $this->get('form.factory')->create(CountryType::class, $country);
      if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
        $em->persist($country);
        $em->flush();

        $request->getSession()->getFlashBag()->add('notice', 'Pays bien enregistrée.');

        return $this->redirectToRoute("ono_admin_list_country");
      }
      return $this->render('OnoMapBundle:Admin:add-country.html.twig', array(
        "form" => $form->createView()
      ));
    }

    public function listCountryAction(){
      $em = $this->getDoctrine()->getManager();
      $countries = $em->getRepository("OnoMapBundle:Country")->findAll();

      return $this->render('OnoMapBundle:Admin:list-country.html.twig', array(
        "countries" => $countries
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
}
