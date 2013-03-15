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
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="UCL\WebKeyPassBundle\Entity\NodeRepository")
 * @ORM\Table(name="node")
 */
class Node
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity="Node", mappedBy="parent")
     */
    private $children;

    /**
     * @ORM\ManyToOne(targetEntity="Node", inversedBy="children")
     * @ORM\JoinColumn(name="parent", referencedColumnName="id", nullable=true, onDelete="CASCADE")
     */
    private $parent;

    /**
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $hostname;

    /**
     * @ORM\Column(type="smallint")
     */
    private $type;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $comment;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $icon;

    /**
     * @ORM\OneToMany(targetEntity="Authentication", mappedBy="node")
     */
    private $authentications;

    public function __construct()
    {
        $this->children = new ArrayCollection();
    }

    /* Getters and Setters */

    public function getId()
    {
        return $this->id;
    }

    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setHostname($hostname)
    {
        $this->hostname = $hostname;

        return $this;
    }

    public function getHostname()
    {
        return $this->hostname;
    }

    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    public function getComment()
    {
        return $this->comment;
    }

    public function addChildren(\UCL\WebKeyPassBundle\Entity\Node $children)
    {
        $this->children[] = $children;

        return $this;
    }

    public function removeChildren(\UCL\WebKeyPassBundle\Entity\Node $children)
    {
        $this->children->removeElement($children);
    }

    public function getChildren()
    {
        return $this->children;
    }

    public function setParent(\UCL\WebKeyPassBundle\Entity\Node $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    public function getParent()
    {
        return $this->parent;
    }

    public function setIcon($icon)
    {
        $this->icon = $icon;

        return $this;
    }

    public function getIcon()
    {
        return $this->icon;
    }

    public function addAuthentication(\UCL\WebKeyPassBundle\Entity\Authentication $authentications)
    {
        $this->authentications[] = $authentications;

        return $this;
    }

    public function removeAuthentication(\UCL\WebKeyPassBundle\Entity\Authentication $authentications)
    {
        $this->authentications->removeElement($authentications);
    }

    public function getAuthentications()
    {
        return $this->authentications;
    }

    /* Other public functions */

    public function getTypeStr()
    {
        switch ($this->getType ())
        {
            case 0:
                return 'category';

            case 1:
                return 'server';

            case 2:
                return 'vm';

            default:
                return 'other';
        }
    }
}
