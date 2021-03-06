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

namespace UCL\WebKeyPassBundle\Controller;

use UCL\WebKeyPassBundle\Form\CreateUserForm;
use UCL\WebKeyPassBundle\Entity\User;

class CreateUserAction extends FormAddAction
{
    protected $fullname = 'Create Account';
    protected $success_msg = "Account created successfully. An e-mail has been sent to an administrator. You will receive an e-mail when the account is activated.";

    protected function renderTemplate ($data)
    {
        return $this->controller->render ('UCLWebKeyPassBundle::create_account_form.html.twig', $data);
    }

    protected function getForm ()
    {
        $shib = new Shibboleth ($this->controller);
        return new CreateUserForm ($shib->isAuthenticated ());
    }

    protected function getFormData ()
    {
        $shib = new Shibboleth ($this->controller);

        if ($shib->isAuthenticated ())
        {
            return array ('username' => $shib->getUsername (),
                          'password1' => '',
                          'password2' => '',
                          'first_name' => $shib->getFirstName (),
                          'last_name' => $shib->getLastName (),
                          'email' => $shib->getEmail ());
        }
        else
        {
            return array ('username' => '',
                          'password1' => '',
                          'password2' => '',
                          'first_name' => '',
                          'last_name' => '',
                          'email' => '',
                          'private_key' => '');
        }
    }

    private function sendMail ($user)
    {
        $user_repo = $this->controller->getUserRepo ();
        $admin_mails = array ();

        foreach ($user_repo->getAdmins () as $admin)
        {
            $admin_mails[] = $admin->getEmail ();
        }

        if (count ($admin_mails) == 0)
        {
            return;
        }

        $to = implode (', ', $admin_mails);

        $subject = "UCL WebKeyPass: new account: " . $user->getUsername ();

        $url = $this->controller->generateUrl ('ucl_wkp_root_view',
                                               array (),
                                               true);

        $msg = "Hello,\n\n"
             . $user->getFirstName () ." ". $user->getLastName () ." has created a new account.\n"
             . "You can activate the account in the administration zone of WebKeyPass:\n\n"
             . $url . "\n\n"
             . "His or her e-mail address: ". $user->getEmail () ."\n\n"
             . "Best regards,\n"
             . "The UCL WebKeyPass application.";

        mail ($to, $subject, $msg);
    }

    protected function saveData ($db_manager, $form)
    {
        $form_data = $form->getData ();
        $user = new User ();

        $user->setUsername ($form_data['username']);
        $this->setUserPassword ($user, $form_data['password1']);
        $user->setFirstName ($form_data['first_name']);
        $user->setLastName ($form_data['last_name']);
        $user->setEmail ($form_data['email']);
        $user->setIsActive (false);
        $user->setIsAdmin (false);

        $shib = new Shibboleth ($this->controller);
        if ($shib->isAuthenticated ())
        {
            $user->setPrivateKey ($shib->getPrivateKey ());
            $user->setWithShibboleth (true);

            $msg = 'Your private key is: ' . $shib->getPrivateKey ();

            $this->addFlashMessage ($msg);
        }
        else
        {
            $user->setPrivateKey ($form_data['private_key']);
            $user->setWithShibboleth (false);
        }

        $db_manager->persist ($user);

        $this->sendMail ($user);
    }

    protected function formIsValid ($form)
    {
        $ok = true;
        $form_data = $form->getData ();

        if (!$this->checkPasswords ($form,
                                    $form_data['password1'],
                                    $form_data['password2']))
        {
            $ok = false;
        }

        if (!$this->checkUsername ($form, $form_data['username']))
        {
            $ok = false;
        }

        return $ok;
    }
}
