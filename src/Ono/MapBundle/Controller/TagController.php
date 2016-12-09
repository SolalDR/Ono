<?php

namespace Ono\MapBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Ono\MapBundle\Entity\Tag;
use Ono\MapBundle\Form\TagType;


class TagController extends Controller
{

  public function addAction(Request $request)
  {
    $manager = $this->getDoctrine()->getManager();
    $tag = new Tag;
    $form = $this->get('form.factory')->create(TagType::class, $tag);
    if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
      $manager->persist($tag);
      $manager->flush();
      $request->getSession()->getFlashBag()->add('notice', 'Tag bien enregistrée.');
      return $this->redirectToRoute("ono_admin_list_tags");
    }
    return $this->render('OnoMapBundle:Admin:add-tag.html.twig', array(
      "form" => $form->createView()
    ));
  }

  public function editAction(Request $request)
  {
    $numId = (int) $request->attributes->all()["id"];
      $manager = $this->getDoctrine()->getManager();
      $tag = $manager->getRepository("OnoMapBundle:Tag")->find($numId);

      $form = $this->get('form.factory')->create(TagType::class, $tag);
      if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
        $manager->persist($tag);
        $manager->flush();
        $request->getSession()->getFlashBag()->add('notice', 'Tag bien modifié.');
        return $this->redirectToRoute("ono_admin_list_country");
      }
      return $this->render('OnoMapBundle:Admin:edit-tag.html.twig', array(
        "form" => $form->createView(),
        "tag" => $tag
      ));
  }

  public function deleteAction(Request $request)
  {
    $numId = (int) $request->attributes->all()["id"];
      $manager = $this->getDoctrine()->getManager();

      // On récupère l'annonce $numId
      $tag = $manager->getRepository('OnoMapBundle:Tag')->find($numId);
      if (null === $tag) {
        throw new NotFoundHttpException("Le pays d'id ".$numId." n'existe pas.");
      }

      // On crée un formulaire vide, qui ne contiendra que le champ CSRF
      // Cela permet de protéger la suppression d'annonce contre cette faille
      $form = $this->createFormBuilder()->getForm();

      if ($form->handleRequest($request)->isValid()) {
        $manager->remove($tag);
        $manager->flush();

        $request->getSession()->getFlashBag()->add('info', "Le tag a bien été supprimée.");

        return $this->redirect($this->generateUrl('ono_admin_list_tags'));
      }

      // Si la requête est en GET, on affiche une page de confirmation avant de supprimer
      return $this->render('OnoMapBundle:Admin:delete.html.twig', array(
        'object' => $tag,
        'title' => $tag->getLibTag(),
        'pathDelete' => "ono_admin_delete_tag",
        'form'   => $form->createView()
      ));
  }

  public function indexAction()
  {
    $manager = $this->getDoctrine()->getManager();
    $tags = $manager->getRepository("OnoMapBundle:Tag")->findAll();

    return $this->render('OnoMapBundle:Admin:list-tags.html.twig', array(
      "tags" => $tags
    ));
  }
}
