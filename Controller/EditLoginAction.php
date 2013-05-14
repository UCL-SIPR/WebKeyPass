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

use UCL\WebKeyPassBundle\Form\AuthenticationForm;

class EditLoginAction extends FormAction
{
    protected $fullname = 'Edit login/password';
    protected $success_msg = 'Login/password edited successfully.';

    protected function getForm ()
    {
        $form = new AuthenticationForm ();
        $form->setAuthRepo ($this->controller->getAuthRepo ());
        return $form;
    }

    protected function getFormData ()
    {
        $auth = $this->node;

        return array ('list_login' => $auth->getLogin (),
                      'other_login' => '',
                      'password1' => '',
                      'password2' => '',
                      '_auth' => $auth);
    }

    protected function formIsValid ($form)
    {
        $data = $form->getData ();

        return $this->checkPasswordsMatch ($form,
                                           $data['password1'],
                                           $data['password2']);
    }

    protected function saveData ($db_manager, $form)
    {
        $form_data = $form->getData ();
        $user = $this->controller->getAuthenticatedUser ();

        $master_key = new MasterKey ($this->controller);

        $iv = null;
        $encrypted_password = $master_key->encryptPassword ($form_data['password1'],
                                                            $user,
                                                            $iv);

        $auth = $form_data['_auth'];

        if ($form_data['other_login'] != '')
        {
            $auth->setLogin ($form_data['other_login']);
        }
        else
        {
            $auth->setLogin ($form_data['list_login']);
        }

        $auth->setPassword ($encrypted_password);
        $auth->setMcryptIv ($iv);
        $db_manager->persist ($auth);
    }
}
