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

class Settings
{
    public function getCanCreateAccount ()
    {
        $filename = $this->getSettingsFilename ();

        if (!file_exists ($filename))
        {
            return true;
        }

        $lines = file ($filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        preg_match ("/(?P<setting>\S+) (?P<value>\S+)/", $lines[0], $matches);

        if ($matches['setting'] == 'can_create_account')
        {
            return $matches['value'] == 'true';
        }

        return false;
    }

    public function setCanCreateAccount ($can_create_account)
    {
        if ($can_create_account)
        {
            $can_create_account_str = "true";
        }
        else
        {
            $can_create_account_str = "false";
        }

        $contents = "can_create_account $can_create_account_str\n";

        $filename = $this->getSettingsFilename ();

        file_put_contents ($filename, $contents);
    }

    private function getSettingsFilename ()
    {
        return __DIR__ . '/../Resources/config/settings';
    }
}
