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

class RemoveIconAction extends Action
{
    private function checkIcon ($icon)
    {
        return preg_match ("/^[a-zA-Z-_]+$/", $icon) == 1;
    }

    public function perform ($icon, $success_msg)
    {
        if ($this->checkIcon ($icon))
        {
            $file = __DIR__ . '/../Resources/public/icons/' . $icon . '.png';
            unlink ($file);
        }

        $this->addFlashMessage ($success_msg);

        return $this->controller->redirect ($this->redirect_url);
    }
}
