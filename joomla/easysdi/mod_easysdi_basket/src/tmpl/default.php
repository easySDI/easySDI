<?php
/**
 * @version     4.4.5
 * @package     mod_easysdi_basket
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */

// no direct access
defined('_JEXEC') or die('Restricted access');
JText::script('COM_EASYSDI_SHOP_BASKET_SUCCESSFULLY_UPDATED');
$document = JFactory::getDocument();
$document->addStyleSheet(JURI::root(true) . '/components/com_easysdi_core/assets/css/easysdi_loader.css?v=' . sdiFactory::getSdiFullVersion());
?>
<script>
    var request;

    function updateBasketContent(JSONtext) {
        if (JSONtext == null) {
            return;
        }

        var JSONobject = JSON.parse(JSONtext, function(key, value) {
            if (key && typeof key === 'string' && key == 'ERROR') {
                jQuery('#modal-confirm-body-text').text(value);
                jQuery('#modal-confirm').modal('show');
            }

            if (key && typeof key === 'string' && key == 'DIALOG') {
                jQuery('#modal-dialog-body-text').text(value);
                jQuery('#modal-dialog').modal('show');
            }

            if (key && typeof key === 'string' && key == 'COUNT') {
                jQuery('#sdi-basket-content-display').text(value);
                jQuery('#modal-confirm-body-text').text(Joomla.JText._('COM_EASYSDI_SHOP_BASKET_SUCCESSFULLY_UPDATED'));
                jQuery('#modal-confirm').modal('show');
            }

        });
    }
   
    function actionAdd(action) {
        jQuery.ajax({
            url: "index.php?option=com_easysdi_shop&task=" + action,
            success: function(data){
                updateBasketContent(data);
            }
        });
    }
</script>

<div id="sdi-basket-content" class="sdi-basket-content">
    <a href="<?php echo JRoute::_('index.php?option=com_easysdi_shop&view=basket', false); ?>">
        <div class="row">
            <div class="span1 offset2" id="sdi-basket-content-display"><?php echo $basketcontent; ?></div>
            <div class="span4"><?php echo JText::_("COM_EASYSDI_SHOP_BASKET_ITEMS"); ?></div>
        </div>
    </a>
</div>

<div id="modal-confirm" class="modal hide fade" style="z-index: 1000000" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel"><?php echo JText::_("COM_EASYSDI_SHOP_BASKET_ITEM_SUCCESSFULLY_ADDED_HEADER") ?></h3>
    </div>
    <div class="modal-body">
        <p><div id="modal-confirm-body-text"></div></p>
    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true"><?php echo JText::_("COM_EASYSDI_SHOP_BASKET_MODAL_BTN_CLOSE") ?></button>
    </div>
</div>

<div id="modal-dialog" class="modal hide fade" style="z-index: 1000000" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel"><?php echo JText::_("COM_EASYSDI_SHOP_BASKET_DIALOG_HEADER") ?></h3>
    </div>
    <div class="modal-body">
        <p><div id="modal-dialog-body-text"></div></p>
    </div>
    <div class="modal-footer">
        <button onClick="actionAdd('abortAdd');" class="btn" data-dismiss="modal" aria-hidden="true"><?php echo JText::_("COM_EASYSDI_SHOP_BASKET_MODAL_BTN_CANCEL") ?></button>
        <button onClick="actionAdd('confirmAdd');" class="btn btn-primary" data-dismiss="modal" aria-hidden="true"><?php echo JText::_("COM_EASYSDI_SHOP_BASKET_MODAL_BTN_ADD") ?></button>
    </div>
</div>

<div class="modal hide" id="modal-wait" data-backdrop="static" data-keyboard="false">
    <div class="modal-header">

    </div>
    <div class="modal-body">
        <div class="progress progress-striped active">
            <div class="bar" style="width: 100%;"></div>
        </div>
    </div>
</div>