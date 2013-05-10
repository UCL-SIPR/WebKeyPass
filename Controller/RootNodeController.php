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
use UCL\WebKeyPassBundle\Form\CategoryForm;
use UCL\WebKeyPassBundle\Entity\Node;

class RootNodeController extends NodeController
{
    protected function getRootActions ()
    {
        return array (array ('name' => 'Add Category',
                             'route' => 'ucl_wkp_root_add_category',
                             'route_data' => array ()));
    }

    public function getCommonData ()
    {
        $data = array ();
        $data['title'] = 'Root';
        $data['path'] = array ();

        $node_repo = $this->getNodeRepo ();
        $data['nodes'] = $node_repo->getNodes ();

        $data['search_form'] = $this->getSearchForm ()->createView ();

        $data['node_types'] = $this->getNodeTypes ();

        $data['open_nodes'] = array ();
        $data['node_id'] = -1;

        return $data;
    }

    public function viewRootAction ()
    {
        $user = $this->getAuthenticatedUser ();

        /* First visit: if the master key doesn't exist, redirect to the admin
         * zone directly. */
        if ($user->getIsAdmin () &&
            $user->getEncryptedMasterKey () == "" &&
            $user->getPrivateKey () != "")
        {
            $redirect_url = $this->generateUrl ('ucl_wkp_admin_master_key');
            return $this->redirect ($redirect_url);
        }

        if ($user->getEncryptedMasterKey () == "")
        {
            $msg = 'The master key is not encrypted for you.';
            $flash_bag = $this->get ('session')->getFlashBag ();
            $flash_bag->add ('error', $msg);

            $redirect_url = $this->generateUrl ('ucl_wkp_login');
            return $this->redirect ($redirect_url);
        }

        $data = $this->getCommonData ();
        $data['infos'] = $this->getEmptyNodeInfos ();
        $data['authentications'] = array ();
        $data['actions'] = $this->getRootActions ();

        return $this->render ('UCLWebKeyPassBundle::node.html.twig', $data);
    }

    public function addCategoryAction ()
    {
        $action = new AddCategoryAction ($this, null);
        $action->setRedirectRoute ('ucl_wkp_root_view');

        return $action->handleForm ();
    }

    public function editUserAction ()
    {
        $user = $this->getAuthenticatedUser ();
        $action = new EditUserAction ($this, $user);
        $action->setRedirectRoute ('ucl_wkp_root_view');

        return $action->handleForm ();
    }
}
