<?php

namespace Ono\MapBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Ono\MapBundle\Entity\Indefinition;
use Ono\MapBundle\Form\IndefinitionType;


class IndefinitionController extends Controller
{

  public function addAction(Request $request)
  {
    if($this->container->get('security.authorization_checker')->isGranted('ROLE_USER')){
      $user = $this->container->get('security.token_storage')->getToken()->getUser();
    }

    $manager = $this->getDoctrine()->getManager();
    $indefinition = new Indefinition;
    $form = $this->get('form.factory')->create(IndefinitionType::class, $indefinition);

    $tagId = (int) $request->attributes->all()["tag_id"];
    $tagRepo = $manager->getRepository("OnoMapBundle:Tag");
    $tag = $tagRepo->find($tagId);

    if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
      if(isset($user)) {
        $indefinition->setUser($user);
      }
      $indefinition->setTag($tag);
      $manager->persist($indefinition);
      $manager->flush();
      $request->getSession()->getFlashBag()->add('notice', 'Indéfinition bien enregistrée.');
      $articleId = (int) $request->attributes->all()["article_id"];
      $articleRepo = $manager->getRepository("OnoMapBundle:Article");
      $article = $articleRepo->find($articleId);
      return $this->redirectToRoute("ono_map_article_view", array("id" => $article->getId()));
    }

    $themRepo = $manager->getRepository("OnoMapBundle:Theme");
    $themes = $themRepo->findAll();

    return $this->render('OnoMapBundle:Indefinition:add.html.twig', array(
      "form" => $form->createView(),
      "tag" => $tag,
      "themes" => $themes
    ));
  }
}
