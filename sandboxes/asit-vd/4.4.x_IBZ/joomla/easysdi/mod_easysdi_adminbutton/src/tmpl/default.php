<?php
/**
 * @version     4.4.5
 * @package     mod_easysdi_adminbutton
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
defined('_JEXEC') or die;

JHtml::_('bootstrap.tooltip');
$document = JFactory::getDocument();
$document->addStyleSheet(JURI::base() . 'modules/mod_easysdi_adminbutton/assets/css/modeasysdiadminbutton.css?v=' . sdiFactory::getSdiFullVersion());
?>
<div class="row-fluid">
    <div class="span12 easysdiadminbutton">
        <?php
        //Loop through the results to get the information + text
        foreach ($usersButton as $return):
            //print_r($return);
            ?>
            <a class="btn <?php echo(isset($return['btntooltip'])?'hasTooltip':'');?> " href="<?php echo $return['link']; ?>" <?php echo(isset($return['btntooltip'])?'data-original-title="'.$return['btntooltip'].'"':'');?>>
                <?php 
                //Condition to know if we have to put a badge on the button
                if ($return['info'] != 0){
                ?>
                <span class="<?php echo(isset($return['badgetooltip'])?'hasTooltip':'');?> badge badge-<?php echo $return['state']; ?> pull-right" <?php echo(isset($return['badgetooltip'])?'data-original-title="'.$return['badgetooltip'].'"':'');?>><?php echo $return['info']; ?></span><div class="text"><?php echo $return['text']; ?></div>
                <?php 
                }else {?>
                <div class="text"><?php echo $return['text']; ?></div>
                <?php }
                ?>
            </a>
        <?php endforeach; ?>
    </div>
</div>