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

class RootNodeController extends NodeController
{
    protected function getRootActions ()
    {
        return array (array ('name' => 'Add Category',
                             'route' => 'ucl_wkp_root_add_category',
                             'route_data' => array ()));
    }

    public function viewRootAction ()
    {
        $title = 'Root';
        $infos = $this->getEmptyNodeInfos ();
        $actions = $this->getRootActions ();
        $path = array ();

        $node_repo = $this->getDoctrine ()->getRepository ('UCLWebKeyPassBundle:Node');
        $nodes = $node_repo->getNodes ();

        return $this->render ('UCLWebKeyPassBundle::node.html.twig',
                              array ('title' => $title,
                                     'path' => $path,
                                     'actions' => $actions,
                                     'infos' => $infos,
                                     'nodes' => $nodes));
    }
}
