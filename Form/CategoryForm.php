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

class CategoryForm extends AbstractType
{
    private function getAllIcons ()
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
        $icons = $this->getAllIcons ();

        $builder->add ('name');
        $builder->add ('icon', 'choice', array ('choices' => $icons));

        $category_type = 0;
        $builder->add ('type', 'hidden', array ('data' => $category_type));
    }

    public function getName ()
    {
        return 'name';
    }

    public function getIcon ()
    {
        return 'icon';
    }

    public function getType ()
    {
        return 'type';
    }
}
