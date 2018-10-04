<?php

/**
 * @version     4.3.2
 * @package     com_easysdi_catalog
 * @copyright   Copyright (C) 2013-2015. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
// No direct access
defined('_JEXEC') or die;

/**
 * Routing class from com_easysdi_catalog
 * @since  3.3
 */
class Easysdi_catalogRouter extends JComponentRouterBase {

    /**
     * Build the route for the com_easysdi_catalog component
     *
     * @param   array  &$query  An array of URL arguments
     * @return  array  The URL arguments to use to assemble the subsequent URL.
     * @since   3.3
     */
    public function build(&$query) {
        $segments = array();

        if (isset($query['view'])) {
            $view = $query['view'];
            $segments[] = $view;
            if ($query['view'] == 'sheet') {
                unset($query['view']);
            }
        }

        if (isset($query['task'])) {
            $segments[] = implode('/', explode('.', $query['task']));
            unset($query['task']);
        }

        if (isset($query['id'])) {
            $segments[] = $query['id'];
            unset($query['id']);
        }

        if ($view == 'sheet') {
            if (isset($query['code'])) {
                $segments[] = $query['code'];
                unset($query['code']);
            }
            if (isset($query['resourcetype'])) {
                $segments[] = $query['resourcetype'];
                unset($query['resourcetype']);
            }
            if (isset($query['lang'])) {
                $segments[] = $query['lang'];
                unset($query['lang']);
            }
            if (isset($query['guid'])) {
                $segments[] = $query['guid'];
                unset($query['guid']);
            }
            if (isset($query['catalog'])) {
                $segments[] = $query['catalog'];
                unset($query['catalog']);
            }
            if (isset($query['type'])) {
                $segments[] = $query['type'];
                unset($query['type']);
            }
            if (isset($query['preview'])) {
                $segments[] = $query['preview'];
                unset($query['preview']);
            }
        }

        return $segments;
    }

    /**
     * Parse the segments of a URL.
     *
     * @param   array  &$segments  The segments of the URL to parse.
     * @return  array  The URL attributes to be used by the application.
     * @since   3.3
     */
    public function parse(&$segments) {
        $vars = array();

        // view is always the first element of the array
        $count = count($segments);

        switch ($segments[0]) {
            case 'sheet':
                //View
                $vars['view'] = array_shift($segments);
                //Guid or Code
                $segment = array_shift($segments);
                if (is_numeric($segment)) {//id
                    $vars['id'] = $segment;
                    $segment = array_shift($segments);
                } elseif (preg_match_all("~\{?[a-z0-9]{8}-[a-z0-9]{4}-[a-z0-9]{4}-[a-z0-9]{4}-[a-z0-9]{12}\}?~", $segment)) {//Guid
                    $vars['guid'] = $segment;
                    $segment = array_shift($segments);
                } elseif (strlen($segment) == 36) {//Guid
                    $vars['guid'] = $segment;
                    $segment = array_shift($segments);
                } elseif (preg_match_all('~[a-z]{2}-[A-Z]{2}~', $segment)) {
                    $vars['lang'] = $segment;
                    $segment = array_shift($segments);
                } else {//Code
                    $vars['code'] = $segment;
                    $segment = array_shift($segments);
                    $vars['resourcetype'] = $segment;
                    $segment = array_shift($segments);
                }
                //Lang                
                if (preg_match_all('~[a-z]{2}-[A-Z]{2}~', $segment)) {
                    $vars['lang'] = $segment;
                    $segment = array_shift($segments);
                }
                //Guid
                if (preg_match_all("~\{?[a-z0-9]{8}-[a-z0-9]{4}-[a-z0-9]{4}-[a-z0-9]{4}-[a-z0-9]{12}\}?~", $segment)) {//Guid
                    $vars['guid'] = $segment;
                    $segment = array_shift($segments);
                }
                //Catalog
                if (!in_array($segment, array('complete', 'result', 'editor', 'public', 'map', 'search_list'))) {
                    $vars['catalog'] = $segment;
                    $segment = array_shift($segments);
                }
                //Type
                if (in_array($segment, array('complete', 'result'))) {
                    $vars['type'] = $segment;
                    $segment = array_shift($segments);
                }
                //Preview
                if (in_array($segment, array('editor', 'public', 'map', 'search_list'))) {
                    $vars['preview'] = $segment;
                    $segment = array_shift($segments);
                }

                break;
            default:

                if ($count) {
                    $count--;
                    $segment = array_pop($segments);
                    if (is_numeric($segment)) {
                        $vars['id'] = $segment;
                    } else {
                        $count--;
                        $vars['task'] = array_pop($segments) . '.' . $segment;
                    }
                }

                if ($count) {
                    $vars['task'] = implode('.', $segments);
                }
                break;
        }

        return $vars;
    }

}

/**
 * @param	array	A named array
 * @return	array
 */
function Easysdi_catalogBuildRoute(&$query) {
    $router = new Easysdi_catalogRouter;
    return $router->build($query);
}

/**
 * @param	array	A named array
 * @param	array
 *
 * Formats:
 *
 * index.php?/easysdi_catalog/task/id/Itemid
 *
 * index.php?/easysdi_catalog/id/Itemid
 */
function Easysdi_catalogParseRoute($segments) {
    $router = new Easysdi_catalogRouter;
    return $router->parse($segments);
}
