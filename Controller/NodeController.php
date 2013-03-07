<?php
/*
 * This file is part of WebKeyPass.
 *
 * Copyright © 2013 Université Catholique de Louvain
 *
 * WebKeyPass is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * WebKeyPass is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with WebKeyPass.  If not, see <http://www.gnu.org/licenses/>.
 *
 * Author: Sébastien Wilmet
 */

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
