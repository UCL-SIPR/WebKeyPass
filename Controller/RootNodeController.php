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
use UCL\WebKeyPassBundle\Form\CategoryForm;
use UCL\WebKeyPassBundle\Entity\Category;

class RootNodeController extends NodeController
{
    protected function getRootActions ()
    {
        return array (array ('name' => 'Add Category',
                             'route' => 'ucl_wkp_root_add_category',
                             'route_data' => array ()));
    }

    private function getCommonData ()
    {
        $data = array ();
        $data['title'] = 'Root';
        $data['path'] = array ();

        $node_repo = $this->getDoctrine ()->getRepository ('UCLWebKeyPassBundle:Node');
        $data['nodes'] = $node_repo->getNodes ();

        return $data;
    }

    public function viewRootAction ()
    {
        $data = $this->getCommonData ();

        $data['infos'] = $this->getEmptyNodeInfos ();
        $data['actions'] = $this->getRootActions ();

        return $this->render ('UCLWebKeyPassBundle::node.html.twig', $data);
    }

    public function addCategoryAction ()
    {
        $data = $this->getCommonData ();

        $data['action'] = 'Add Category';

        $category = new Category ();
        $category->setName ('OpenBSD');
        $category->setIcon ('bsd.png');

        $form = $this->createForm (new CategoryForm(), $category);
        $data['form'] = $form->createView ();

        return $this->render ('UCLWebKeyPassBundle::form.html.twig', $data);
    }
}
