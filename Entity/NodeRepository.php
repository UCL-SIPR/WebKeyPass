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

            $tree[] = array ('id' => $node->getId (),
                             'name' => $node->getName (),
                             'class' => $node->getTypeStr (),
                             'icon' => $node->getIcon (),
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

    public function searchNames ($search_text)
    {
        $nodes = $this->getEntityManager ()
            ->createQueryBuilder ()
            ->select ('node')
            ->from ('UCLWebKeyPassBundle:Node', 'node')
            ->where ('node.name like :search_text')
            ->setParameter ('search_text', '%'.$search_text.'%')
            ->getQuery ()
            ->getResult ();

        sort ($nodes, SORT_STRING);
        return $nodes;
    }
}
