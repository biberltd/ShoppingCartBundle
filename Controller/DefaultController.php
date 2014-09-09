<?php

namespace BiberLtd\Bundle\ShoppingCartBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('BiberLtdShoppingCartBundle:Default:index.html.twig', array('name' => $name));
    }
}
