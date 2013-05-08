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

class AddLoginAction extends FormAddAction
{
    protected $fullname = 'Add login/password';
    protected $success_msg = 'Login/password added successfully.';

    protected function getForm ()
    {
        return new AuthenticationForm ();
    }

    protected function getFormData ()
    {
        $auth = $this->node;

        return array ('login' => $auth->getLogin (),
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

        $encrypted_password = $master_key->encryptPassword ($form_data['password1'], $user);

        $auth = $form_data['_auth'];
        $auth->setLogin ($form_data['login']);
        $auth->setPassword ($encrypted_password);
        $db_manager->persist ($auth);
    }
}
