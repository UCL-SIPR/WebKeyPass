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

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class NodeController extends MainController
{
    protected $node;

    public function getAuthRepo ()
    {
        return $this->getDoctrine ()->getRepository ('UCLWebKeyPassBundle:Authentication');
    }

    protected function getEmptyNodeInfos ()
    {
        return array (array ('title' => 'No information',
                             'content' => ''));
    }

    protected function getNodeInfos ($node)
    {
        $infos = array ();

        $infos[] = array ('title' => 'Hostname',
                          'content' => htmlspecialchars ($node->getHostname ()));

        $comment = $node->getComment ();
        $this->encode_comment ($comment);

        $infos[] = array ('title' => 'Comment',
                          'content' => $comment);

        return $infos;
    }

    private function getRemoveLoginUrl ($auth)
    {
        $request = $this->container->get ('request');
        $route = $request->get ('_route');
        $route_data = $request->get ('_route_params');
        $url = $this->generateUrl ($route, $route_data);
        $url .= '/remove_login_' . $auth->getId ();

        return $url;
    }

    private function getEditLoginUrl ($auth)
    {
        $request = $this->container->get ('request');
        $route = $request->get ('_route');
        $route_data = $request->get ('_route_params');
        $url = $this->generateUrl ($route, $route_data);
        $url .= '/edit_login_' . $auth->getId ();

        return $url;
    }

    protected function getAuthentications ($node)
    {
        $master_key = new MasterKey ($this);
        $user = $this->getAuthenticatedUser ();

        $auths = array ();

        foreach ($node->getAuthentications () as $auth)
        {
            $iv = $auth->getMcryptIv ();
            $decrypted_password = $master_key->decryptPassword ($auth->getPassword (),
                                                                $user,
                                                                $iv);

            $auths[] = array ('login' => $auth->getLogin (),
                              'password' => $decrypted_password,
                              'remove_url' => $this->getRemoveLoginUrl ($auth),
                              'edit_url' => $this->getEditLoginUrl ($auth));
        }

        return $auths;
    }

    protected function getActions ($node_id)
    {
        return array ();
    }

    protected function checkType ($node)
    {
        return true;
    }

    protected function getNodeTypes ()
    {
        $icons = new Icons ();
        return $icons->getIcons ();
    }

    private function getOpenNodes ()
    {
        $open_nodes = array ();

        if (!$this->node->isLeaf ())
        {
            $open_nodes[] = $this->node->getId ();
        }

        for ($parent = $this->node->getParent (); $parent != null; $parent = $parent->getParent ())
        {
            $open_nodes[] = $parent->getId ();
        }

        return $open_nodes;
    }

    public function getCommonData ()
    {
        $data = array ();
        $data['title'] = $this->node->getName ();
        $data['path'] = $this->getPath ($this->node);
        $data['open_nodes'] = $this->getOpenNodes ();
        $data['node_id'] = $this->node->getId ();

        $node_repo = $this->getNodeRepo ();
        $data['nodes'] = $node_repo->getNodes ();

        $data['search_form'] = $this->getSearchForm ()->createView ();

        $data['node_types'] = $this->getNodeTypes ();

        $settings = new Settings ();
        $data['session_expiration_timeout'] = $settings->getSessionExpirationTimeout ();

        return $data;
    }

    protected function getNodeFromId ($node_id)
    {
        $node_repo = $this->getNodeRepo ();
        $node = $node_repo->find ($node_id);

        if (!$node)
        {
            throw $this->createNotFoundException ('Node id '.$node_id.' not found');
        }

        if (!$this->checkType ($node))
        {
            throw $this->createNotFoundException ('Wrong node type');
        }

        return $node;
    }

    protected function getAuthFromId ($auth_id)
    {
        $auth_repo = $this->getAuthRepo ();
        $auth = $auth_repo->find ($auth_id);

        if (!$auth)
        {
            throw $this->createNotFoundException ('Login/password with id '.$auth_id.' not found');
        }

        return $auth;
    }

    public function viewAction ($node_id)
    {
        $this->node = $this->getNodeFromId ($node_id);

        $data = $this->getCommonData ();
        $data['infos'] = $this->getNodeInfos ($this->node);
        $data['authentications'] = $this->getAuthentications ($this->node);
        $data['actions'] = $this->getActions ($node_id);

        return $this->render ('UCLWebKeyPassBundle::node.html.twig', $data);
    }

    public function removeLoginAction ($node_id, $auth_id)
    {
        $node = $this->getNodeFromId ($node_id);
        $node_type = $node->getTypeStr ();

        $auth = $this->getAuthFromId ($auth_id);

        $action = new RemoveAction ($this, $auth);
        $action->setRedirectRoute ('ucl_wkp_'.$node_type.'_view', array ('node_id' => $node_id));

        $success_msg = 'Login/password removed successfully.';
        return $action->perform ($success_msg);
    }

    /**
     * Find web URLs and replace them with HTML tags
     * the remaining text converted with htmlspecialchars().
     *
     * Source: http://blog.amappa.com/2011/07/matching-urls-and-transforming-them-into-links-with-php/
     **/
    protected function encode_comment(&$text)
    {
      // The Regular Expression filter
      $reg_exUrl = "/(?i)\b(".
        "(?:".
          "https?:\/\/|www\d{0,3}[.]|".
          "[a-z0-9.\-]+[.][a-z]{2,4}\/".
        ")".
        "(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+".
        "(?:".
          "\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|".
          "[^\s`!()\[\]{};:'\".,<>?«»“”‘’]".
        ")".
      ")/";

      /**
       * sort by decreasing length
       * @param string $a
       * @param string $b
       * @return int
       */

      // Check if there are urls in the text
      preg_match_all($reg_exUrl, $text, $urls);

      if (empty($urls)) {
        $text = htmlspecialchars($text);
      } else {
        // sort to start with longer string
        usort($urls[0],
              function ($a, $b) {
                if (strlen($a) == strlen($b)) {
                    return 0;
                }
                return (strlen($a) > strlen($b)) ? -1 : 1;
              });

        // and put a placeholder
        foreach ($urls[0] as $k => $url) {
          $text = str_replace(
            $url,
            $this->placeholder($k),
            $text
          );
        }
        // convert here before final replacement
        $text = htmlspecialchars($text);
        // avoided nested replacements, now replace safely
        foreach ($urls[0] as $k => $url) {
          // if url starts with www
          if (substr($url, 0, 4) == 'www.') {
            // href better if with http before it
            $href = 'http://'.$url;
          } else {
            $href = $url;
            // if url starts with http
            if (substr($url, 0, 7) == 'http://') {
              // don't show it to keep text cleaner
              $url = substr($url, 7);
            }
          }
          $text = str_replace(
            $this->placeholder($k),
            '<a href="'.$href.'">'.$url.'</a>',
            $text
          );
        }
      }

      $text = nl2br ($text);
    }

    /**
     * temporary placeholder
     * @param int $k
     * @return string
     */
    private static function placeholder($k) {
        return '{]('.$k.'}[)';
    }
}
