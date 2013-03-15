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
use UCL\WebKeyPassBundle\Form\CategoryForm;

class CategoryNodeController extends NodeController
{
    protected function getNodeInfos ($node)
    {
        return $this->getEmptyNodeInfos ();
    }

    protected function getActions ($node_id)
    {
        $route_data = array ('node_id' => $node_id);

        return array (array ('name' => 'Edit',
                             'route' => 'ucl_wkp_category_edit',
                             'route_data' => $route_data),

                      array ('name' => 'Add Server',
                             'route' => 'ucl_wkp_category_add_server',
                             'route_data' => $route_data),

                      array ('name' => 'Add Sub-category',
                             'route' => 'ucl_wkp_category_add_subcategory',
                             'route_data' => $route_data),

                      array ('name' => 'Remove',
                             'route' => 'ucl_wkp_category_remove',
                             'route_data' => $route_data));
    }

    protected function checkType ($node)
    {
        return $node->getTypeStr () == 'category';
    }

    public function editAction ($node_id)
    {
        $node = $this->getNodeFromId ($node_id);
        $form = $this->createForm (new CategoryForm (), $node);

        $data = $this->getCommonData ($node);
        $data['action'] = 'Edit Category';
        $data['submit_route'] = 'ucl_wkp_category_edit';
        $data['submit_route_data'] = array ('node_id' => $node_id);

        $redirect_url = $this->generateUrl ('ucl_wkp_category_view', array ('node_id' => $node_id));

        $action_type = 'edit';

        return $this->handleForm ($data,
                                  $form,
                                  $action_type,
                                  'Category edited successfully.',
                                  $redirect_url);
    }
}
