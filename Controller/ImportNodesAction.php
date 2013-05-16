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

use UCL\WebKeyPassBundle\Form\ImportNodesForm;
use UCL\WebKeyPassBundle\Entity\Node;
use UCL\WebKeyPassBundle\Entity\Authentication;

class ImportNodesAction extends FormAction
{
    protected $fullname = 'Import nodes';
    protected $success_msg = 'Nodes imported successfully.';

    protected function getForm ()
    {
        return new ImportNodesForm ();
    }

    protected function getFormData ()
    {
        return array ('filename' => '');
    }

    private function saveAuth ($db_manager, $auth_data, $node)
    {
        $user = $this->controller->getAuthenticatedUser ();
        $master_key = new MasterKey ($this->controller);

        $iv = null;
        $encrypted_password = $master_key->encryptPassword ($auth_data['password'], $user, $iv);

        $auth = new Authentication ();
        $auth->setNode ($node);
        $auth->setLogin ($auth_data['login']);
        $auth->setPassword ($encrypted_password);
        $auth->setMcryptIv ($iv);

        $db_manager->persist ($auth);
    }

    private function saveNode ($db_manager, $node_data, $parent)
    {
        $node = new Node ();
        $node->setParent ($parent);
        $node->setName ($node_data['name']);
        $node->setHostname ($node_data['hostname']);
        $node->setSerialNumber ($node_data['serial_number']);
        $node->setTypeStr ($node_data['type']);
        $node->setComment ($node_data['comment']);
        $node->setIcon ($node_data['icon']);

        $db_manager->persist ($node);

        foreach ($node_data['auths'] as $auth_data)
        {
            $this->saveAuth ($db_manager, $auth_data, $node);
        }

        foreach ($node_data['subnodes'] as $subnode)
        {
            $this->saveNode ($db_manager, $subnode, $node);
        }
    }

    protected function saveData ($db_manager, $form)
    {
        $form_data = $form->getData ();
        $file = $form_data['filename']->openFile ();

        $json_data = "";
        foreach ($file as $line)
        {
            $json_data .= $line;
        }

        $data = json_decode ($json_data, true);

        foreach ($data as $node_data)
        {
            $this->saveNode ($db_manager, $node_data, null);
        }
    }

    protected function renderTemplate ($data)
    {
        return $this->controller->render ('UCLWebKeyPassBundle::admin_form.html.twig', $data);
    }
}
