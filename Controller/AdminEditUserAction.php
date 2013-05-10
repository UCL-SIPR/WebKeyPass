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

use UCL\WebKeyPassBundle\Form\AdminEditUserForm;

class AdminEditUserAction extends FormAction
{
    protected $fullname = 'Edit User';
    protected $success_msg = 'User edited successfully.';

    protected function getForm ()
    {
        return new AdminEditUserForm ();
    }

    protected function renderTemplate ($data)
    {
        return $this->controller->render ('UCLWebKeyPassBundle::admin_form.html.twig', $data);
    }

    protected function saveData ($db_manager, $form)
    {
        $user = $this->node;

        if ($user->getIsActive () &&
            $user->getEncryptedMasterKey () == "" &&
            $user->getPrivateKey () != "")
        {
            $master_key = new MasterKey ($this->controller);

            $admin_user = $this->controller->getAuthenticatedUser ();
            $decrypted_master_key = $master_key->decryptMasterKey ($admin_user);

            $master_key->encryptMasterKey ($decrypted_master_key, $user);

            $this->addFlashMessage ("Master key encrypted for the user.");
        }
    }
}
