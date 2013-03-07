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
    private function getNodeInfos ($node)
    {
        $infos = array ();

        $infos[] = array ('title' => 'Hostname',
                          'content' => $node->getHostname ());

        $infos[] = array ('title' => 'Comment',
                          'content' => $node->getComment ());

        return $infos;
    }

    private function getActions ($node_type)
    {
        $category = 0;
        $server = 1;
        $vm = 2;

        switch ($node_type)
        {
            case $category:
                return array (array ('name' => 'Add Server'),
                              array ('name' => 'Add Sub-category'),
                              array ('name' => 'Remove'));

            case $server:
                return array (array ('name' => 'Edit'),
                              array ('name' => 'Add VM'),
                              array ('name' => 'Move'),
                              array ('name' => 'Remove'));

            case $vm:
                return array (array ('name' => 'Edit'),
                              array ('name' => 'Move'),
                              array ('name' => 'Remove'));

            default:
                return array ();
        }
    }

    private function getPath ($node)
    {
        $path = array ($node->getName ());

        for ($parent = $node->getParent (); $parent != null; $parent = $parent->getParent ())
        {
            $path[] = $parent->getName ();
        }

        return array_reverse ($path);
    }

    public function viewAction ($node_id)
    {
        $node_repo = $this->getDoctrine ()->getRepository ('UCLWebKeyPassBundle:Node');
        $node = $node_repo->find ($node_id);

        if (!$node)
        {
            throw $this->createNotFoundException ('No node found for id '.$id);
        }

        $title = $node->getName ();
        $infos = $this->getNodeInfos ($node);
        $actions = $this->getActions ($node->getType ());
        $path = $this->getPath ($node);
        $nodes = $node_repo->getNodes ();

        return $this->render ('UCLWebKeyPassBundle::node.html.twig',
                              array ('title' => $title,
                                     'path' => $path,
                                     'actions' => $actions,
                                     'infos' => $infos,
                                     'nodes' => $nodes));
    }
}
