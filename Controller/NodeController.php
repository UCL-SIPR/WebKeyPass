<?php

namespace UCL\WebKeyPassBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class NodeController extends Controller
{
    public function viewAction()
    {
        return $this->render('UCLWebKeyPassBundle::base.html.twig');
    }
}
