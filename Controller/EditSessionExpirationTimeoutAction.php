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
use UCL\WebKeyPassBundle\Form\SessionExpirationTimeoutForm;

class EditSessionExpirationTimeoutAction extends FormAction
{
    protected $fullname = 'Set session expiration timeout';
    protected $success_msg = 'Session expiration timeout set.';

    protected function getForm ()
    {
        return new SessionExpirationTimeoutForm ();
    }

    protected function getFormData ()
    {
        $settings = new Settings ();
        $value = $settings->getSessionExpirationTimeout ();

        return array ('session_expiration_timeout' => $value);
    }

    protected function formIsValid ($form)
    {
        $form_data = $form->getData ();

        if (preg_match ('/^\d+$/', $form_data['session_expiration_timeout']) != 1)
        {
            $msg = 'Only digits are allowed.';
            $form->addError (new FormError ($msg));
            return false;
        }

        return true;
    }

    protected function saveData ($db_manager, $form)
    {
        $form_data = $form->getData ();
        $settings = new Settings ();
        $settings->setSessionExpirationTimeout ($form_data['session_expiration_timeout']);
    }

    protected function renderTemplate ($data)
    {
        return $this->controller->render ('UCLWebKeyPassBundle::admin_form.html.twig', $data);
    }
}
