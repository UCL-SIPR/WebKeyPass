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
    private function getCryptModule ($user)
    {
        $crypt_module = mcrypt_module_open (MCRYPT_RIJNDAEL_256, '', MCRYPT_MODE_ECB, '');

        $iv = mcrypt_create_iv (mcrypt_enc_get_iv_size ($crypt_module), MCRYPT_DEV_RANDOM);

        $max_key_size = mcrypt_enc_get_key_size ($crypt_module);
        $private_key = substr (md5 ($user->getPrivateKey ()), 0, $max_key_size);

        mcrypt_generic_init ($crypt_module, $private_key, $iv);

        return $crypt_module;
    }

    private function closeCryptModule ($crypt_module)
    {
        mcrypt_generic_deinit ($crypt_module);
        mcrypt_module_close ($crypt_module);
    }

    public function encryptMasterKey ($master_key, $user)
    {
        $crypt_module = $this->getCryptModule ($user);
        $encrypted_master_key = mcrypt_generic ($crypt_module, $master_key);
        $this->closeCryptModule ($crypt_module);

        $user->setEncryptedMasterKey ($encrypted_master_key);
    }

    public function decryptMasterKey ($user)
    {
        $crypt_module = $this->getCryptModule ($user);
        $master_key = mdecrypt_generic ($crypt_module, $user->getEncryptedMasterKey ());
        $this->closeCryptModule ($crypt_module);

        return trim ($master_key);
    }
}
