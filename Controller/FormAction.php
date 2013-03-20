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
    private $submit_route = '';
    private $submit_route_data = array ();
    private $redirect_url = '';

    /* Set submit route.
     * When the form is submitted, where to go?
     * TODO: get this information automatically, based on the controller
     */
    public function setSubmitRoute ($route, $route_data = array ())
    {
        $this->submit_route = $route;
        $this->submit_route_data = $route_data;
    }

    /* Set redirect route when the action has been performed successfully. */
    public function setRedirectRoute ($route, $route_data = array ())
    {
        $this->redirect_url = $this->controller->generateUrl ($route, $route_data);
    }

    protected function getForm ()
    {
    }

    protected function saveData ($db_manager, $form)
    {
    }

    public function handleForm ()
    {
        $data = $this->controller->getCommonData ();
        $data['action'] = $this->fullname;
        $data['submit_route'] = $this->submit_route;
        $data['submit_route_data'] = $this->submit_route_data;

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

                $flash_bag = $this->controller->get ('session')->getFlashBag ();
                $flash_bag->add ('notice', $this->success_msg);

                return $this->controller->redirect ($this->redirect_url);
            }
        }

        $data['form'] = $form->createView ();
        return $this->controller->render ('UCLWebKeyPassBundle::form.html.twig', $data);
    }
}
