<?php

namespace Ono\MapBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Finder\Exception\AccessDeniedException;

use Ono\MapBundle\Entity\Theme;
use Ono\MapBundle\Form\ThemeType;


class ThemeController extends Controller
{

  public function updateSessionAction(Request $request) {
    if ($request->request->get('xhr')) {

      //Traitement du filtre des thèmes
      $filters = (array) json_decode($request->request->get("json"));
      $request->getSession()->set("themes", $filters["themes"]);

      $route = $this->getRefererRoute($request);
      switch($route) {
        case "ono_map_homepage":
          return $this->redirectToRoute("ono_map_update_homepage");
          break;
        default:
          return new Response("Cette route ne match pas !", 500);
      }
      return new Response("Pas de route !", 500);
    }
    return new Response("Error : Request not XHR argument");
  }

  public function addAction(Request $request){
    $manager = $this->getDoctrine()->getManager();
    $theme = new Theme;
    $form = $this->get('form.factory')->create(ThemeType::class, $theme);

    if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
      $manager->persist($theme);
      $manager->flush();
      $request->getSession()->getFlashBag()->add('notice', 'Thème bien enregistrée.');
      return $this->redirectToRoute("ono_admin_list_theme");
    }
    return $this->render('OnoMapBundle:Admin:add-theme.html.twig', array(
      "form" => $form->createView()
    ));
  }

  public function listAction(){
    $manager = $this->getDoctrine()->getManager();
    $themes = $manager->getRepository("OnoMapBundle:Theme")->findAll();
    return $this->render('OnoMapBundle:Admin:list-theme.html.twig', array(
      "themes" => $themes
    ));
  }

  public function editAction(Request $request){
    $numId = (int) $request->attributes->all()["id"];

    $manager = $this->getDoctrine()->getManager();
    $theme = $manager->getRepository("OnoMapBundle:Theme")->find($numId);
    $form = $this->get('form.factory')->create(ThemeType::class, $theme);
    if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
      $manager->persist($theme);
      $manager->flush();
      $request->getSession()->getFlashBag()->add('notice', 'Thème bien modifié.');
      return $this->redirectToRoute("ono_admin_list_theme");
    }
    return $this->render('OnoMapBundle:Admin:edit-theme.html.twig', array(
      "form" => $form->createView(),
      "theme" => $theme
    ));
  }

  public function deleteAction(Request $request)
  {
      $numId = (int) $request->attributes->all()["id"];
      $manager = $this->getDoctrine()->getManager();
      // On récupère l'annonce $numId
      $theme = $manager->getRepository('OnoMapBundle:Theme')->find($numId);
      if (null === $theme) {
        throw new NotFoundHttpException("Le thème d'id ".$numId." n'existe pas.");
      }
      // On crée un formulaire vide, qui ne contiendra que le champ CSRF
      // Cela permet de protéger la suppression d'annonce contre cette faille
      $form = $this->createFormBuilder()->getForm();
      if ($form->handleRequest($request)->isValid()) {
        $manager->remove($theme);
        $manager->flush();
        $request->getSession()->getFlashBag()->add('info', "Le thème a bien été supprimée.");
        return $this->redirect($this->generateUrl('ono_admin_list_theme'));
      }

      // Si la requête est en GET, on affiche une page de confirmation avant de supprimer
      return $this->render('OnoMapBundle:Admin:delete.html.twig', array(
        'object' => $theme,
        'title' => $theme->getLibTheme(),
        'pathDelete' => "ono_admin_delete_theme",
        'form'   => $form->createView()
      ));
  }

  private function getRefererRoute(Request $request)
  {

      //look for the referer route
      $referer = $request->headers->get('referer');
      $lastPath = substr($referer, strpos($referer, $request->getBaseUrl()));
      $lastPath = str_replace($request->getBaseUrl(), '', $lastPath);

      $matcher = $this->get('router')->getMatcher();
      $parameters = $matcher->match($lastPath);
      $route = $parameters['_route'];
      return $route;
  }
}
