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

use UCL\WebKeyPassBundle\Form\UserForm;
use UCL\WebKeyPassBundle\Entity\User;

class AddUserAction extends FormAddAction
{
    protected $fullname = 'Add User';
    protected $success_msg = 'User added successfully.';

    protected function getForm ()
    {
        return new UserForm ();
    }

    protected function getFormData ()
    {
        return array ('username' => '',
                      'password1' => '',
                      'password2' => '',
                      'first_name' => '',
                      'last_name' => '',
                      'email' => '',
                      'private_key' => '',
                      'isActive' => false,
                      'isAdmin' => false);
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
        $user->setIsActive ($form_data['isActive']);
        $user->setIsAdmin ($form_data['isAdmin']);

        $db_manager->persist ($user);
    }

    protected function renderTemplate ($data)
    {
        return $this->controller->render ('UCLWebKeyPassBundle::admin_form.html.twig', $data);
    }

    protected function formIsValid ($form)
    {
        $form_data = $form->getData ();

        return $this->checkPasswords ($form,
                                      $form_data['password1'],
                                      $form_data['password2']);
    }
}
