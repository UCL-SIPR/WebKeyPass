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

class Shibboleth
{
    private $request;

    public function __construct ($controller)
    {
        $this->request = $controller->getRequest ();
    }

    /* Is authenticated with Shibboleth? */
    public function isAuthenticated ()
    {
        return false;
    }

    public function getUsername ()
    {
        return $this->request->server->get ('uid');
    }

    public function getFirstName ()
    {
        return $this->request->server->get ('givenName');
    }

    public function getLastName ()
    {
        return $this->request->server->get ('sn');
    }

    public function getEmail ()
    {
        return $this->request->server->get ('mail');
    }

    public function getPrivateKey ()
    {
        return "foobar";
    }
}
