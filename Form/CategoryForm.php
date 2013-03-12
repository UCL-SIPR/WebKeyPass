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

class CategoryForm extends AbstractType
{
    private function getAllIcons ()
    {
        /* TODO use the Finder Symfony2 component */

        return array ('apple.png' => 'apple.png',
                      'microsoft.png' => 'microsoft.png',
                      'solaris.png' => 'solaris.png',
                      'tux.png' => 'tux.png');
    }

    public function buildForm (FormBuilderInterface $builder, array $options)
    {
        $icons = $this->getAllIcons ();

        $builder->add ('name');
        $builder->add ('icon', 'choice', array ('choices' => $icons));
    }

    public function getName ()
    {
        return 'name';
    }

    public function getIcon ()
    {
        return 'icon';
    }
}
