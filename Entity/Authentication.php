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

/**
 * @ORM\Entity
 * @ORM\Table(name="authentication")
 */
class Authentication
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Node", inversedBy="authentications")
     * @ORM\JoinColumn(name="node_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $node;

    /**
     * @ORM\Column(type="string")
     */
    private $login;

    /**
     * @ORM\Column(type="string")
     */
    private $password;

    /* Getters and Setters */

    public function getId()
    {
        return $this->id;
    }

    public function setLogin($login)
    {
        $this->login = $login;

        return $this;
    }

    public function getLogin()
    {
        return $this->login;
    }

    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setNode(\UCL\WebKeyPassBundle\Entity\Node $node = null)
    {
        $this->node = $node;

        return $this;
    }

    public function getNode()
    {
        return $this->node;
    }
}
