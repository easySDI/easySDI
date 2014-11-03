<?php
/**
 * @version     4.0.0
 * @package     mod_easysdi_adminbutton
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org§> - http://www.easysdi.org
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