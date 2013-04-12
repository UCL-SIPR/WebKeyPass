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

use UCL\WebKeyPassBundle\Form\CreateUserForm;
use UCL\WebKeyPassBundle\Entity\User;

class CreateUserAction extends FormAddAction
{
    protected $fullname = 'Create Account';
    protected $success_msg = 'Account created successfully. Ask an admin to activate it.';

    protected function renderTemplate ($data)
    {
        return $this->controller->render ('UCLWebKeyPassBundle::create_account_form.html.twig', $data);
    }

    protected function getForm ()
    {
        return new CreateUserForm ();
    }

    protected function getFormData ()
    {
        return array ('username' => '',
                      'password1' => '',
                      'password2' => '',
                      'first_name' => '',
                      'last_name' => '',
                      'email' => '',
                      'private_key' => '');
    }

    protected function saveData ($db_manager, $form)
    {
        $form_data = $form->getData ();
        $user = new User ();

        $user->setUsername ($form_data['username']);
        $user->setPassword ($form_data['password1']);
        $user->setFirstName ($form_data['first_name']);
        $user->setLastName ($form_data['last_name']);
        $user->setEmail ($form_data['email']);
        $user->setPrivateKey ($form_data['private_key']);
        $user->setIsActive (false);
        $user->setIsAdmin (false);

        $db_manager->persist ($user);
    }

    protected function formIsValid ($form)
    {
        $ok = true;
        $form_data = $form->getData ();

        if (!$this->checkPasswords ($form,
                                    $form_data['password1'],
                                    $form_data['password2']))
        {
            $ok = false;
        }

        if (!$this->checkUsername ($form, $form_data['username']))
        {
            $ok = false;
        }

        return $ok;
    }
}
