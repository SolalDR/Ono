<?php

namespace Ono\MapBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Finder\Exception\AccessDeniedException;

//Response already use;
use Ono\MapBundle\Entity\Country;
use Ono\MapBundle\Form\CountryType;


class CountryController extends Controller
{
  ////////////////////////////////////
  //        Country
  ///////////////////////////////////
  public function addAction(Request $request){
    $manager = $this->getDoctrine()->getManager();
    $country = new Country;

    $form = $this->get('form.factory')->create(CountryType::class, $country);
    if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
      $manager->persist($country);
      $manager->flush();

      $request->getSession()->getFlashBag()->add('notice', 'Pays bien enregistrée.');

      return $this->redirectToRoute("ono_admin_list_country");
    }
    return $this->render('OnoMapBundle:Admin:add-country.html.twig', array(
      "form" => $form->createView()
    ));
  }

  public function indexAction(){
    $manager = $this->getDoctrine()->getManager();
    $countries = $manager->getRepository("OnoMapBundle:Country")->findAll();

    return $this->render('OnoMapBundle:Admin:list-country.html.twig', array(
      "countries" => $countries
    ));
  }
  public function editAction(Request $request, $id){
      $manager = $this->getDoctrine()->getManager();
      $country = $manager->getRepository("OnoMapBundle:Country")->find($id);

      $form = $this->get('form.factory')->create(CountryType::class, $country);
      if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
        $manager->persist($country);
        $manager->flush();

        $request->getSession()->getFlashBag()->add('notice', 'Pays bien modifié.');

        return $this->redirectToRoute("ono_admin_list_country");
      }

      return $this->render('OnoMapBundle:Admin:edit-country.html.twig', array(
        "form" => $form->createView(),
        "country" => $country
      ));
    }

    public function deleteAction(Request $request, $id)
    {
        $manager = $this->getDoctrine()->getManager();

        // On récupère l'annonce $id
        $country = $manager->getRepository('OnoMapBundle:Country')->find($id);

        if (null === $country) {
          throw new NotFoundHttpException("Le pays d'id ".$id." n'existe pas.");
        }

        // On crée un formulaire vide, qui ne contiendra que le champ CSRF
        // Cela permet de protéger la suppression d'annonce contre cette faille
        $form = $this->createFormBuilder()->getForm();

        if ($form->handleRequest($request)->isValid()) {
          $manager->remove($country);
          $manager->flush();

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
}
