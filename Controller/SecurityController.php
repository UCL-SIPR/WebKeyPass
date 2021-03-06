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
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use UCL\WebKeyPassBundle\Entity\Log;

class SecurityController extends MainController
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

        $settings = new Settings ();
        $data['can_create_account'] = $settings->getCanCreateAccount ();

        $cookies = $request->cookies;

        if ($cookies->has ('automatic_logout'))
        {
            $msg = $cookies->get ('automatic_logout');
            setcookie ('automatic_logout', '', time () - 3600);

            $flash_bag = $session->getFlashBag ();
            $flash_bag->add ('notice', $msg);
        }

        return $this->render ('UCLWebKeyPassBundle::login.html.twig', $data);
    }

    private function errorLogin ($msg)
    {
        $flash_bag = $this->get ('session')->getFlashBag ();
        $flash_bag->add ('error', $msg);

        $redirect_url = $this->generateUrl ('ucl_wkp_login');
        return $this->redirect ($redirect_url);
    }

    public function loginWithShibbolethAction ()
    {
        $shib = new Shibboleth ($this);

        if (!$shib->isAuthenticated ())
        {
            return $this->errorLogin ('You are not authenticated with Shibboleth.');
        }

        $user_repo = $this->getUserRepo ();
        $user = $user_repo->getUser ($shib->getUsername ());

        if ($user == null)
        {
            return $this->errorLogin ('The username returned by Shibboleth doesn\'t exist in WebKeyPass.');
        }

        if (!$user->getWithShibboleth ())
        {
            return $this->errorLogin ('Your account was created without Shibboleth activated.');
        }

        $token = new UsernamePasswordToken ($user,
                                            '',
                                            'secured_area',
                                            $user->getRoles ());

        $request = $this->getRequest ();
        $session = $request->getSession ();
        $session->set ('_security_secured_area', serialize ($token));

        $redirect_url = $this->generateUrl ('ucl_wkp_login_success');
        return $this->redirect ($redirect_url);
    }

    private function setLogIP ($log)
    {
        $server = $this->getRequest ()->server;

        $ip = $server->get ('REMOTE_ADDR');
        $log->setIp ($ip);

        if ($server->has ('REMOTE_HOST'))
        {
            $log->setHost ($server->get ('REMOTE_HOST'));
        }
        else
        {
            $log->setHost (gethostbyaddr ($ip));
        }
    }

    public function loginSuccessAction ()
    {
        $log = new Log ();
        $log->setType ('login');

        $user = $this->getAuthenticatedUser ();
        $log->setComment ('User: ' . $user->getUsername ());

        $this->setLogIP ($log);

        $db_manager = $this->getDoctrine ()->getManager ();
        $db_manager->persist ($log);
        $db_manager->flush ();

        $redirect_url = $this->generateUrl ('ucl_wkp_login_private_key');
        return $this->redirect ($redirect_url);
    }

    public function logLogoutAction ()
    {
        $log = new Log ();
        $log->setType ('logout');

        $user = $this->getAuthenticatedUser ();
        $log->setComment ('User: ' . $user->getUsername ());

        $this->setLogIP ($log);

        $db_manager = $this->getDoctrine ()->getManager ();
        $db_manager->persist ($log);
        $db_manager->flush ();

        $redirect_url = $this->generateUrl ('ucl_wkp_logout_real');
        return $this->redirect ($redirect_url);
    }

    public function automaticLogoutAction ()
    {
        $msg = "The session has expired.";
        setcookie ('automatic_logout', $msg);

        $redirect_url = $this->generateUrl ('ucl_wkp_logout');
        return $this->redirect ($redirect_url);
    }

    private function checkPrivateKey ($shib)
    {
        $user = $this->getAuthenticatedUser();
        $good_hash = $user->getPrivateKeyHash ();

        $hash = PrivateKey::getHash ($shib->getPrivateKey (),
                                     $user->getPrivateKeySalt ());

        return $hash === $good_hash;
    }

    public function loginPrivateKeyAction ()
    {
        $shib = new Shibboleth ($this);
        $user = $this->getAuthenticatedUser ();

        if ($shib->isAuthenticated () &&
            $user->getWithShibboleth ())
        {
            if (!$this->checkPrivateKey ($shib))
            {
                $msg = 'The Shibboleth private key is not correct.';
                $flash_bag = $this->get ('session')->getFlashBag ();
                $flash_bag->add ('error', $msg);

                $redirect_url = $this->generateUrl ('ucl_wkp_login');
                return $this->redirect ($redirect_url);
            }

            $session = $this->get ('session');
            $session->set ('private_key', $shib->getPrivateKey ());

            $redirect_url = $this->generateUrl ('ucl_wkp_root_view');
            return $this->redirect ($redirect_url);
        }
        else
        {
            $action = new SetPrivateKeyAction ($this, null);
            $action->setRedirectRoute ('ucl_wkp_root_view');

            return $action->handleForm ();
        }
    }

    public function getCommonData ()
    {
        $data = array ();
        $data['title'] = 'Login';

        $settings = new Settings ();
        $data['session_expiration_timeout'] = $settings->getSessionExpirationTimeout ();

        return $data;
    }
}
