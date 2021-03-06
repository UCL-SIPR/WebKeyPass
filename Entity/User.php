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

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use UCL\WebKeyPassBundle\Controller\PrivateKey;

/**
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="UCL\WebKeyPassBundle\Entity\UserRepository")
 */
class User implements AdvancedUserInterface, \Serializable
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=25, unique=true)
     */
    private $username;

    /**
     * @ORM\Column(type="string")
     * Salt used for the password hash.
     */
    private $salt;

    /**
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;

    /**
     * @ORM\Column(name="is_admin", type="boolean")
     */
    private $isAdmin;

    /**
     * @ORM\Column(type="string")
     */
    private $first_name;

    /**
     * @ORM\Column(type="string")
     */
    private $last_name;

    /**
     * @ORM\Column(type="string")
     */
    private $email;

    /**
     * @ORM\Column(type="string")
     */
    private $private_key;

    /**
     * @ORM\Column(type="string")
     */
    private $private_key_hash;

    /**
     * @ORM\Column(type="string")
     */
    private $private_key_salt;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $encrypted_master_key;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $mcrypt_iv;

    /**
     * @ORM\Column(type="boolean")
     */
    private $with_shibboleth;

    public function __construct()
    {
        $this->isActive = true;
        $this->salt = md5 (uniqid (null, true));
        $this->private_key_salt = md5 (uniqid (null, true));
    }

    public function getRoles()
    {
        if ($this->isAdmin)
        {
            return array('ROLE_ADMIN');
        }
        else
        {
            return array('ROLE_USER');
        }
    }

    public function eraseCredentials()
    {
    }

    public function serialize()
    {
        return serialize(array(
            $this->id,
        ));
    }

    public function unserialize($serialized)
    {
        list (
            $this->id,
        ) = unserialize($serialized);
    }

    public function isAccountNonExpired()
    {
        return true;
    }

    public function isAccountNonLocked()
    {
        return true;
    }

    public function isCredentialsNonExpired()
    {
        return true;
    }

    public function isEnabled()
    {
        return $this->isActive;
    }

    /* Getters and setters */

    public function getId()
    {
        return $this->id;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function setUsername($username)
    {
        $this->username = $username;
        return $this;
    }

    public function getSalt()
    {
        return $this->salt;
    }

    public function setSalt ($salt)
    {
        $this->salt = $salt;
        return $this;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    public function getIsActive()
    {
        return $this->isActive;
    }

    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
        return $this;
    }

    public function getIsAdmin()
    {
        return $this->isAdmin;
    }

    public function setIsAdmin($isAdmin)
    {
        $this->isAdmin = $isAdmin;
        return $this;
    }

    public function setFirstName($firstName)
    {
        $this->first_name = $firstName;

        return $this;
    }

    public function getFirstName()
    {
        return $this->first_name;
    }

    public function setLastName($lastName)
    {
        $this->last_name = $lastName;

        return $this;
    }

    public function getLastName()
    {
        return $this->last_name;
    }

    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setPrivateKey($private_key)
    {
        $this->private_key = $private_key;

        if ($private_key != null && $private_key != "")
        {
            $hash = PrivateKey::getHash ($private_key, $this->getPrivateKeySalt ());
            $this->setPrivateKeyHash ($hash);
        }

        return $this;
    }

    public function getPrivateKey()
    {
        return $this->private_key;
    }

    public function setPrivateKeyHash($private_key_hash)
    {
        $this->private_key_hash = $private_key_hash;

        return $this;
    }

    public function getPrivateKeyHash()
    {
        return $this->private_key_hash;
    }

    public function setPrivateKeySalt($private_key_salt)
    {
        $this->private_key_salt = $private_key_salt;

        return $this;
    }

    public function getPrivateKeySalt()
    {
        return $this->private_key_salt;
    }

    public function setEncryptedMasterKey($encryptedMasterKey)
    {
        $this->encrypted_master_key = base64_encode ($encryptedMasterKey);
        return $this;
    }

    public function getEncryptedMasterKey()
    {
        return base64_decode ($this->encrypted_master_key);
    }

    public function getMcryptIv ()
    {
        return base64_decode ($this->mcrypt_iv);
    }

    public function setMcryptIv ($mcrypt_iv)
    {
        $this->mcrypt_iv = base64_encode ($mcrypt_iv);
        return $this;
    }

    public function getWithShibboleth ()
    {
        return $this->with_shibboleth;
    }

    public function setWithShibboleth ($with_shibboleth)
    {
        $this->with_shibboleth = $with_shibboleth;
        return $this;
    }
}
