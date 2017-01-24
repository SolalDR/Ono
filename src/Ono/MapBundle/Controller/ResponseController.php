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

    // View Response
    public function viewAction(Request $request)
    {
      $numId = (int) $request->attributes->all()["id"];

      $manager = $this->getDoctrine()->getManager();
      $repoReponse = $manager->getRepository("OnoMapBundle:Response");
      $themRepo = $manager->getRepository("OnoMapBundle:Theme");
      dump($numId);
      $response = $repoReponse->find($numId);
      if($response === null){
        throw new NotFoundHttpException("La réponse à afficher n'existe pas.");
      }
      $themes = $themRepo->findAll();

      return $this->render("OnoMapBundle:Response:view.html.twig", array(
        "response" => $response,
        "themes" =>$themes
      ));
    }

    public function addAction(Request $request)
    {
      $numId = (int) $request->attributes->all()["id"];
      $manager =$this->getDoctrine()->getManager();
      $repoQuestion = $manager->getRepository("OnoMapBundle:Question");
      $question = $repoQuestion->find($numId);

      //On crée le formulaire d'ajout de réponse, qu'on modifiera dynamiquement après
      if($question){
        $response = new ResponseQ;
        $response->setDtcreation(new \DateTime());
        $response->setQuestion($question);

        //Si un utilisateur est co on adapte le formulaire
        if($this->container->get('security.authorization_checker')->isGranted('ROLE_USER')){
          $user = $this->get('security.token_storage')->getToken()->getUser();
          $response->updateUser($user);
          $form = $this->get('form.factory')->create(ResponseLogType::class, $response);
        } else {
          $form = $this->get('form.factory')->create(ResponseType::class, $response);
        }

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
          $manager = $this->getDoctrine()->getManager();

          $manager->persist($response);
          $question->addResponse($response);

          $manager->flush();
          return $this->redirectToRoute('ono_map_response_view', array('id' => $response->getId()));
        }
      }

      $themRepo = $manager->getRepository("OnoMapBundle:Theme");
      $themes = $themRepo->findAll();

      return $this->render('OnoMapBundle:Response:add.html.twig', array(
        'form' => $form->createView(),
        'question' =>$question,
        "themes" => $themes
      ));
    }

    // public function addAction(Request $request)
    // {
    //   $numId = (int) $request->attributes->all()["id"];
    //   $manager =$this->getDoctrine()->getManager();
    //   $repoQuestion = $manager->getRepository("OnoMapBundle:Question");
    //   $question = $repoQuestion->find($numId);
    //
    //   //On crée le formulaire d'ajout de réponse, qu'on modifiera dynamiquement après
    //   if($question){
    //     $response = new ResponseQ;
    //     $response->setDtcreation(new \DateTime());
    //     $response->setQuestion($question);
    //
    //     if($this->container->get('security.authorization_checker')->isGranted('ROLE_USER')){
    //       $user = $this->get('security.token_storage')->getToken()->getUser();
    //       $response->updateUser($user);
    //       $form = $this->get('form.factory')->create(ResponseLogType::class, $response);
    //     } else {
    //       $form = $this->get('form.factory')->create(ResponseType::class, $response);
    //     }
    //
    //
    //
    //     if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
    //       $manager->persist($response);
    //       $question->addResponse($response);
    //       $manager->flush();
    //       dump("Bonjour");
    //       dump($response);
    //       exit;
    //       return new JsonResponse(array(
    //         "type"=>"notice",
    //         "title" => "Message bien enregistré"
    //       ));
    //     }
    //   }
    //
    //
    //   $question = $manager->getRepository("OnoMapBundle:Question")->find($numId);
    //   //Si il n'y a pas de réponse
    //   if($question === null){
    //     throw new NotFoundHttpException("La question à répondre n'existe pas.");
    //   }
    //
    //   //On instancie la réponse
    //   $response = new ResponseQ;
    //
    //   //On définis la date courante et la question affilié
    //   $response->setDtcreation(new \DateTime());
    //   $response->setQuestion($question);
    //   $response->setDtnaissance(new \DateTime());
    //
    //   //On crée le formulaire
    //   $form = $this->get('form.factory')->create(ResponseType::class, $response);
    //
    //   if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
    //
    //     dump(true);
    //     exit;
    //     dump($response);
    //     dump($question);
    //     exit;
    //
    //     $manager = $this->getDoctrine()->getManager();
    //     $manager->persist($response);
    //     $question->addResponse($response);
    //     $manager->flush();
    //     return $this->redirectToRoute('ono_map_response_view', array('id' => $response->getId()));
    //   }
    //
    //   $themRepo = $manager->getRepository("OnoMapBundle:Theme");
    //   $themes = $themRepo->findAll();
    //
    //   return $this->render('OnoMapBundle:Response:add.html.twig', array(
    //     'form' => $form->createView(),
    //     'question' =>$question,
    //     "themes" => $themes
    //   ));
    //
    //
    //
    //   $manager->persist($response);
    //   $manager->flush();
    //
    //   return $this->redirectToRoute('ono_map_response_view', array(
    //     "id" => $response->getId()
    //   ));
    // }

    public function likeAction(Request $request){
      $numId = (int) $request->attributes->all()["id"];

      $manager = $this->getDoctrine()->getManager();
      $repoResponse = $manager->getRepository("OnoMapBundle:Response");
      $response = $repoResponse->find($numId);

      if($response){
        if($this->container->get('security.authorization_checker')->isGranted('ROLE_USER')){
          $user = $this->get('security.token_storage')->getToken()->getUser();
          if($user){
            $isLiking = $user->isLiking($response);
            if(!$isLiking){
              $user->addResponsesLiked($response);
              $response->incrementLikes();
              $manager->persist($user);
              $manager->persist($response);
              $manager->flush();
            }
            if($request->isXmlHttpRequest()){
              return  new Response($this->getXhrLikesResponse(true, $response->getNbLikes(), $response->getId()));
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

    public function unlikeAction(Request $request){
      $numId = (int) $request->attributes->all()["id"];

      $manager = $this->getDoctrine()->getManager();
      $repoResponse = $manager->getRepository("OnoMapBundle:Response");
      $response = $repoResponse->find($numId);

      if($response){
        if($this->container->get('security.authorization_checker')->isGranted('ROLE_USER')){
          $user = $this->get('security.token_storage')->getToken()->getUser();
          if($user){
            $isLiking = $user->isLiking($response);
            if($isLiking){
              $user->removeResponsesLiked($response);
              $response->decrementLikes();
              $manager->persist($user);
              $manager->persist($response);
              $manager->flush();
            }
            if($request->isXmlHttpRequest()){
              return  new Response($this->getXhrLikesResponse(false, $response->getNbLikes(), $response->getId()));
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

    private function getXhrLikesResponse($isLiking, $nbLikes, $id){
      $render = [];
      $render["nbLike"] = $nbLikes;
      if($isLiking){
        $render["liking"] = true;
        $render["nextRoute"] = $this->generateUrl(
              'ono_map_response_unlike',
              array('id' => $id)
          );
        return json_encode($render);
      }
      $render["liking"] = false;
      $render["nextRoute"] =  $this->generateUrl(
            'ono_map_response_like',
            array('id' => $id)
        );
      return json_encode($render);
    }
}
