<?php

namespace Ono\MapBundle\Twig;

use Ono\MapBundle\Entity\Response;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Ono\UserBundle\Entity\User;

class AppExtension extends \Twig_Extension
{
// , SecurityContext $context
    public function __construct(ContainerInterface $container)
    {
      $this->container = $container;
    }
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('price', array($this, 'priceFilter')),
            new \Twig_SimpleFilter('isliking', array($this, 'isLiking'))
        );
    }

    public function priceFilter($number, $decimals = 0, $decPoint = '.', $thousandsSep = ',')
    {
        $price = number_format($number, $decimals, $decPoint, $thousandsSep);
        $price = '$'.$price;

        return $price;
    }

    public function isLiking($object)
    {
      $user = $this->container->get('security.token_storage')->getToken()->getUser();
      if($user instanceof User){
        if($object instanceof Response){
          dump($user);
          if($user->isLikingResponse($object)){
            return true;
          }
          return false;
        } elseif($object instanceof Article){
          if($user->isLikingArticle($object)){
            return true;
          }
        }
      }
      return false;
    }

    public function getName()
    {
        return 'app_extension';
    }
}
