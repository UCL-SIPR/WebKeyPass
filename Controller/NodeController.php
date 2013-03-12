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
    protected function getEmptyNodeInfos ()
    {
        return array (array ('title' => 'No information',
                             'content' => ''));
    }

    protected function getNodeInfos ($node)
    {
        $infos = array ();

        $infos[] = array ('title' => 'Hostname',
                          'content' => $node->getHostname ());

        foreach ($node->getAuthentications () as $auth)
        {
            $text = $auth->getLogin () . ': ' . $auth->getPassword ();

            $infos[] = array ('title' => 'Login/Password',
                              'content' => $text);
        }

        $infos[] = array ('title' => 'Comment',
                          'content' => $node->getComment ());

        return $infos;
    }

    protected function getActions ($node_id)
    {
        return array ();
    }

    protected function checkType ($node)
    {
        return true;
    }

    private function getPathNodeInfos ($node)
    {
        return array ('class' => $node->getTypeStr (),
                      'id' => $node->getId (),
                      'name' => $node->getName ());
    }

    private function getPath ($node)
    {
        $path = array ($this->getPathNodeInfos ($node));

        for ($parent = $node->getParent (); $parent != null; $parent = $parent->getParent ())
        {
            $path[] = $this->getPathNodeInfos ($parent);
        }

        return array_reverse ($path);
    }

    public function viewAction ($node_id)
    {
        $node_repo = $this->getDoctrine ()->getRepository ('UCLWebKeyPassBundle:Node');
        $node = $node_repo->find ($node_id);

        if (!$node)
        {
            throw $this->createNotFoundException ('Node id '.$node_id.' not found');
        }

        if (!$this->checkType ($node))
        {
            throw $this->createNotFoundException ('Wrong node type');
        }

        $title = $node->getName ();
        $infos = $this->getNodeInfos ($node);
        $actions = $this->getActions ($node_id);
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
