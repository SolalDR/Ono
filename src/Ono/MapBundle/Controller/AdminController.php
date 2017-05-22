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
use Ono\MapBundle\Entity\Indefinition;

use Ono\MapBundle\Form\ResponseType;
use Ono\MapBundle\Form\ResponseAdminType;
use Ono\MapBundle\Form\IndefinitionType;
use Ono\MapBundle\Form\IndefinitionAdminType;
use Ono\MapBundle\Form\QuestionType;

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;


class AdminController extends Controller
{
    public function indexAction()
    {
      $manager = $this->getDoctrine()->getManager();
      $questionRepo = $manager->getRepository("OnoMapBundle:Question");
      $responseRepo = $manager->getRepository("OnoMapBundle:Response");
      $themesRepo = $manager->getRepository("OnoMapBundle:Theme");
      $serializer = $this->get('serializer');


      $questions = $questionRepo->findAll();
      $themes = $themesRepo->findAll();


      return $this->render('OnoMapBundle:Admin:index.html.twig', array(
        "questions" => $questions,
        "themes" => $themes
      ));
    }

    ////////////////////////////////////
    //        Questions
    ///////////////////////////////////


    public function addQuestionAction(Request $request){
      $manager = $this->getDoctrine()->getManager();
      $question = new Question;

      $form = $this->get('form.factory')->create(QuestionType::class, $question);
      if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
        $manager->persist($question);
        $manager->flush();

        $request->getSession()->getFlashBag()->add('notice', 'Question bien enregistrée.');

        return $this->redirectToRoute("ono_admin_list_question");
      }
      return $this->render('OnoMapBundle:Admin:add-question.html.twig', array(
        "form" => $form->createView()
      ));
    }

    public function listQuestionAction(){
      $manager = $this->getDoctrine()->getManager();
      $questions = $manager->getRepository("OnoMapBundle:Question")->findAll();

      return $this->render('OnoMapBundle:Admin:list-question.html.twig', array(
        "questions" => $questions
      ));
    }


      public function editQuestionAction(Request $request){
        $numId = (int) $request->attributes->all()["id"];

          $manager = $this->getDoctrine()->getManager();
          $question = $manager->getRepository("OnoMapBundle:Question")->find($numId);

          $form = $this->get('form.factory')->create(QuestionType::class, $question);
          if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $manager->persist($question);
            $manager->flush();

            $request->getSession()->getFlashBag()->add('notice', 'Question bien modifié.');

            return $this->redirectToRoute("ono_admin_list_question");
          }

          return $this->render('OnoMapBundle:Admin:edit-question.html.twig', array(
            "form" => $form->createView(),
            "question" => $question
          ));
        }

        public function deleteQuestionAction(Request $request)
        {
          $numId = (int) $request->attributes->all()["id"];

            $manager = $this->getDoctrine()->getManager();

            // On récupère l'annonce $numId
            $question = $manager->getRepository('OnoMapBundle:Question')->find($numId);

            if (null === $question) {
              throw new NotFoundHttpException("La question d'id ".$numId." n'existe pas.");
            }

            // On crée un formulaire vide, qui ne contiendra que le champ CSRF
            // Cela permet de protéger la suppression d'annonce contre cette faille
            $form = $this->createFormBuilder()->getForm();

            if ($form->handleRequest($request)->isValid()) {
              $manager->remove($question);
              $manager->flush();

              $request->getSession()->getFlashBag()->add('info', "La question a bien été supprimée.");

              return $this->redirect($this->generateUrl('ono_admin_list_question'));
            }

            // Si la requête est en GET, on affiche une page de confirmation avant de supprimer
            return $this->render('OnoMapBundle:Admin:delete.html.twig', array(
              'object' => $question,
              'title' => $question->getLibQuestion(),
              'pathDelete' => "ono_admin_delete_question",
              'form'   => $form->createView()
            ));
        }

    ////////////////////////////////////
    //        Response
    ///////////////////////////////////
    public function addResponseAction(Request $request){
      $manager = $this->getDoctrine()->getManager();
      $response = new ResponseQ;

      $form = $this->get('form.factory')->create(ResponseAdminType::class, $response);
      if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
        $manager->persist($response);
        $manager->flush();

        $request->getSession()->getFlashBag()->add('notice', 'Réponse bien enregistrée.');

        return $this->redirectToRoute("ono_admin_list_response");
      }
      return $this->render('OnoMapBundle:Admin:add-response.html.twig', array(
        "form" => $form->createView()
      ));
      return $this->render('OnoMapBundle:Admin:add-response.html.twig', array());
    }

    public function listResponseAction(){
      $manager = $this->getDoctrine()->getManager();
      $responses = $manager->getRepository("OnoMapBundle:Response")->findAll();

      return $this->render('OnoMapBundle:Admin:list-response.html.twig', array(
        "responses" => $responses
      ));
    }

    public function editResponseAction(Request $request){
      $numId = (int) $request->attributes->all()["id"];

        $manager = $this->getDoctrine()->getManager();
        $response = $manager->getRepository("OnoMapBundle:Response")->find($numId);

        $form = $this->get('form.factory')->create(ResponseType::class, $response);
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
          $manager->persist($response);
          $manager->flush();

          $request->getSession()->getFlashBag()->add('notice', 'Réponse bien modifié.');

          return $this->redirectToRoute("ono_admin_list_response");
        }

        return $this->render('OnoMapBundle:Admin:edit-response.html.twig', array(
          "form" => $form->createView(),
          "response" => $response
        ));
      }

      public function deleteResponseAction(Request $request)
      {
        $numId = (int) $request->attributes->all()["id"];

          $manager = $this->getDoctrine()->getManager();

          // On récupère l'annonce $numId
          $response = $manager->getRepository('OnoMapBundle:Response')->find($numId);

          if (null === $response) {
            throw new NotFoundHttpException("La réponse d'id ".$numId." n'existe pas.");
          }

          // On crée un formulaire vide, qui ne contiendra que le champ CSRF
          // Cela permet de protéger la suppression d'annonce contre cette faille
          $form = $this->createFormBuilder()->getForm();

          if ($form->handleRequest($request)->isValid()) {
            $manager->remove($response);
            $manager->flush();

            $request->getSession()->getFlashBag()->add('info', "La réponse a bien été supprimée.");

            return $this->redirect($this->generateUrl('ono_admin_list_response'));
          }

          // Si la requête est en GET, on affiche une page de confirmation avant de supprimer
          return $this->render('OnoMapBundle:Admin:delete.html.twig', array(
            'object' => $response,
            'title' => $response->getQuestion()->getLibQuestion(),
            'pathDelete' => "ono_admin_delete_response",
            'form'   => $form->createView()
          ));
      }

      ////////////////////////////////////
      //        Indefinition
      ///////////////////////////////////

      public function addIndefinitionAction(Request $request){
        $manager = $this->getDoctrine()->getManager();
        $indefinition = new Indefinition;

        $form = $this->get('form.factory')->create(IndefinitionAdminType::class, $indefinition);
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
          $user = $this->container->get('security.token_storage')->getToken()->getUser();
          $indefinition->setUser($user);
          $manager->persist($indefinition);
          $manager->flush();

          $request->getSession()->getFlashBag()->add('notice', 'Indéfinition bien enregistrée.');

          return $this->redirectToRoute("ono_admin_list_indefinitions");
        }
        return $this->render('OnoMapBundle:Admin:add-indefinition.html.twig', array(
          "form" => $form->createView()
        ));
      }

      public function listIndefinitionAction(){
        $manager = $this->getDoctrine()->getManager();
        $indefinitions = $manager->getRepository("OnoMapBundle:Indefinition")->findAll();

        return $this->render('OnoMapBundle:Admin:list-indefinition.html.twig', array(
          "indefinitions" => $indefinitions
        ));
      }

      public function editIndefinitionAction(Request $request){
        $numId = (int) $request->attributes->all()["id"];

          $manager = $this->getDoctrine()->getManager();
          $indefinition = $manager->getRepository("OnoMapBundle:Indefinition")->find($numId);

          $form = $this->get('form.factory')->create(IndefinitionType::class, $indefinition);
          if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $manager->persist($indefinition);
            $manager->flush();

            $request->getSession()->getFlashBag()->add('notice', 'Indéfinition bien modifiée.');

            return $this->redirectToRoute("ono_admin_list_indefinitions");
          }

          return $this->render('OnoMapBundle:Admin:edit-indefinition.html.twig', array(
            "form" => $form->createView(),
            "indefinition" => $indefinition
          ));
        }

        public function deleteIndefinitionAction(Request $request)
        {
          $numId = (int) $request->attributes->all()["id"];

            $manager = $this->getDoctrine()->getManager();

            // On récupère l'annonce $numId
            $indefinition = $manager->getRepository('OnoMapBundle:Indefinition')->find($numId);

            if (null === $indefinition) {
              throw new NotFoundHttpException("L'indéfinition d'id ".$numId." n'existe pas.");
            }

            // On crée un formulaire vide, qui ne contiendra que le champ CSRF
            // Cela permet de protéger la suppression d'annonce contre cette faille
            $form = $this->createFormBuilder()->getForm();

            if ($form->handleRequest($request)->isValid()) {
              $manager->remove($indefinition);
              $manager->flush();

              $request->getSession()->getFlashBag()->add('info', "L'indéfinition a bien été supprimée.");

              return $this->redirect($this->generateUrl('ono_admin_list_indefinitions'));
            }

            // Si la requête est en GET, on affiche une page de confirmation avant de supprimer
            return $this->render('OnoMapBundle:Admin:delete.html.twig', array(
              'object' => $indefinition,
              'title' => $indefinition->getTag()->getLibTag(),
              'indefContent' => $indefinition->getContent(),
              'pathDelete' => "ono_admin_delete_indefinition",
              'form'   => $form->createView()
            ));
        }
}
