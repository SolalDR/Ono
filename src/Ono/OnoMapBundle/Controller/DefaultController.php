<?php

namespace Ono\OnoMapBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('OnoOnoMapBundle:Default:index.html.twig');
    }
}
