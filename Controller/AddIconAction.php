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
use UCL\WebKeyPassBundle\Form\IconForm;

class AddIconAction extends FormAction
{
    protected $fullname = 'Add Icon';
    protected $success_msg = 'Icon added successfully.';

    protected function getForm ()
    {
        return new IconForm ();
    }

    protected function getFormData ()
    {
        return array ('name' => '',
                      'icon' => '');
    }

    protected function formIsValid ($form)
    {
        $form_data = $form->getData ();
        $name = $form_data['name'];

        $icons = new Icons ();
        if (!$icons->nameIsValid ($name))
        {
            $msg = 'The name must contain only letters, digits, "-" or "_".';
            $form->addError (new FormError ($msg));
            return false;
        }

        return true;
    }

    protected function saveData ($db_manager, $form)
    {
        $form_data = $form->getData ();
        $file = $form_data['icon'];
        $file->move (__DIR__ . '/../Resources/public/icons/', $form_data['name'] . '.png');
    }

    protected function renderTemplate ($data)
    {
        return $this->controller->render ('UCLWebKeyPassBundle::admin_form.html.twig', $data);
    }
}
