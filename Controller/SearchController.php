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
        $data['open_nodes'] = array ();
        $data['node_id'] = -1;

        $icons = new Icons ();
        $data['node_types'] = $icons->getIcons ();

        $data['found_data'] = array ();

        $settings = new Settings ();
        $data['session_expiration_timeout'] = $settings->getSessionExpirationTimeout ();

        $form = $this->getSearchForm ();
        $request = $this->getRequest ();

        if ($request->isMethod ('POST'))
        {
            $form->bind ($request);

            if ($form->isValid ())
            {
                $search_data = $form->getData ();
                $node_repo = $this->getNodeRepo ();

                /* Search results by names */
                $found_nodes = $node_repo->searchNames ($search_data['search_text']);

                $found_data = array ();
                foreach ($found_nodes as $found_node)
                {
                    $found_data[] = array ('path' => $this->getPath ($found_node),
                                           'serial_number' => "");
                }

                /* Search results by serial numbers */
                $found_nodes = $node_repo->searchSerialNumbers ($search_data['search_text']);

                foreach ($found_nodes as $found_node)
                {
                    $found_data[] = array ('path' => $this->getPath ($found_node),
                                           'serial_number' => $found_node->getSerialNumber ());
                }

                $data['found_data'] = $found_data;
            }
        }

        $data['search_form'] = $form->createView ();

        return $this->render ('UCLWebKeyPassBundle::search.html.twig', $data);
    }
}
