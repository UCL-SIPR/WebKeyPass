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
use Symfony\Component\Security\Core\SecurityContext;
use UCL\WebKeyPassBundle\Entity\Log;

class SecurityController extends Controller
{
    public function loginAction ()
    {
        $request = $this->getRequest ();
        $session = $request->getSession ();

        /* Get the login error if there is one. */
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR))
        {
            $error = $request->attributes->get (SecurityContext::AUTHENTICATION_ERROR);
        }
        else
        {
            $error = $session->get (SecurityContext::AUTHENTICATION_ERROR);
            $session->remove (SecurityContext::AUTHENTICATION_ERROR);
        }

        $data = array ();
        $data['last_username'] = $session->get(SecurityContext::LAST_USERNAME);
        $data['error'] = $error;
        $data['title'] = 'Login';

        return $this->render ('UCLWebKeyPassBundle::login.html.twig', $data);
    }

    private function getAuthenticatedUser ()
    {
        return $this->get ('security.context')->getToken ()->getUser ();
    }

    public function loginSuccessAction ()
    {
        $log = new Log ();
        $log->setType ('login');

        $user = $this->getAuthenticatedUser ();
        $log->setComment ('User: ' . $user->getUsername ());

        $db_manager = $this->getDoctrine ()->getManager ();
        $db_manager->persist ($log);
        $db_manager->flush ();

        $redirect_url = $this->generateUrl ('ucl_wkp_root_view');
        return $this->redirect ($redirect_url);
    }

    public function logLogoutAction ()
    {
        $log = new Log ();
        $log->setType ('logout');

        $user = $this->getAuthenticatedUser ();
        $log->setComment ('User: ' . $user->getUsername ());

        $db_manager = $this->getDoctrine ()->getManager ();
        $db_manager->persist ($log);
        $db_manager->flush ();

        $redirect_url = $this->generateUrl ('ucl_wkp_logout_real');
        return $this->redirect ($redirect_url);
    }
}
