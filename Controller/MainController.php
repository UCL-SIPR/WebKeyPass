<?php

namespace UCL\WebKeyPassBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class MainController extends Controller
{
    public function viewAction()
    {
        return $this->render('UCLWebKeyPassBundle::node.html.twig');
    }
}
