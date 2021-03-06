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

use Symfony\Component\Finder\Finder;

class Icons
{
    public function getIcons ()
    {
        $finder = new Finder ();
        $finder->files ()->in (__DIR__ . '/../Resources/public/icons/');
        $finder->name ('*.png');
        $finder->sortByName ();

        $icons = array ();

        foreach ($finder as $icon)
        {
            $icons[] = basename ($icon, '.png');
        }

        return $icons;
    }

    public function nameIsValid ($icon_name)
    {
        return preg_match ("/^[a-zA-Z0-9-_]+$/", $icon_name) == 1;
    }
}
