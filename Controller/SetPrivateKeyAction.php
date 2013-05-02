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

use UCL\WebKeyPassBundle\Form\PrivateKeyForm;

class SetPrivateKeyAction extends FormAction
{
    protected $fullname = 'Set Private Key';
    protected $success_msg = 'Private key set.';

    protected function getForm ()
    {
        return new PrivateKeyForm ();
    }

    protected function getFormData ()
    {
        return array ('private_key' => '');
    }

    protected function saveData ($db_manager, $form)
    {
        $form_data = $form->getData ();

        $session = $this->controller->get ('session');
        $session->set ('private_key', $form_data['private_key']);
    }

    protected function renderTemplate ($data)
    {
        return $this->controller->render ('UCLWebKeyPassBundle::private_key_form.html.twig', $data);
    }
}
