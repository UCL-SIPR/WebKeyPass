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

class UserForm extends AbstractType
{
    protected $has_password = true;

    public function buildForm (FormBuilderInterface $builder, array $options)
    {
        $builder->add ('username');

        if ($this->has_password)
        {
            $builder->add ('password');
        }

        $builder->add ('isActive', 'checkbox', array ('label' => 'Is active',
                                                      'required' => false));

        $builder->add ('isAdmin', 'checkbox', array ('label' => 'Is admin',
                                                     'required' => false));
    }

    public function setDefaultOptions (OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults (array ('data_class' => 'UCL\WebKeyPassBundle\Entity\User'));
    }

    public function getName ()
    {
        return 'name';
    }
}
