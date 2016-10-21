<?php

namespace Ono\UserBundle\Controller;

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

use Ono\UserBundle\Entity\User;
use Ono\UserBundle\Form\RegistrationAdminType;


//JSON
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;


class UserController extends Controller
{
    public function promoteAction($id)
    {
      if($this->container->get('security.authorization_checker')->isGranted('ROLE_ADMIN')){

        $userManager = $this->get('fos_user.user_manager');
        $user = $userManager->findUserBy(array("id"=>$id));

        if(!$user->hasRole('ROLE_ADMIN')){
          $user->addRole('ROLE_ADMIN');
          $userManager->updateUser($user);
        }

        return $this->render('OnoMapBundle:Admin:list-user.html.twig', array(
            // ...
        ));
      } else {
        return new AccessDeniedException();
      }

    }

    ////////////////////////////////////
    //        Users
    ///////////////////////////////////
    public function addAction(Request $request){
      $user = new User();


      $form = $this->get('form.factory')->create(RegistrationAdminType::class, $user);
      // $form = $this->createForm(new RegistrationType(), $user);
      // $form->submit($request);

      if($request->isMethod('POST') && $form->isValid()) {
          $userManager = $this->get('fos_user.user_manager');
          $exists = $userManager->findUserBy(array('email' => $user->getEmail()));
          if ($exists instanceof User) {
              throw new HttpException(409, 'Email already taken');
          }

          $request->getSession()->getFlashBag()->add('notice', 'Utilisateur bien enregistrée.');
          $userManager->updateUser($user);

          return redirectToRoute("ono_admin_list_user");
       }

      return $this->render('OnoMapBundle:Admin:add-user.html.twig', array(
        "form"=> $form->createView()
      ));
    }

    public function listAction(Request $request){
        $userManager = $this->get('fos_user.user_manager');
        $users = $userManager->findUsers();

        return $this->render('OnoMapBundle:Admin:list-user.html.twig', array(
          "users"=>$users
        ));
    }

    public function deleteAction($id){

    }

    public function editAction(Request $request, $id){
      // Pour récupérer le service UserManager du bundle
      $userManager = $this->get('fos_user.user_manager');


      $user = $userManager->findUserBy(array("id"=>$id));


      if($user){
        $form = $this->get('form.factory')->create(RegistrationAdminType::class, $user);
        // $form = $this->createForm(new RegistrationType(), $user);
        // $form->submit($request);

        dump($form);
        exit;
        if($request->isMethod('POST') && $form->isValid()) {

            // $exists = $userManager->findUserBy(array('email' => $user->getEmail()));
            // if ($exists instanceof User) {
            //     throw new HttpException(409, 'Email already taken');
            // }
            $request->getSession()->getFlashBag()->add('notice', 'Utilisateur bien enregistrée.');
            $userManager->updateUser($user);

            return redirectToRoute("ono_admin_list_user");
         }

        return $this->render('OnoMapBundle:Admin:add-user.html.twig', array(
          "form"=> $form->createView()
        ));
      }
      return $this->redirectToRoute("ono_admin_list_user");
    }
}
