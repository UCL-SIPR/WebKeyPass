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

class SearchController extends MainController
{
    public function searchAction ()
    {
        $data = array ();
        $data['title'] = 'Search Results';

        $node_repo = $this->getNodeRepo ();
        $data['nodes'] = $node_repo->getNodes ();

        $data['found_paths'] = array ();

        $form = $this->getSearchForm ();
        $request = $this->getRequest ();

        if ($request->isMethod ('POST'))
        {
            $form->bind ($request);

            if ($form->isValid ())
            {
                $search_data = $form->getData ();

                $node_repo = $this->getNodeRepo ();
                $found_nodes = $node_repo->searchNames ($search_data['search_text']);

                $found_paths = array ();
                foreach ($found_nodes as $found_node)
                {
                    $found_paths[] = $this->getPath ($found_node);
                }

                $data['found_paths'] = $found_paths;
            }
        }

        $data['search_form'] = $form->createView ();

        return $this->render ('UCLWebKeyPassBundle::search.html.twig', $data);
    }
}
