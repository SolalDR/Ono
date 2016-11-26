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
use Ono\MapBundle\Entity\Language;

use Ono\MapBundle\Form\CountryType;
use Ono\MapBundle\Form\ResponseType;
use Ono\MapBundle\Form\ResponseAdminType;
use Ono\MapBundle\Form\QuestionType;
use Ono\MapBundle\Form\ThemeType;
use Ono\MapBundle\Form\LanguageType;



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


      public function editQuestionAction(Request $request, $id){
          $em = $this->getDoctrine()->getManager();
          $question = $em->getRepository("OnoMapBundle:Question")->find($id);

          $form = $this->get('form.factory')->create(QuestionType::class, $question);
          if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $em->persist($question);
            $em->flush();

            $request->getSession()->getFlashBag()->add('notice', 'Question bien modifié.');

            return $this->redirectToRoute("ono_admin_list_question");
          }

          return $this->render('OnoMapBundle:Admin:edit-question.html.twig', array(
            "form" => $form->createView(),
            "question" => $question
          ));
        }

        public function deleteQuestionAction(Request $request, $id)
        {
            $em = $this->getDoctrine()->getManager();

            // On récupère l'annonce $id
            $question = $em->getRepository('OnoMapBundle:Question')->find($id);

            if (null === $question) {
              throw new NotFoundHttpException("La question d'id ".$id." n'existe pas.");
            }

            // On crée un formulaire vide, qui ne contiendra que le champ CSRF
            // Cela permet de protéger la suppression d'annonce contre cette faille
            $form = $this->createFormBuilder()->getForm();

            if ($form->handleRequest($request)->isValid()) {
              $em->remove($question);
              $em->flush();

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

    public function editResponseAction(Request $request, $id){
        $em = $this->getDoctrine()->getManager();
        $response = $em->getRepository("OnoMapBundle:Response")->find($id);

        $form = $this->get('form.factory')->create(ResponseType::class, $response);
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
          $em->persist($response);
          $em->flush();

          $request->getSession()->getFlashBag()->add('notice', 'Réponse bien modifié.');

          return $this->redirectToRoute("ono_admin_list_response");
        }

        return $this->render('OnoMapBundle:Admin:edit-response.html.twig', array(
          "form" => $form->createView(),
          "response" => $response
        ));
      }

      public function deleteResponseAction(Request $request, $id)
      {
          $em = $this->getDoctrine()->getManager();

          // On récupère l'annonce $id
          $response = $em->getRepository('OnoMapBundle:Response')->find($id);

          if (null === $response) {
            throw new NotFoundHttpException("La réponse d'id ".$id." n'existe pas.");
          }

          // On crée un formulaire vide, qui ne contiendra que le champ CSRF
          // Cela permet de protéger la suppression d'annonce contre cette faille
          $form = $this->createFormBuilder()->getForm();

          if ($form->handleRequest($request)->isValid()) {
            $em->remove($response);
            $em->flush();

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
    public function editCountryAction(Request $request, $id){
        $em = $this->getDoctrine()->getManager();
        $country = $em->getRepository("OnoMapBundle:Country")->find($id);

        $form = $this->get('form.factory')->create(CountryType::class, $country);
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
          $em->persist($country);
          $em->flush();

          $request->getSession()->getFlashBag()->add('notice', 'Pays bien modifié.');

          return $this->redirectToRoute("ono_admin_list_country");
        }

        return $this->render('OnoMapBundle:Admin:edit-country.html.twig', array(
          "form" => $form->createView(),
          "country" => $country
        ));
      }

      public function deleteCountryAction(Request $request, $id)
      {
          $em = $this->getDoctrine()->getManager();

          // On récupère l'annonce $id
          $country = $em->getRepository('OnoMapBundle:Country')->find($id);

          if (null === $country) {
            throw new NotFoundHttpException("Le pays d'id ".$id." n'existe pas.");
          }

          // On crée un formulaire vide, qui ne contiendra que le champ CSRF
          // Cela permet de protéger la suppression d'annonce contre cette faille
          $form = $this->createFormBuilder()->getForm();

          if ($form->handleRequest($request)->isValid()) {
            $em->remove($country);
            $em->flush();

            $request->getSession()->getFlashBag()->add('info', "Le pays a bien été supprimée.");

            return $this->redirect($this->generateUrl('ono_admin_list_country'));
          }

          // Si la requête est en GET, on affiche une page de confirmation avant de supprimer
          return $this->render('OnoMapBundle:Admin:delete.html.twig', array(
            'object' => $country,
            'title' => $country->getLibCountry(),
            'pathDelete' => "ono_admin_delete_country",
            'form'   => $form->createView()
          ));
      }

      ////////////////////////////////////
      //        Language
      ///////////////////////////////////
      public function addLanguageAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $language = new Language;

        $form = $this->get('form.factory')->create(LanguageType::class, $language);
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
          $em->persist($language);
          $em->flush();

          $request->getSession()->getFlashBag()->add('notice', 'Langue bien enregistrée.');

          return $this->redirectToRoute("ono_admin_list_language");
        }
        return $this->render('OnoMapBundle:Admin:add-language.html.twig', array(
          "form" => $form->createView()
        ));
      }

      public function listLanguageAction(){
        $em = $this->getDoctrine()->getManager();
        $languages = $em->getRepository("OnoMapBundle:Language")->findAll();

        return $this->render('OnoMapBundle:Admin:list-language.html.twig', array(
          "languages" => $languages
        ));
      }
      public function editLanguageAction(Request $request, $id){
          $em = $this->getDoctrine()->getManager();
          $language = $em->getRepository("OnoMapBundle:Language")->find($id);

          $form = $this->get('form.factory')->create(LanguageType::class, $language);
          if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $em->persist($language);
            $em->flush();

            $request->getSession()->getFlashBag()->add('notice', 'Langue bien modifiée.');

            return $this->redirectToRoute("ono_admin_list_language");
          }

          return $this->render('OnoMapBundle:Admin:edit-language.html.twig', array(
            "form" => $form->createView(),
            "language" => $language
          ));
        }

        public function deleteLanguageAction(Request $request, $id)
        {
            $em = $this->getDoctrine()->getManager();

            // On récupère l'annonce $id
            $language = $em->getRepository('OnoMapBundle:Language')->find($id);

            if (null === $language) {
              throw new NotFoundHttpException("La langue d'id ".$id." n'existe pas.");
            }

            // On crée un formulaire vide, qui ne contiendra que le champ CSRF
            // Cela permet de protéger la suppression d'annonce contre cette faille
            $form = $this->createFormBuilder()->getForm();

            if ($form->handleRequest($request)->isValid()) {
              $em->remove($language);
              $em->flush();

              $request->getSession()->getFlashBag()->add('info', "La langue a bien été supprimée.");

              return $this->redirect($this->generateUrl('ono_admin_list_language'));
            }

            // Si la requête est en GET, on affiche une page de confirmation avant de supprimer
            return $this->render('OnoMapBundle:Admin:delete.html.twig', array(
              'object' => $language,
              'title' => $language->getLibLanguageFr(),
              'pathDelete' => "ono_admin_delete_language",
              'form'   => $form->createView()
            ));
        }


}
