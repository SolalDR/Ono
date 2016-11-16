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
use Ono\MapBundle\Entity\LikeResponse;
use Ono\MapBundle\Form\ResponseType;
use Ono\MapBundle\Form\ResponseLogType;


use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;



class ResponseController extends Controller
{

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
    public function likeResponseAction($id, Request $request){
      $em = $this->getDoctrine()->getManager();
      $repoResponse = $em->getRepository("OnoMapBundle:Response");
      $response = $repoResponse->find($id);

      if($response){
        if($this->container->get('security.authorization_checker')->isGranted('ROLE_USER')){
          $user = $this->get('security.token_storage')->getToken()->getUser();
          if($user){
            $isLiking = $user->isLikingResponse($response->getId());
            if(!$isLiking){
              $user->addResponsesLiked($response);
              $response->incrementLikes();
              $em->persist($user);
              $em->persist($response);
              $em->flush();
            }
            $request->getSession()->getFlashBag()->add('notice', 'La réponse est déja aimé.');
            return $this->redirectToRoute('ono_map_response_view', array('id' => $response->getId()));
          }
        }
        return $this->redirectToRoute('ono_map_response_view', array('id' => $response->getId()));
      } else {
        $request->getSession()->getFlashBag()->add('notice', 'La réponse n\'existe pas.');
      }
      //L'utilisateur n'est pas authentifié
      return $this->redirectToRoute('ono_map_homepage');
    }

    public function unlikeResponseAction($id, Request $request){
      $em = $this->getDoctrine()->getManager();
      $repoResponse = $em->getRepository("OnoMapBundle:Response");
      $response = $repoResponse->find($id);

      if($response){
        if($this->container->get('security.authorization_checker')->isGranted('ROLE_USER')){
          $user = $this->get('security.token_storage')->getToken()->getUser();
          if($user){
            $isLiking = $user->isLikingResponse($response->getId());
            if($isLiking){
              $user->removeResponsesLiked($response);
              $response->decrementLikes();
              $em->persist($user);
              $em->persist($response);
              $em->flush();
            }
            $request->getSession()->getFlashBag()->add('notice', 'La réponse n\'est pas aimé.');
            return $this->redirectToRoute('ono_map_response_view', array('id' => $response->getId()));
          }
          //La response n'existe pas
        }
        return $this->redirectToRoute('ono_map_response_view', array('id' => $response->getId()));
      }
      //L'utilisateur n'est pas authentifié
      return $this->redirectToRoute('ono_map_homepage');
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

        if($this->container->get('security.authorization_checker')->isGranted('ROLE_USER')){
          $user = $this->get('security.token_storage')->getToken()->getUser();
          $response->updateUser($user);
          $form = $this->get('form.factory')->create(ResponseLogType::class, $response);
        } else {
          $form = $this->get('form.factory')->create(ResponseType::class, $response);
        }

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
          //On persist la réponse
          $em->persist($response);

          //On rajoute la réponse à la question
          $question->addResponse($response);

          //On enregistre
          $em->flush();

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

}
