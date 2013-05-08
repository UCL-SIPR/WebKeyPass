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

class MasterKey
{
    private $controller;

    public function __construct ($controller)
    {
        $this->controller = $controller;
    }

    private function getSessionPrivateKey ()
    {
        $session = $this->controller->get ('session');
        $private_key = $session->get ('private_key');

#        echo "Private key: $private_key<br />\n";
        return $private_key;
    }

    private function getCryptModule ($key)
    {
        $crypt_module = mcrypt_module_open (MCRYPT_RIJNDAEL_256, '', MCRYPT_MODE_ECB, '');

        $iv = mcrypt_create_iv (mcrypt_enc_get_iv_size ($crypt_module), MCRYPT_DEV_URANDOM);

        $max_key_size = mcrypt_enc_get_key_size ($crypt_module);
        $key = substr (md5 ($key), 0, $max_key_size);

        mcrypt_generic_init ($crypt_module, $key, $iv);

        return $crypt_module;
    }

    private function closeCryptModule ($crypt_module)
    {
        mcrypt_generic_deinit ($crypt_module);
        mcrypt_module_close ($crypt_module);
    }

    public function encryptMasterKey ($master_key, $user)
    {
        $crypt_module = $this->getCryptModule ($user->getPrivateKey ());
        $encrypted_master_key = mcrypt_generic ($crypt_module, $master_key);
        $this->closeCryptModule ($crypt_module);

        $user->setEncryptedMasterKey ($encrypted_master_key);
        $user->setPrivateKey ('');
    }

    public function decryptMasterKey ($user)
    {
        $crypt_module = $this->getCryptModule ($this->getSessionPrivateKey ());
        $master_key = mdecrypt_generic ($crypt_module, $user->getEncryptedMasterKey ());
        $this->closeCryptModule ($crypt_module);

        $ret = trim ($master_key);
#        echo "master key: $tmp<br />\n";

        return $ret;
    }

    public function encryptPassword ($password, $user)
    {
        $master_key = $this->decryptMasterKey ($user);

        $crypt_module = $this->getCryptModule ($master_key);
        $encrypted_password = mcrypt_generic ($crypt_module, $password);
        $this->closeCryptModule ($crypt_module);

        return $encrypted_password;
    }

    public function decryptPassword ($encrypted_password, $user)
    {
        $master_key = $this->decryptMasterKey ($user);

        $crypt_module = $this->getCryptModule ($master_key);
        $decrypted_password = mdecrypt_generic ($crypt_module, $encrypted_password);
        $this->closeCryptModule ($crypt_module);

        return trim ($decrypted_password);
    }
}
