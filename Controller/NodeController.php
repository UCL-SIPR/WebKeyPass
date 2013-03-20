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

    public function viewAction ($node_id)
    {
        $this->node = $this->getNodeFromId ($node_id);

        $data = $this->getCommonData ();
        $data['infos'] = $this->getNodeInfos ($this->node);
        $data['actions'] = $this->getActions ($node_id);

        return $this->render ('UCLWebKeyPassBundle::node.html.twig', $data);
    }

    protected function handleForm ($data,
                                   $form,
                                   $action_type,
                                   $success_msg,
                                   $success_redirect_url)
    {
        $request = $this->getRequest ();

        if ($request->isMethod ('POST'))
        {
            $form->bind ($request);

            if ($form->isValid ())
            {
                $db_manager = $this->getDoctrine ()->getManager ();

                if ($action_type == 'add')
                {
                    $node = $form->getData ();
                    $db_manager->persist ($node);
                }

                $db_manager->flush ();

                $flash_bag = $this->get ('session')->getFlashBag ();
                $flash_bag->add ('notice', $success_msg);

                return $this->redirect ($success_redirect_url);
            }
        }

        $data['form'] = $form->createView ();

        return $this->render ('UCLWebKeyPassBundle::form.html.twig', $data);
    }

    protected function handleRemove ($node_to_remove,
                                     $success_msg,
                                     $success_redirect_url)
    {
        $db_manager = $this->getDoctrine ()->getManager ();
        $db_manager->remove ($node_to_remove);
        $db_manager->flush ();

        $flash_bag = $this->get ('session')->getFlashBag ();
        $flash_bag->add ('notice', $success_msg);

        return $this->redirect ($success_redirect_url);
    }
}
