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
use UCL\WebKeyPassBundle\Entity\Node;
use UCL\WebKeyPassBundle\Entity\Authentication;

class CloneAction extends Action
{
    private function cloneAuthentications ($node, $new_node, $db_manager)
    {
        foreach ($node->getAuthentications () as $auth)
        {
            $new_auth = new Authentication ();
            $new_auth->setLogin ($auth->getLogin ());
            $new_auth->setPassword ($auth->getPassword ());
            $new_auth->setMcryptIv ($auth->getMcryptIv ());
            $new_auth->setNode ($new_node);

            $db_manager->persist ($new_auth);
        }
    }

    private function cloneNode ($node, $parent, $db_manager)
    {
        $new_node = new Node ();
        $new_node->setName ($node->getName ());
        $new_node->setHostname ($node->getHostname () . " [clone]");
        $new_node->setSerialNumber ($node->getSerialNumber () . " [clone]");
        $new_node->setType ($node->getType ());
        $new_node->setComment ($node->getComment ());
        $new_node->setIcon ($node->getIcon ());
        $new_node->setParent ($parent);

        $db_manager->persist ($new_node);

        $this->cloneAuthentications ($node, $new_node, $db_manager);

        foreach ($node->getChildren () as $child)
        {
            $this->cloneNode ($child, $new_node, $db_manager);
        }
    }

    public function perform ($success_msg)
    {
        $db_manager = $this->controller->getDoctrine ()->getManager ();

        $this->cloneNode ($this->node,
                          $this->node->getParent (),
                          $db_manager);

        $db_manager->flush ();

        $this->addFlashMessage ($success_msg);

        return $this->controller->redirect ($this->redirect_url);
    }
}
