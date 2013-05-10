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

    private function encryptMasterKey ($user)
    {
        $master_key = new MasterKey ($this->controller);

        $admin_user = $this->controller->getAuthenticatedUser ();
        $decrypted_master_key = $master_key->decryptMasterKey ($admin_user);

        $master_key->encryptMasterKey ($decrypted_master_key, $user);

        $this->addFlashMessage ("Master key encrypted for the user.");
    }

    private function sendMail ($user)
    {
        $to = $user->getEmail ();

        $subject = "UCL WebKeyPass: your account has been activated.";

        $admin = $this->controller->getAuthenticatedUser ();
        $url = $this->controller->generateUrl ('ucl_wkp_root_view',
                                               array (),
                                               true);

        $msg = "Hello,\n\n"
             . $admin->getFirstName () ." ". $admin->getLastName () ." has activated your account on UCL WebKeyPass.\n"
             . "Go to the following URL:\n\n"
             . $url . "\n\n"
             . "Best regards,\n"
             . "The UCL WebKeyPass application.";

        mail ($to, $subject, $msg);

        $this->addFlashMessage ("An e-mail has been sent to the user to explain that the account is now activated.");
    }

    protected function saveData ($db_manager, $form)
    {
        $user = $this->node;

        if ($user->getIsActive () &&
            $user->getEncryptedMasterKey () == "" &&
            $user->getPrivateKey () != "")
        {
            $this->encryptMasterKey ($user);
            $this->sendMail ($user);
        }
    }
}
