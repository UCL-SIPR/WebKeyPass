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

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class AdminController extends Controller
{
    private function getUserRepo ()
    {
        return $this->getDoctrine ()->getRepository ('UCLWebKeyPassBundle:User');
    }

    private function getUserList ()
    {
        $user_repo = $this->getUserRepo ();
        $all_users = $user_repo->getAllUsers ();

        $list = array ();
        foreach ($all_users as $user)
        {
            $list[] = array ('username' => $user->getUsername (),
                             'is_active' => $user->getIsActive (),
                             'is_admin' => $user->getIsAdmin (),
                             'remove_route_data' => array ('user_id' => $user->getId ()));
        }

        return $list;
    }

    protected function getUserFromId ($user_id)
    {
        $user_repo = $this->getUserRepo ();
        $user = $user_repo->find ($user_id);

        if (!$user)
        {
            throw $this->createNotFoundException ('User id '.$user_id.' not found');
        }

        return $user;
    }

    public function showUserListAction ()
    {
        $data = array ();
        $data['title'] = 'Admin Zone';
        $data['users'] = $this->getUserList ();

        return $this->render ('UCLWebKeyPassBundle::admin.html.twig', $data);
    }

    public function removeAction ($user_id)
    {
        $user = $this->getUserFromId ($user_id);

        $action = new RemoveAction ($this, $user);
        $action->setRedirectRoute ('ucl_wkp_admin');

        $success_msg = 'User removed successfully.';
        return $action->perform ($success_msg);
    }
}
