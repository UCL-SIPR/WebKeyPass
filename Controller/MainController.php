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

class MainController extends Controller
{
    public function getNodeRepo ()
    {
        return $this->getDoctrine ()->getRepository ('UCLWebKeyPassBundle:Node');
    }

    protected function getPathNodeInfos ($node)
    {
        return array ('class' => $node->getTypeStr (),
                      'id' => $node->getId (),
                      'name' => $node->getName ());
    }

    protected function getPath ($node)
    {
        $path = array ($this->getPathNodeInfos ($node));

        for ($parent = $node->getParent (); $parent != null; $parent = $parent->getParent ())
        {
            $path[] = $this->getPathNodeInfos ($parent);
        }

        return array_reverse ($path);
    }

    protected function getSearchForm ()
    {
        $search_data = array ('search_text' => '');

        return $this->createFormBuilder ($search_data)
            ->add ('search_text', 'search')
            ->getForm ();
    }
}
