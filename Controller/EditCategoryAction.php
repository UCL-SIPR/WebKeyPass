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

use UCL\WebKeyPassBundle\Form\CategoryForm;

class EditCategoryAction extends FormAction
{
    protected $fullname = 'Edit Category';
    protected $success_msg = 'Category edited successfully.';

    protected function getForm ()
    {
        $form = new CategoryForm ();
        $form->setNodeRepository ($this->controller->getNodeRepo ());

        return $form;
    }

    protected function getFormData ()
    {
        return array ('list_name' => $this->node->getName (),
                      'other_name' => '',
                      'icon' => $this->node->getIcon (),
                      'comment' => $this->node->getComment (),
                      '_node' => $this->node);
    }

    protected function saveData ($db_manager, $form)
    {
        $data = $form->getData ();
        $node = $data['_node'];

        if ($data['other_name'] != '')
        {
            $node->setName ($data['other_name']);
        }
        else
        {
            $node->setName ($data['list_name']);
        }

        $node->setIcon ($data['icon']);
        $node->setComment ($data['comment']);
    }
}
