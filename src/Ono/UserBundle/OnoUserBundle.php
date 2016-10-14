<?php

namespace Ono\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class OnoUserBundle extends Bundle
{
  public function getParent(){
    return 'FOSUserBundle';
  }
}
