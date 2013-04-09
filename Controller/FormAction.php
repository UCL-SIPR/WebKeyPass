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

use Symfony\Component\HttpFoundation\Request;

class FormAction extends Action
{
    protected function getForm ()
    {
    }

    protected function saveData ($db_manager, $form)
    {
    }

    protected function renderTemplate ($data)
    {
        return $this->controller->render ('UCLWebKeyPassBundle::form.html.twig', $data);
    }

    public function handleForm ()
    {
        $data = $this->controller->getCommonData ();
        $data['action'] = $this->fullname;

        $form = $this->controller->createForm ($this->getForm (), $this->node);

        $request = $this->controller->getRequest ();

        if ($request->isMethod ('POST'))
        {
            $form->bind ($request);

            if ($form->isValid ())
            {
                $db_manager = $this->controller->getDoctrine ()->getManager ();
                $this->saveData ($db_manager, $form);
                $db_manager->flush ();

                $this->addFlashMessage ($this->success_msg);

                return $this->controller->redirect ($this->redirect_url);
            }
        }

        $data['form'] = $form->createView ();
        return $this->renderTemplate ($data);
    }
}
