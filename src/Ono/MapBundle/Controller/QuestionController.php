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

use Ono\MapBundle\Form\QuestionType;

class QuestionController extends Controller
{
    //Action affichant la page d'accueil avec la carte
    public function indexAction(Request $request, $numIds = null, $activeThemes)
    {
      //Initialisation
      $manager = $this->getDoctrine()->getManager();
      $questionRepo = $manager->getRepository("OnoMapBundle:Question");
      $responseRepo = $manager->getRepository("OnoMapBundle:Response");
      $themRepo = $manager->getRepository("OnoMapBundle:Theme");

      //On récupère les objets
      $questions = $questionRepo->findAll();
      $themes = $themRepo->findAll();


      $themesActive = $request->getSession()->get("themes");
      if(count($themesActive)>0 && $activeThemes==="active"){
        $responses = $responseRepo->getResponses($themesActive, true);
      } else {
        $responses = $responseRepo->getResponses(false, true);
      }


      $routeName = $request->get('_route');
      if($routeName === "ono_admin_list_question") {
        return $this->render('OnoMapBundle:Admin:list-question.html.twig', array(
          "questions" => $questions
        ));
      }
        //On retourne le tout
      return $this->render('OnoMapBundle:Question:index.html.twig', array(
        "questions" => $questions,
        "responses" => $responses,
        "themes" => $themes
      ));

    }


    public function viewAction(Request $request)
    {
      $numId = (int) $request->attributes->all()["id"];

      $manager = $this->getDoctrine()->getManager();
      $repoQ = $manager->getRepository("OnoMapBundle:Question");
      $repoR = $manager->getRepository("OnoMapBundle:Response");
      $themRepo = $manager->getRepository("OnoMapBundle:Theme");

      $question = $repoQ->find($numId);
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

    public function editAction(Request $request){
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

    public function addAction(Request $request){
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

    public function deleteAction(Request $request)
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

}
