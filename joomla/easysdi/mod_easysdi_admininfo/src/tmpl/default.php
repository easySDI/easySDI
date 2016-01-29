<?php
/**
 * @version		4.4.0
 * @package     mod_easysdi_admininfo
 * @copyright	
 * @license		
 * @author		
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