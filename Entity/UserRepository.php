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

class UserRepository extends EntityRepository
{
    public function getAllUsers ()
    {
        return $this->getEntityManager ()
            ->createQueryBuilder ()
            ->select ('user')
            ->from ('UCLWebKeyPassBundle:User', 'user')
            ->orderBy ('user.username', 'ASC')
            ->getQuery ()
            ->getResult ();
    }

    public function userExists ($username)
    {
        $nb_results = $this->getEntityManager ()
            ->createQueryBuilder ()
            ->select ('count(user)')
            ->from ('UCLWebKeyPassBundle:User', 'user')
            ->where ('user.username = :username')
            ->setParameter ('username', $username)
            ->getQuery ()
            ->getSingleScalarResult ();

        return $nb_results > 0;
    }
}
