<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  mod_logged
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

JHtml::_('bootstrap.tooltip');
$document = JFactory::getDocument();
$document->addStyleSheet(JURI::base() . 'modules/mod_easysdi_admininfo/assets/css/modeasysdiadmininfo.css');
?>
<div class="row-striped easysdiadmininfo">
    <?php
    //Loop through the results to get the information + text
    foreach ($infos as $return):
        ?>
        <div class="row-fluid">
            <div class="span12">
                <?php
                echo '<span class="easysdiadmininfovalue">' . $return['info'] . '</span> ';
                echo $return['text'];
                ?>
            </div>
        </div>
        <?php
    endforeach;
    ?>


</div>