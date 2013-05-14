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
    private $values = array ('can_create_account' => 'true',
                             'session_expiration_timeout' => 10);

    public function __construct ()
    {
        $this->readSettings ();
    }

    public function getCanCreateAccount ()
    {
        return $this->values['can_create_account'] == 'true';
    }

    public function setCanCreateAccount ($can_create_account)
    {
        if ($can_create_account)
        {
            $this->values['can_create_account'] = 'true';
        }
        else
        {
            $this->values['can_create_account'] = 'false';
        }

        $this->writeSettings ();
    }

    public function getSessionExpirationTimeout ()
    {
        return $this->values['session_expiration_timeout'];
    }

    public function setSessionExpirationTimeout ($value)
    {
        $this->values['session_expiration_timeout'] = $value;

        $this->writeSettings ();
    }

    private function getSettingsFilename ()
    {
        return __DIR__ . '/../Resources/config/settings';
    }

    private function readSettings ()
    {
        $filename = $this->getSettingsFilename ();

        if (!file_exists ($filename))
        {
            return;
        }

        $lines = file ($filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line)
        {
            preg_match ("/(?P<setting>\S+) (?P<value>\S+)/", $line, $matches);
            $setting = $matches['setting'];
            $value = $matches['value'];

            $this->values[$setting] = $value;
        }
    }

    private function writeSettings ()
    {
        $contents = "";

        foreach ($this->values as $name => $value)
        {
            $contents .= "$name $value\n";
        }

        $filename = $this->getSettingsFilename ();
        file_put_contents ($filename, $contents);
    }
}
