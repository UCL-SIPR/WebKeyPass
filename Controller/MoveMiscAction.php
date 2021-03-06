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

use UCL\WebKeyPassBundle\Form\MoveMiscForm;

class MoveMiscAction extends FormAction
{
    protected $fullname = 'Move misc item';
    protected $success_msg = 'Misc item moved successfully.';

    protected function getForm ()
    {
        $form = new MoveMiscForm ();
        $form->setNodeRepository ($this->controller->getNodeRepo ());
        $form->setNodeToMove ($this->node);

        return $form;
    }
}
