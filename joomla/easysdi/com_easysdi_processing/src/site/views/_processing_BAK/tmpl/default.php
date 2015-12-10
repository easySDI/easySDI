<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_contact
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

jimport('joomla.html.html.bootstrap');

$doc = JFactory::getDocument();


//$doc->addStylesheet('//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css');

$user = JFactory::getUser();

$tab= JRequest::getVar('tab','order');
$action= JRequest::getVar('action','index');
$id= JRequest::getVar('id',null);

?>
<div class="row">
    <div class="span9"><?php

        if ($user->guest && $tab=='order')  {
            include_once(dirname(__FILE__).'/include/guest.php');
        } else {

            if (in_array($tab.'_'.$action, [
                'order_index','order_new','order_edit','order_show','order_input','order_output'
                /*,'processing_index'*/,'processing_show'
                ])) {
                include_once(dirname(__FILE__).'/include/'.$tab.'_'.$action.'.php');
        }

        var_dump($tab.'_'.$action.'.php');

    }

    ?></div>
    <div class="span3"><?php include_once(dirname(__FILE__).'/include/col.php'); ?></div>
</div>



<?php



