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
use UCL\WebKeyPassBundle\Entity\User;

class AdminController extends Controller
{
    public function getCommonData ()
    {
        $data = array ();
        $data['title'] = 'Admin Zone';
        return $data;
    }

    private function getUserRepo ()
    {
        return $this->getDoctrine ()->getRepository ('UCLWebKeyPassBundle:User');
    }

    private function getLogRepo ()
    {
        return $this->getDoctrine ()->getRepository ('UCLWebKeyPassBundle:Log');
    }

    private function getUserList ()
    {
        $user_repo = $this->getUserRepo ();
        $all_users = $user_repo->getAllUsers ();

        $list = array ();
        foreach ($all_users as $user)
        {
            $list[] = array ('id' => $user->getId (),
                             'username' => $user->getUsername (),
                             'is_active' => $user->getIsActive (),
                             'is_admin' => $user->getIsAdmin (),
                             'route_data' => array ('user_id' => $user->getId ()));
        }

        return $list;
    }

    private function getUserFromId ($user_id)
    {
        $user_repo = $this->getUserRepo ();
        $user = $user_repo->find ($user_id);

        if (!$user)
        {
            throw $this->createNotFoundException ('User id '.$user_id.' not found');
        }

        return $user;
    }

    private function getAuthenticatedUser ()
    {
        return $this->get ('security.context')->getToken ()->getUser ();
    }

    public function showUserListAction ()
    {
        $data = $this->getCommonData ();
        $data['users'] = $this->getUserList ();
        $data['auth_user_id'] = $this->getAuthenticatedUser ()->getId ();

        return $this->render ('UCLWebKeyPassBundle::admin_user_list.html.twig', $data);
    }

    public function removeUserAction ($user_id)
    {
        $user = $this->getUserFromId ($user_id);

        $action = new RemoveAction ($this, $user);
        $action->setRedirectRoute ('ucl_wkp_admin_user_list');

        $success_msg = 'User removed successfully.';
        return $action->perform ($success_msg);
    }

    public function addUserAction ()
    {
        $action = new AddUserAction ($this, null);
        $action->setRedirectRoute ('ucl_wkp_admin_user_list');

        return $action->handleForm ();
    }

    public function editUserAction ($user_id)
    {
        $user = $this->getUserFromId ($user_id);
        $action = new EditUserAction ($this, $user);
        $action->setRedirectRoute ('ucl_wkp_admin_user_list');

        return $action->handleForm ();
    }

    private function getLogEntries ($year_month)
    {
        $log_repo = $this->getLogRepo ();
        $logs = $log_repo->getLogs ($year_month);

        $log_entries = array ();
        foreach ($logs as $log)
        {
            $log_entry = array ();
            $log_entry['type'] = $log->getType ();
            $log_entry['info'] = $log->getComment ();
            $log_entry['date'] = $log->getDateStr ();

            $log_entries[] = $log_entry;
        }

        return $log_entries;
    }

    public function showLogAction ($year_month)
    {
        $data = $this->getCommonData ();
        $data['log_entries'] = $this->getLogEntries ($year_month);

        return $this->render ('UCLWebKeyPassBundle::admin_log.html.twig', $data);
    }

    private function getLogMonths ()
    {
        $log_repo = $this->getLogRepo ();
        $months = $log_repo->getMonths ();
        $log_months = array ();

        foreach ($months as $month)
        {
            $log_months[] = array ('route_data' => $month,
                                   'str' => $month['year_month']);
        }

        return $log_months;
    }

    public function showLogMonthsAction ()
    {
        $data = $this->getCommonData ();
        $data['months'] = $this->getLogMonths ();

        return $this->render ('UCLWebKeyPassBundle::admin_log_months.html.twig', $data);
    }
}
