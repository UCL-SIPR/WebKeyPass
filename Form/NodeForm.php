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
use Symfony\Component\Finder\Finder;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class NodeForm extends AbstractType
{
    protected $node_type = 0; # category, by default
    protected $has_name = true;
    protected $has_hostname = false;
    protected $has_icon = false;
    protected $has_comment = false;
    protected $has_parent = false;
    protected $parent_type = 0; # categories, by default

    protected function getAllIcons ()
    {
        $finder = new Finder ();
        $finder->files ()->in (__DIR__ . '/../Resources/public/icons/');
        $finder->name ('*.png');
        $finder->sortByName ();

        $icons = array ();

        foreach ($finder as $icon)
        {
            $icon_name = basename ($icon, '.png');
            $icons[$icon_name] = $icon_name;
        }

        return $icons;
    }

    public function buildForm (FormBuilderInterface $builder, array $options)
    {
        if ($this->has_name)
        {
            $builder->add ('name');
        }

        if ($this->has_hostname)
        {
            $builder->add ('hostname');
        }

        if ($this->has_icon)
        {
            $builder->add ('icon', 'choice', array ('choices' => $this->getAllIcons ()));
        }

        if ($this->has_comment)
        {
            $builder->add ('comment');
        }

        if ($this->has_parent)
        {
            $builder->add ('parent',
                           'entity',
                           array ('class' => 'UCLWebKeyPassBundle:Node',
                                  'property' => 'name'));
        }

        $builder->add ('type', 'hidden', array ('data' => $this->node_type));
    }

    public function setDefaultOptions (OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults (array ('data_class' => 'UCL\WebKeyPassBundle\Entity\Node'));
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
