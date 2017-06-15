<?php

namespace Ono\UXInteractiveBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('OnoUXInteractiveBundle:Default:index.html.twig');
    }
}
