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
    protected $has_username = true;
    protected $has_password = true;
    protected $has_old_password = false;
    protected $has_first_name = true;
    protected $has_last_name = true;
    protected $has_email = true;
    protected $has_private_key = true;
    protected $has_is_active = true;
    protected $has_is_admin = true;

    public function buildForm (FormBuilderInterface $builder, array $options)
    {
        if ($this->has_username)
        {
            $builder->add ('username');
        }

        if ($this->has_old_password)
        {
            $builder->add ('old-password', 'password', array ('label' => 'Old password',
                                                              'required' => false));
        }

        if ($this->has_password)
        {
            $builder->add ('password1', 'password', array ('label' => 'Password',
                                                           'required' => false));

            $builder->add ('password2', 'password', array ('label' => 'Password (retype)',
                                                           'required' => false));
        }

        if ($this->has_first_name)
        {
            $builder->add ('first_name');
        }

        if ($this->has_last_name)
        {
            $builder->add ('last_name');
        }

        if ($this->has_email)
        {
            $builder->add ('email');
        }

        if ($this->has_private_key)
        {
            $builder->add ('private_key');
        }

        if ($this->has_is_active)
        {
            $builder->add ('isActive', 'checkbox', array ('label' => 'Is active',
                                                          'required' => false));
        }

        if ($this->has_is_admin)
        {
            $builder->add ('isAdmin', 'checkbox', array ('label' => 'Is admin',
                                                         'required' => false));
        }
    }

    public function getName ()
    {
        return 'name';
    }
}
