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

use Symfony\Component\Form\FormError;
use UCL\WebKeyPassBundle\Form\EditUserForm;
use UCL\WebKeyPassBundle\Entity\User;

class EditUserAction extends FormAction
{
    protected $fullname = 'Edit Account';
    protected $success_msg = 'Account edited successfully.';

    protected function getForm ()
    {
        return new EditUserForm ();
    }

    protected function getFormData ()
    {
        $user = $this->node;

        return array ('old-password' => '',
                      'password1' => '',
                      'password2' => '',
                      'email' => $user->getEmail ());
    }

    protected function saveData ($db_manager, $form)
    {
        $form_data = $form->getData ();
        $user = $this->node;

        if ($form_data['old-password'] != '')
        {
            $this->setUserPassword ($user, $form_data['password1']);
        }

        $user->setEmail ($form_data['email']);
    }

    private function checkOldPassword ($form, $old_password)
    {
        $ok = true;

        $user = $this->node;
        $test_user = new User ();
        $this->setUserPassword ($test_user, $old_password);

        if ($user->getPassword () !== $test_user->getPassword ())
        {
            $msg = 'The old password is not correct.';
            $form->addError (new FormError ($msg));
            $ok = false;
        }

        return $ok;
    }

    protected function formIsValid ($form)
    {
        $ok = true;
        $form_data = $form->getData ();

        if ($form_data['old-password'] != '')
        {
            if (!$this->checkOldPassword ($form, $form_data['old-password']))
            {
                $ok = false;
            }

            if (!$this->checkPasswords ($form,
                                        $form_data['password1'],
                                        $form_data['password2']))
            {
                $ok = false;
            }
        }

        return $ok;
    }
}
