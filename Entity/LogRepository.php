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

class LogRepository extends EntityRepository
{
    public function getMonths ()
    {
        return $this->getEntityManager ()
            ->createQueryBuilder ()
            ->select ('distinct substring(log.date, 1, 7) as year_month')
            ->from ('UCLWebKeyPassBundle:Log', 'log')
            ->orderBy ('year_month', 'DESC')
            ->getQuery ()
            ->getResult ();
    }

    public function getLogs ($year_month)
    {
        return $this->getEntityManager ()
            ->createQueryBuilder ()
            ->select ('log')
            ->from ('UCLWebKeyPassBundle:Log', 'log')
            ->where ('substring(log.date, 1, 7) = :year_month')
            ->setParameter ('year_month', $year_month)
            ->orderBy ('log.date', 'DESC')
            ->getQuery ()
            ->getResult ();
    }

    public function clearLogs ()
    {
        $connection = $this->getEntityManager ()->getConnection ();
        $platform = $connection->getDatabasePlatform ();

        $cascade = false;
        $truncate = $platform->getTruncateTableSQL ('log', $cascade);

        $connection->executeUpdate ($truncate);
    }
}
