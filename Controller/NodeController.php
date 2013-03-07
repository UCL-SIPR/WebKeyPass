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
        $path = array ('Linux', 'Server 2');

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

        $server1_VMs = array (array ('name' => 'Virtual Machine A',
                                     'class' => 'vm',
                                     'subnodes' => null),
                              array ('name' => 'Virtual Machine B',
                                     'class' => 'vm',
                                     'subnodes' => null));

        $linux_nodes = array (array ('name' => 'Server 1',
                                     'class' => 'server',
                                     'subnodes' => $server1_VMs),
                              array ('name' => 'Server 2',
                                     'class' => 'server',
                                     'subnodes' => null));

        $nodes = array (array ('name' => 'Linux',
                               'class' => 'category linux',
                               'subnodes' => $linux_nodes),
                        array ('name' => 'Windows',
                               'class' => 'category windows',
                               'subnodes' => null),
                        array ('name' => 'Solaris',
                               'class' => 'category solaris',
                               'subnodes' => null));

        return $this->render ('UCLWebKeyPassBundle::node.html.twig',
                              array ('title' => $node,
                                     'path' => $path,
                                     'actions' => $actions,
                                     'infos' => $infos,
                                     'nodes' => $nodes));
    }
}
