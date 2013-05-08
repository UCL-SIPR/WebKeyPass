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

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormError;

class FormAction extends Action
{
    protected function getForm ()
    {
    }

    protected function getFormData ()
    {
        return $this->node;
    }

    protected function saveData ($db_manager, $form)
    {
    }

    protected function setUserPassword ($user, $password)
    {
        $factory = $this->controller->get ('security.encoder_factory');
        $encoder = $factory->getEncoder ($user);
        $hashed_password = $encoder->encodePassword ($password, $user->getSalt ());
        $user->setPassword ($hashed_password);
    }

    protected function renderTemplate ($data)
    {
        return $this->controller->render ('UCLWebKeyPassBundle::form.html.twig', $data);
    }

    protected function checkPasswords ($form, $password1, $password2)
    {
        $ok = true;

        if (!$this->isStrongPassword ($form, $password1))
        {
            $ok = false;
        }

        if (!$this->checkPasswordsMatch ($form, $password1, $password2))
        {
            $ok = false;
        }

        return $ok;
    }

    protected function checkPasswordsMatch ($form, $password1, $password2)
    {
        if ($password1 !== $password2)
        {
            $msg = 'The passwords don\'t match.';
            $form->addError (new FormError ($msg));
            return false;
        }

        return true;
    }

    protected function isStrongPassword ($form, $password)
    {
        $min_length = 8;
        $ok = true;

        if (strlen ($password) < $min_length)
        {
            $msg = 'The password must have '. $min_length .' characters minimum.';
            $form->addError (new FormError ($msg));
            $ok = false;
        }

        if (!preg_match ("/[0-9]/", $password))
        {
            $msg = 'The password must contain a digit.';
            $form->addError (new FormError ($msg));
            $ok = false;
        }

        if (!preg_match ("/[a-z]/", $password))
        {
            $msg = 'The password must contain a lowercase letter.';
            $form->addError (new FormError ($msg));
            $ok = false;
        }

        if (!preg_match ("/[A-Z]/", $password))
        {
            $msg = 'The password must contain an uppercase letter.';
            $form->addError (new FormError ($msg));
            $ok = false;
        }

        return $ok;
    }

    protected function checkUsername ($form, $username)
    {
        $ok = true;

        $user_repo = $this->controller->getUserRepo ();
        if ($user_repo->userExists ($username))
        {
            $msg = 'The username is already taken.';
            $form->addError (new FormError ($msg));
            $ok = false;
        }

        return $ok;
    }

    protected function checkNode ($form, $is_edit)
    {
        $ok = true;

        if ($is_edit)
        {
            $except_node = $this->node;
        }
        else
        {
            $except_node = null;
        }

        $node_repo = $this->controller->getNodeRepo ();
        if ($node_repo->hostnameExists ($this->node->getHostname (), $except_node))
        {
            $msg = 'The hostname already exists.';
            $form->addError (new FormError ($msg));
            $ok = false;
        }

        if ($node_repo->serialExists ($this->node->getSerialNumber (), $except_node))
        {
            $msg = 'The serial number already exists.';
            $form->addError (new FormError ($msg));
            $ok = false;
        }

        return $ok;
    }

    protected function formIsValid ($form)
    {
        return true;
    }

    public function handleForm ()
    {
        $data = $this->controller->getCommonData ();
        $data['action'] = $this->fullname;

        $form = $this->controller->createForm ($this->getForm (), $this->getFormData ());

        $request = $this->controller->getRequest ();

        if ($request->isMethod ('POST'))
        {
            $form->bind ($request);

            if ($this->formIsValid ($form) &&
                $form->isValid ())
            {
                $db_manager = $this->controller->getDoctrine ()->getManager ();
                $this->saveData ($db_manager, $form);
                $db_manager->flush ();

                $this->addFlashMessage ($this->success_msg);

                return $this->controller->redirect ($this->redirect_url);
            }
        }

        $data['form'] = $form->createView ();
        return $this->renderTemplate ($data);
    }
}
