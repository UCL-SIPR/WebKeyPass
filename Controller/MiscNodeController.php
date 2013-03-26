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
use UCL\WebKeyPassBundle\Entity\Node;
use UCL\WebKeyPassBundle\Entity\Authentication;

class MiscNodeController extends NodeController
{
    protected function getActions ($node_id)
    {
        $route_data = array ('node_id' => $node_id);

        return array (array ('name' => 'Edit',
                             'route' => 'ucl_wkp_misc_edit',
                             'route_data' => $route_data),

                      array ('name' => 'Add Login',
                             'route' => 'ucl_wkp_misc_add_login',
                             'route_data' => $route_data),

                      array ('name' => 'Add Misc',
                             'route' => 'ucl_wkp_misc_add_misc',
                             'route_data' => $route_data),

                      array ('name' => 'Move',
                             'route' => 'ucl_wkp_misc_move',
                             'route_data' => $route_data),

                      array ('name' => 'Remove',
                             'route' => 'ucl_wkp_misc_remove',
                             'route_data' => $route_data));
    }

    protected function getNodeInfos ($node)
    {
        return array (array ('title' => 'Comment',
                             'content' => $node->getComment ()));
    }

    protected function checkType ($node)
    {
        return $node->getTypeStr () == 'misc';
    }

    public function editAction ($node_id)
    {
        $this->node = $this->getNodeFromId ($node_id);
        $action = new EditMiscAction ($this, $this->node);

        $action->setRedirectRoute ('ucl_wkp_misc_view',
                                   array ('node_id' => $node_id));

        return $action->handleForm ();
    }

    public function removeAction ($node_id)
    {
        $node = $this->getNodeFromId ($node_id);
        $parent_id = $node->getParent ()->getId ();

        $parent_type = $node->getParent ()->getTypeStr ();

        $action = new RemoveAction ($this, $node);
        $action->setRedirectRoute ('ucl_wkp_'.$parent_type.'_view', array ('node_id' => $parent_id));

        $success_msg = 'Misc item removed successfully.';
        return $action->perform ($success_msg);
    }

    public function moveAction ($node_id)
    {
        $this->node = $this->getNodeFromId ($node_id);

        $action = new MoveMiscAction ($this, $this->node);

        $action->setRedirectRoute ('ucl_wkp_misc_view',
                                   array ('node_id' => $node_id));

        return $action->handleForm ();
    }

    public function addMiscAction ($node_id)
    {
        $this->node = $this->getNodeFromId ($node_id);

        $new_node = new Node ();
        $new_node->setParent ($this->node);

        $action = new AddMiscAction ($this, $new_node);

        $action->setRedirectRoute ('ucl_wkp_misc_view',
                                   array ('node_id' => $node_id));

        return $action->handleForm ();
    }

    public function addLoginAction ($node_id)
    {
        $this->node = $this->getNodeFromId ($node_id);

        $new_auth = new Authentication ();
        $new_auth->setNode ($this->node);

        $action = new AddLoginAction ($this, $new_auth);

        $action->setRedirectRoute ('ucl_wkp_misc_view',
                                   array ('node_id' => $node_id));

        return $action->handleForm ();
    }
}
