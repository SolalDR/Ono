<?php

namespace Ono\OnoMapBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class MapController extends Controller
{
    public function indexAction()
    {
        return $this->render('OnoOnoMapBundle:Default:index.html.twig');
    }
}
