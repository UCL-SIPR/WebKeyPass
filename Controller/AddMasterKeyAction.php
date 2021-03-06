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

use UCL\WebKeyPassBundle\Form\MasterKeyForm;

class AddMasterKeyAction extends FormAction
{
    protected $fullname = 'Add Master Key';
    protected $success_msg = 'Master key encrypted successfully.';

    protected function getForm ()
    {
        return new MasterKeyForm ();
    }

    protected function getFormData ()
    {
        return array ('master_key' => '');
    }

    protected function saveData ($db_manager, $form)
    {
        $form_data = $form->getData ();
        $user = $this->controller->getAuthenticatedUser ();
        $master_key = new MasterKey ($this->controller);
        $master_key->encryptMasterKey ($form_data['master_key'], $user);
    }

    protected function renderTemplate ($data)
    {
        return $this->controller->render ('UCLWebKeyPassBundle::admin_form.html.twig', $data);
    }
}
