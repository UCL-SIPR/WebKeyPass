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

class EncryptMasterKeyAction extends Action
{
    public function perform ()
    {
        $master_key = new MasterKey ($this->controller);

        $admin_user = $this->controller->getAuthenticatedUser ();
        $decrypted_master_key = $master_key->decryptMasterKey ($admin_user);

        $user = $this->node;
        $master_key->encryptMasterKey ($decrypted_master_key, $user);

        $db_manager = $this->controller->getDoctrine ()->getManager ();
        $db_manager->flush ();

        $this->addFlashMessage ("Master key encrypted successfully.");

        return $this->controller->redirect ($this->redirect_url);
    }
}
