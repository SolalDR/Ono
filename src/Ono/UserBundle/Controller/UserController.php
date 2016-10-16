<?php

namespace Ono\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class UserController extends Controller
{
    public function promoteUserAction($id)
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

      }

    }

}
