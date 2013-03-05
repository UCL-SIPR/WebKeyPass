<?php

namespace UCL\WebKeyPassBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class MainController extends Controller
{
    public function viewAction ()
    {
        $actions = array (array ('name' => 'Add Category'));

        return $this->render ('UCLWebKeyPassBundle::main.html.twig',
                              array ('actions' => $actions));
    }
}
