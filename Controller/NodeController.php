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

    protected function getCommonData ($node_repo, $node)
    {
        $data = array ();
        $data['title'] = $node->getName ();
        $data['path'] = $this->getPath ($node);
        $data['nodes'] = $node_repo->getNodes ();

        return $data;
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

        $data = $this->getCommonData ($node_repo, $node);

        $data['infos'] = $this->getNodeInfos ($node);
        $data['actions'] = $this->getActions ($node_id);

        return $this->render ('UCLWebKeyPassBundle::node.html.twig', $data);
    }

    protected function handleForm (Request $request,
                                   $node,
                                   $action_name,
                                   $form,
                                   $success_msg,
                                   $success_redirect_url)
    {
        $node_repo = $this->getDoctrine ()->getRepository ('UCLWebKeyPassBundle:Node');

        $data = $this->getCommonData ($node_repo, $node);
        $data['action'] = $action_name;

        if ($request->isMethod ('POST'))
        {
            $form->bind ($request);

            if ($form->isValid ())
            {
                $flash_bag = $this->get ('session')->getFlashBag ();
                $flash_bag->add ('notice', $success_msg);

                return $this->redirect ($success_redirect_url);
            }
        }

        $data['form'] = $form->createView ();

        return $this->render ('UCLWebKeyPassBundle::form.html.twig', $data);
    }
}
