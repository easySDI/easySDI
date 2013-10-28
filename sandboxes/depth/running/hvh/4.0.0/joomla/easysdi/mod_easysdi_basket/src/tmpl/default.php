<?php
// no direct access
defined('_JEXEC') or die('Restricted access');
JText::script('COM_EASYSDI_SHOP_BASKET_SUCCESSFULLY_UPDATED');
$document = JFactory::getDocument();
$document->addStyleSheet(JURI::root().'/administrator/components/com_easysdi_core/assets/css/easysdi_loader.css');

?>
<script>
    var request;
    
    jQuery(document).ready(function() {
        jQuery('#content').prepend("<div id='sdi-loader' style='display : none'><img id='sdi-loader-image'  src='<?php echo JURI::root(); ?>administrator/components/com_easysdi_core/assets/images/loader.gif' alt=''></div>");
    });
    
    
    
    function updateBasketContent() {
        if (request.readyState == 4) {
            jQuery('#sdi-loader').hide();
            
            var JSONtext = request.responseText;

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
    }
    function initRequest (){
        jQuery('#sdi-loader').show();
        request = false;
        if (window.XMLHttpRequest) {
            request = new XMLHttpRequest();
        } else if (window.ActiveXObject) {
            try {
                request = new ActiveXObject("Msxml2.XMLHTTP");
            } catch (e) {
                try {
                    request = new ActiveXObject("Microsoft.XMLHTTP");
                } catch (e) {
                    request = false;
                }
            }
        }
        if (!request)
            return;
    }
    function actionAdd(action){
        initRequest();

        var query = "index.php?option=com_easysdi_shop&task="+action;
        request.onreadystatechange = updateBasketContent;
        request.open("GET", query, true);
        request.send(null);
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

<div id="modal-confirm" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
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

<div id="modal-dialog" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
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