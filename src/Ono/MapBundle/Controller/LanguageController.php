<?php

namespace Ono\MapBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Finder\Exception\AccessDeniedException;

use Ono\MapBundle\Entity\Language;
use Ono\MapBundle\Form\LanguageType;

class LanguageController extends Controller
{

  public function addAction(Request $request){
    $manager = $this->getDoctrine()->getManager();
    $language = new Language;

    $form = $this->get('form.factory')->create(LanguageType::class, $language);
    if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
      $manager->persist($language);
      $manager->flush();

      $request->getSession()->getFlashBag()->add('notice', 'Langue bien enregistrée.');

      return $this->redirectToRoute("ono_admin_list_language");
    }
    return $this->render('OnoMapBundle:Admin:add-language.html.twig', array(
      "form" => $form->createView()
    ));
  }

  public function indexAction(){
    $manager = $this->getDoctrine()->getManager();
    $languages = $manager->getRepository("OnoMapBundle:Language")->findAll();

    return $this->render('OnoMapBundle:Admin:list-language.html.twig', array(
      "languages" => $languages
    ));
  }
  public function editAction(Request $request, $id){
      $manager = $this->getDoctrine()->getManager();
      $language = $manager->getRepository("OnoMapBundle:Language")->find($id);

      $form = $this->get('form.factory')->create(LanguageType::class, $language);
      if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
        $manager->persist($language);
        $manager->flush();

        $request->getSession()->getFlashBag()->add('notice', 'Langue bien modifiée.');

        return $this->redirectToRoute("ono_admin_list_language");
      }

      return $this->render('OnoMapBundle:Admin:edit-language.html.twig', array(
        "form" => $form->createView(),
        "language" => $language
      ));
    }

    public function deleteAction(Request $request, $id)
    {
        $manager = $this->getDoctrine()->getManager();

        // On récupère l'annonce $id
        $language = $manager->getRepository('OnoMapBundle:Language')->find($id);

        if (null === $language) {
          throw new NotFoundHttpException("La langue d'id ".$id." n'existe pas.");
        }

        // On crée un formulaire vide, qui ne contiendra que le champ CSRF
        // Cela permet de protéger la suppression d'annonce contre cette faille
        $form = $this->createFormBuilder()->getForm();

        if ($form->handleRequest($request)->isValid()) {
          $manager->remove($language);
          $manager->flush();

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
