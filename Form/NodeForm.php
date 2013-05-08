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

namespace UCL\WebKeyPassBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceList;
use UCL\WebKeyPassBundle\Controller\Icons;

class NodeForm extends AbstractType
{
    protected $node_type = 0; # category, by default
    protected $has_name = true;
    protected $has_list_name = false;
    protected $has_hostname = false;
    protected $has_serial_number = false;
    protected $has_icon = false;
    protected $has_comment = false;
    protected $has_parent = false;
    protected $parent_type = 0; # categories, by default

    private $node_repo = null;
    private $node_to_move = null;

    protected function getAllIcons ()
    {
        $icons = new Icons ();
        $icon_names = $icons->getIcons ();
        $all_icons = array ();

        foreach ($icon_names as $icon_name)
        {
            $all_icons[$icon_name] = $icon_name;
        }

        return $all_icons;
    }

    public function setNodeRepository ($node_repo)
    {
        $this->node_repo = $node_repo;
    }

    /* Useful, so the node to move is not selected as a parent node. */
    public function setNodeToMove ($node_to_move)
    {
        $this->node_to_move = $node_to_move;
    }

    private function getParentNodes ()
    {
        $all_nodes = $this->node_repo->getNodesByType ($this->parent_type);

        /* If the node we want to move is present in the parent nodes, remove
           it. We can not set the parent of a node as the node itself, there
           would be an infinite loop. */
        $nodes = array ();
        foreach ($all_nodes as $node)
        {
            if ($node != $this->node_to_move)
            {
                $nodes[] = $node;
            }
        }

        sort ($nodes, SORT_STRING);

        $labels = array ();
        foreach ($nodes as $node)
        {
            $labels[] = $node->__toString ();
        }

        return new ChoiceList ($nodes, $labels);
    }

    private function getNameChoices ()
    {
        $names = $this->node_repo->getNames ($this->node_type);
        $choices = array (0 => '');

        foreach ($names as $array_name)
        {
            $name = $array_name['name'];
            $choices[$name] = $name;
        }

        return $choices;
    }

    public function buildForm (FormBuilderInterface $builder, array $options)
    {
        if ($this->has_name)
        {
            $builder->add ('name');
        }

        if ($this->has_list_name)
        {
            $builder->add ('list_name', 'choice', array ('label' => 'Name',
                                                         'choices' => $this->getNameChoices ()));

            $builder->add ('other_name', 'text', array ('label' => 'Other name',
                                                        'required' => false));
        }

        if ($this->has_hostname)
        {
            $builder->add ('hostname');
        }

        if ($this->has_serial_number)
        {
            $builder->add ('serial_number');
        }

        if ($this->has_icon)
        {
            $builder->add ('icon', 'choice', array ('choices' => $this->getAllIcons ()));
        }

        if ($this->has_comment)
        {
            if ($this->has_hostname || $this->has_serial_number)
            {
                $label = "Comment";
            }
            else
            {
                $label = "Information";
            }

            $builder->add ('comment', 'textarea', array ('required' => false,
                                                         'label' => $label));
        }

        if ($this->has_parent)
        {
            $builder->add ('parent', 'choice', array ('choice_list' => $this->getParentNodes ()));
        }

        $builder->add ('type', 'hidden', array ('data' => $this->node_type));
    }

    public function getName ()
    {
        return 'name';
    }

    public function getHostname ()
    {
        return 'hostname';
    }

    public function getIcon ()
    {
        return 'icon';
    }

    public function getComment ()
    {
        return 'comment';
    }

    public function getType ()
    {
        return 'type';
    }
}
