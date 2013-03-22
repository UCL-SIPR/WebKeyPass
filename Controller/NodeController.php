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
use Symfony\Component\HttpFoundation\Request;

class NodeController extends Controller
{
    protected $node;

    protected function getNodeRepo ()
    {
        return $this->getDoctrine ()->getRepository ('UCLWebKeyPassBundle:Node');
    }

    protected function getAuthRepo ()
    {
        return $this->getDoctrine ()->getRepository ('UCLWebKeyPassBundle:Authentication');
    }

    protected function getEmptyNodeInfos ()
    {
        return array (array ('title' => 'No information',
                             'content' => ''));
    }

    protected function getRemoveLoginLink ($auth)
    {
        $request = $this->container->get ('request');
        $route = $request->get ('_route');
        $route_data = $request->get ('_route_params');
        $url = $this->generateUrl ($route, $route_data);
        $url .= '/remove_login_' . $auth->getId ();

        return "<a href=\"$url\" onclick=\"return confirm ('Are you sure you want to remove the login/password?')\">Remove</a>\n";
    }

    protected function getNodeInfos ($node)
    {
        $infos = array ();

        $infos[] = array ('title' => 'Hostname',
                          'content' => $node->getHostname ());

        foreach ($node->getAuthentications () as $auth)
        {
            $text = $auth->getLogin () . ': ' . $auth->getPassword ();

            $text .= "<br />\n";
            $text .= $this->getRemoveLoginLink ($auth);

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

    public function getCommonData ()
    {
        $data = array ();
        $data['title'] = $this->node->getName ();
        $data['path'] = $this->getPath ($this->node);

        $node_repo = $this->getNodeRepo ();
        $data['nodes'] = $node_repo->getNodes ();

        return $data;
    }

    protected function getNodeFromId ($node_id)
    {
        $node_repo = $this->getNodeRepo ();
        $node = $node_repo->find ($node_id);

        if (!$node)
        {
            throw $this->createNotFoundException ('Node id '.$node_id.' not found');
        }

        if (!$this->checkType ($node))
        {
            throw $this->createNotFoundException ('Wrong node type');
        }

        return $node;
    }

    protected function getAuthFromId ($auth_id)
    {
        $auth_repo = $this->getAuthRepo ();
        $auth = $auth_repo->find ($auth_id);

        if (!$auth)
        {
            throw $this->createNotFoundException ('Login/password with id '.$auth_id.' not found');
        }

        return $auth;
    }

    public function viewAction ($node_id)
    {
        $this->node = $this->getNodeFromId ($node_id);

        $data = $this->getCommonData ();
        $data['infos'] = $this->getNodeInfos ($this->node);
        $data['actions'] = $this->getActions ($node_id);

        return $this->render ('UCLWebKeyPassBundle::node.html.twig', $data);
    }

    public function removeLoginAction ($node_id, $auth_id)
    {
        $node = $this->getNodeFromId ($node_id);
        $node_type = $node->getTypeStr ();

        $auth = $this->getAuthFromId ($auth_id);

        $action = new RemoveAction ($this, $auth);
        $action->setRedirectRoute ('ucl_wkp_'.$node_type.'_view', array ('node_id' => $node_id));

        $success_msg = 'Login/password removed successfully.';
        return $action->perform ($success_msg);
    }
}
