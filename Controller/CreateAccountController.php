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
use UCL\WebKeyPassBundle\Entity\User;

class CreateAccountController extends MainController
{
    public function getCommonData ()
    {
        $data = array ();
        $data['title'] = 'Create Account';

        $shib = new Shibboleth ($this);
        $data['shibboleth'] = $shib->isAuthenticated ();

        $settings = new Settings ();
        $data['session_expiration_timeout'] = $settings->getSessionExpirationTimeout ();
        return $data;
    }

    public function createAccountAction ()
    {
        $action = new CreateUserAction ($this, null);
        $action->setRedirectRoute ('ucl_wkp_login');

        return $action->handleForm ();
    }
}
