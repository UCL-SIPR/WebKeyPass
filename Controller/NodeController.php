<?php

namespace UCL\WebKeyPassBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class NodeController extends Controller
{
    public function viewAction ($node)
    {
        $nodes = array ('Linux', 'Server 2');

        $actions = array (array ('name' => 'Edit'),
                          array ('name' => 'Add VM'),
                          array ('name' => 'Move'),
                          array ('name' => 'Remove'));

        $infos = array (array ('title' => 'Hostname',
                               'content' => 'server2.sipr.ucl.ac.be'),
                        array ('title' => 'User/password',
                               'content' => 'root: Btrn78duXl32'),
                        array ('title' => 'User/password',
                               'content' => 'mysql: 23lXud87nrtB'),
                        array ('title' => 'Comment',
                               'content' => 'Virtualisation : avec Xen'));

        return $this->render ('UCLWebKeyPassBundle::node.html.twig',
                              array ('node' => $node,
                                     'nodes' => $nodes,
                                     'actions' => $actions,
                                     'infos' => $infos));
    }
}
