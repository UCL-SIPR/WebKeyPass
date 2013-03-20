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

class Action
{
    protected $controller;
    protected $node;
    protected $redirect_url;

    protected $fullname = '';
    protected $success_msg = '';

    public function __construct ($controller, $node)
    {
        $this->controller = $controller;
        $this->node = $node;
    }

    /* Set redirect route when the action has been performed successfully. */
    public function setRedirectRoute ($route, $route_data = array ())
    {
        $this->redirect_url = $this->controller->generateUrl ($route, $route_data);
    }

    protected function addFlashMessage ($msg)
    {
        $flash_bag = $this->controller->get ('session')->getFlashBag ();
        $flash_bag->add ('notice', $msg);
    }
}
