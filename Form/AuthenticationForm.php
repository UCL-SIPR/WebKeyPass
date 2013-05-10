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

class AuthenticationForm extends AbstractType
{
    private $auth_repo = null;

    public function setAuthRepo ($auth_repo)
    {
        $this->auth_repo = $auth_repo;
    }

    private function getLoginChoices ()
    {
        $logins = $this->auth_repo->getLogins ();
        $choices = array (0 => '');

        foreach ($logins as $array_login)
        {
            $login = $array_login['login'];
            $choices[$login] = $login;
        }

        return $choices;
    }

    public function buildForm (FormBuilderInterface $builder, array $options)
    {
        $builder->add ('list_login', 'choice', array ('label' => 'Login',
                                                      'choices' => $this->getLoginChoices ()));

        $builder->add ('other_login', 'text', array ('label' => 'Other login',
                                                     'required' => false));

        $builder->add ('password1', 'password', array ('label' => 'Password',
                                                       'required' => false));

        $builder->add ('password2', 'password', array ('label' => 'Password (retype)',
                                                       'required' => false));
    }

    public function getName ()
    {
        return 'authentication';
    }
}
