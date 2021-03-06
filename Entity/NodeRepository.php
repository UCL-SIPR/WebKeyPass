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

namespace UCL\WebKeyPassBundle\Entity;

use Doctrine\ORM\EntityRepository;
use UCL\WebKeyPassBundle\Controller\MasterKey;

class NodeRepository extends EntityRepository
{
    private function getChildren ($parent_id)
    {
        $query = $this->getEntityManager ()
            ->createQueryBuilder ()
            ->select ('node')
            ->from ('UCLWebKeyPassBundle:Node', 'node');

        if ($parent_id == null)
        {
            $query = $query->where ('node.parent is null');
        }
        else
        {
            $query = $query->where ('node.parent = :parent_id')
                ->setParameter ('parent_id', $parent_id);
        }

        return $query->orderBy ('node.name', 'ASC')
            ->getQuery ()
            ->getResult ();
    }

    private function getNodesInfos ($nodes)
    {
        $tree = array ();

        foreach ($nodes as $node)
        {
            $subnodes = $this->getChildren ($node->getId ());
            $subtree = $this->getNodesInfos ($subnodes);

            if ($node->getTypeStr () == 'server')
            {
                $icon = 'server';
            }
            else if ($node->getTypeStr () == 'vm')
            {
                $icon = 'virtual-machine';
            }
            else
            {
                $icon = $node->getIcon ();
            }

            $tree[] = array ('id' => $node->getId (),
                             'name' => $node->getName (),
                             'class' => $node->getTypeStr (),
                             'icon' => $icon,
                             'subnodes' => $subtree);
        }

        if (count ($tree) == 0)
        {
            return null;
        }
        else
        {
            return $tree;
        }
    }

    public function getNodes ()
    {
        $root_nodes = $this->getChildren (null);
        return $this->getNodesInfos ($root_nodes);
    }

    private function getDecryptedAuths ($node, $controller)
    {
        $auths = array ();

        $master_key = new MasterKey ($controller);
        $user = $controller->getAuthenticatedUser ();

        foreach ($node->getAuthentications () as $auth)
        {
            $iv = $auth->getMcryptIv ();
            $decrypted_password = $master_key->decryptPassword ($auth->getPassword (), $user, $iv);

            $auths[] = array ('login' => $auth->getLogin (),
                              'password' => $decrypted_password);
        }

        return $auths;
    }

    private function getNodesInfosForExport ($nodes, $controller)
    {
        $tree = array ();

        foreach ($nodes as $node)
        {
            $subnodes = $this->getChildren ($node->getId ());
            $subtree = $this->getNodesInfosForExport ($subnodes, $controller);

            $tree[] = array ('name' => $node->getName (),
                             'hostname' => $node->getHostname (),
                             'serial_number' => $node->getSerialNumber (),
                             'type' => $node->getTypeStr (),
                             'comment' => $node->getComment (),
                             'icon' => $node->getIcon (),
                             'auths' => $this->getDecryptedAuths ($node, $controller),
                             'subnodes' => $subtree);
        }

        return $tree;
    }

    /* The controller is needed to decrpyt the passwords. */
    public function getNodesForExport ($controller)
    {
        $root_nodes = $this->getChildren (null);
        return $this->getNodesInfosForExport ($root_nodes, $controller);
    }

    public function getNodesByType ($node_type)
    {
        $query = $this->getEntityManager ()
            ->createQueryBuilder ()
            ->select ('node')
            ->from ('UCLWebKeyPassBundle:Node', 'node');

        if ($node_type != -1)
        {
            $query = $query->where ('node.type = :node_type')
                ->setParameter ('node_type', $node_type);
        }

        return $query->getQuery ()->getResult ();
    }

    private function search ($search_text, $field)
    {
        $nodes = $this->getEntityManager ()
            ->createQueryBuilder ()
            ->select ('node')
            ->from ('UCLWebKeyPassBundle:Node', 'node')
            ->where ('node.'.$field.' like :search_text')
            ->setParameter ('search_text', '%'.$search_text.'%')
            ->getQuery ()
            ->getResult ();

        sort ($nodes, SORT_STRING);
        return $nodes;
    }

    public function searchNames ($search_text)
    {
        return $this->search ($search_text, 'name');
    }

    public function searchSerialNumbers ($search_text)
    {
        return $this->search ($search_text, 'serial_number');
    }

    public function getNames ($node_type)
    {
        $names = $this->getEntityManager ()
            ->createQueryBuilder ()
            ->select ('distinct node.name')
            ->from ('UCLWebKeyPassBundle:Node', 'node')
            ->where ('node.type = :node_type')
            ->setParameter ('node_type', $node_type)
            ->orderBy ('node.name', 'ASC')
            ->getQuery ()
            ->getResult ();

        return $names;
    }

    public function hostnameExists ($hostname, $except_node = null)
    {
        $query = $this->getEntityManager ()
            ->createQueryBuilder ()
            ->select ('count(node)')
            ->from ('UCLWebKeyPassBundle:Node', 'node')
            ->where ('node.hostname = :hostname')
            ->setParameter ('hostname', $hostname);

        if ($except_node != null)
        {
            $query = $query->andWhere ('node.id <> :node_id')
                ->setParameter ('node_id', $except_node->getId ());
        }

        $nb_results = $query->getQuery ()
            ->getSingleScalarResult ();

        return $nb_results > 0;
    }

    public function serialExists ($serial_number, $except_node)
    {
        $query = $this->getEntityManager ()
            ->createQueryBuilder ()
            ->select ('count(node)')
            ->from ('UCLWebKeyPassBundle:Node', 'node')
            ->where ('node.serial_number = :serial_number')
            ->setParameter ('serial_number', $serial_number);

        if ($except_node != null)
        {
            $query = $query->andWhere ('node.id <> :node_id')
                ->setParameter ('node_id', $except_node->getId ());
        }

        $nb_results = $query->getQuery ()
            ->getSingleScalarResult ();

        return $nb_results > 0;
    }
}
