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

class NodeController extends MainController
{
    protected $node;

    public function getAuthRepo ()
    {
        return $this->getDoctrine ()->getRepository ('UCLWebKeyPassBundle:Authentication');
    }

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

        $infos[] = array ('title' => 'Comment',
                          'content' => $node->getComment ());

        return $infos;
    }

    private function getRemoveLoginUrl ($auth)
    {
        $request = $this->container->get ('request');
        $route = $request->get ('_route');
        $route_data = $request->get ('_route_params');
        $url = $this->generateUrl ($route, $route_data);
        $url .= '/remove_login_' . $auth->getId ();

        return $url;
    }

    private function getEditLoginUrl ($auth)
    {
        $request = $this->container->get ('request');
        $route = $request->get ('_route');
        $route_data = $request->get ('_route_params');
        $url = $this->generateUrl ($route, $route_data);
        $url .= '/edit_login_' . $auth->getId ();

        return $url;
    }

    protected function getAuthentications ($node)
    {
        $master_key = new MasterKey ($this);
        $user = $this->getAuthenticatedUser ();

        $auths = array ();

        foreach ($node->getAuthentications () as $auth)
        {
            $iv = $auth->getMcryptIv ();
            $decrypted_password = $master_key->decryptPassword ($auth->getPassword (),
                                                                $user,
                                                                $iv);

            $auths[] = array ('login' => $auth->getLogin (),
                              'password' => $decrypted_password,
                              'remove_url' => $this->getRemoveLoginUrl ($auth),
                              'edit_url' => $this->getEditLoginUrl ($auth));
        }

        return $auths;
    }

    protected function getActions ($node_id)
    {
        return array ();
    }

    protected function checkType ($node)
    {
        return true;
    }

    protected function getNodeTypes ()
    {
        $icons = new Icons ();
        return $icons->getIcons ();
    }

    private function getOpenNodes ()
    {
        $open_nodes = array ();

        if (!$this->node->isLeaf ())
        {
            $open_nodes[] = $this->node->getId ();
        }

        for ($parent = $this->node->getParent (); $parent != null; $parent = $parent->getParent ())
        {
            $open_nodes[] = $parent->getId ();
        }

        return $open_nodes;
    }

    public function getCommonData ()
    {
        $data = array ();
        $data['title'] = $this->node->getName ();
        $data['path'] = $this->getPath ($this->node);
        $data['open_nodes'] = $this->getOpenNodes ();
        $data['node_id'] = $this->node->getId ();

        $node_repo = $this->getNodeRepo ();
        $data['nodes'] = $node_repo->getNodes ();

        $data['search_form'] = $this->getSearchForm ()->createView ();

        $data['node_types'] = $this->getNodeTypes ();

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
        $data['authentications'] = $this->getAuthentications ($this->node);
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
