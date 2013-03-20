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
use UCL\WebKeyPassBundle\Form\ServerForm;
use UCL\WebKeyPassBundle\Form\VMForm;
use UCL\WebKeyPassBundle\Entity\Node;

class ServerNodeController extends NodeController
{
    protected function getActions ($node_id)
    {
        $route_data = array ('node_id' => $node_id);

        return array (array ('name' => 'Edit',
                             'route' => 'ucl_wkp_server_edit',
                             'route_data' => $route_data),

                      array ('name' => 'Add VM',
                             'route' => 'ucl_wkp_server_add_vm',
                             'route_data' => $route_data),

                      array ('name' => 'Move',
                             'route' => 'ucl_wkp_server_move',
                             'route_data' => $route_data),

                      array ('name' => 'Remove',
                             'route' => 'ucl_wkp_server_remove',
                             'route_data' => $route_data));
    }

    protected function checkType ($node)
    {
        return $node->getTypeStr () == 'server';
    }

    public function editAction ($node_id)
    {
        $this->node = $this->getNodeFromId ($node_id);
        $form = $this->createForm (new ServerForm (), $this->node);

        $data = $this->getCommonData ();
        $data['action'] = 'Edit Server';
        $data['submit_route'] = 'ucl_wkp_server_edit';
        $data['submit_route_data'] = array ('node_id' => $node_id);

        $redirect_url = $this->generateUrl ('ucl_wkp_server_view', array ('node_id' => $node_id));

        $action_type = 'edit';

        return $this->handleForm ($data,
                                  $form,
                                  $action_type,
                                  'Server edited successfully.',
                                  $redirect_url);
    }

    public function addVMAction ($node_id)
    {
        $this->node = $this->getNodeFromId ($node_id);
        $vm = new Node ();
        $vm->setParent ($this->node);
        $form = $this->createForm (new VMForm (), $vm);

        $data = $this->getCommonData ();
        $data['action'] = 'Add Virtual Machine';
        $data['submit_route'] = 'ucl_wkp_server_add_vm';
        $data['submit_route_data'] = array ('node_id' => $node_id);

        $redirect_url = $this->generateUrl ('ucl_wkp_server_view', array ('node_id' => $node_id));

        $action_type = 'add';

        return $this->handleForm ($data,
                                  $form,
                                  $action_type,
                                  'Virtual Machine added successfully.',
                                  $redirect_url);
    }

    public function removeAction ($node_id)
    {
        $node = $this->getNodeFromId ($node_id);
        $parent_id = $node->getParent ()->getId ();

        $action = new RemoveAction ($this, $node);
        $action->setRedirectRoute ('ucl_wkp_category_view', array ('node_id' => $parent_id));

        $success_msg = 'Server removed successfully.';
        return $action->perform ($success_msg);
    }
}
