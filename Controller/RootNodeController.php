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
use UCL\WebKeyPassBundle\Entity\Category;

class RootNodeController extends NodeController
{
    protected function getRootActions ()
    {
        return array (array ('name' => 'Add Category',
                             'route' => 'ucl_wkp_root_add_category',
                             'route_data' => array ()));
    }

    protected function getCommonData ($node_repo, $node)
    {
        $data = array ();
        $data['title'] = 'Root';
        $data['path'] = array ();
        $data['nodes'] = $node_repo->getNodes ();

        return $data;
    }

    public function viewRootAction ()
    {
        $node_repo = $this->getDoctrine ()->getRepository ('UCLWebKeyPassBundle:Node');
        $data = $this->getCommonData ($node_repo, null);

        $data['infos'] = $this->getEmptyNodeInfos ();
        $data['actions'] = $this->getRootActions ();

        return $this->render ('UCLWebKeyPassBundle::node.html.twig', $data);
    }

    public function addCategoryAction (Request $request)
    {
        $category = new Category ();
        $category->setName ('OpenBSD');
        $category->setIcon ('bsd.png');

        $form = $this->createForm (new CategoryForm(), $category);

        $redirect_url = $this->generateUrl ('ucl_wkp_root_view');

        return $this->handleForm ($request,
                                  null,
                                  'Add Category',
                                  $form,
                                  'Category added successfully.',
                                  $redirect_url);
    }
}
