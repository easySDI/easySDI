<?php
/**
 * @version     4.4.5
 * @package     com_easysdi_shop
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
// no direct access
defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');

require_once JPATH_SITE . '/components/com_easysdi_shop/helpers/easysdi_shop.php';

$document = JFactory::getDocument();
$document->addScript(Juri::root(true) . '/components/com_easysdi_shop/helpers/helper.js?v=' . sdiFactory::getSdiFullVersion());
$base_url = Juri::root(true) . '/components/com_easysdi_core/libraries';
//TODO : do not include proj here !!
$document->addScript($base_url . '/proj4js-1.1.0/lib/proj4js-compressed.js');
$document->addScript($base_url . '/proj4js-1.1.0/lib/defs/EPSG2056.js');
$document->addScript($base_url . '/proj4js-1.1.0/lib/defs/EPSG21781.js');
$document->addScript($base_url . '/proj4js-1.1.0/lib/projCode/somerc.js');
$document->addScript($base_url . '/proj4js-1.1.0/lib/projCode/merc.js');
$document->addScript($base_url . '/proj4js-1.1.0/lib/projCode/lcc.js');
$document->addScript($base_url . '/filesaver/FileSaver.js');
$document->addStyleSheet(Juri::root(true) . '/components/com_easysdi_shop/views/basket/tmpl/basket.css?v=' . sdiFactory::getSdiFullVersion());
Easysdi_shopHelper::addMapShopConfigToDoc();
?>
<?php
if ($this->item) :
    $orderLayout = new JLayoutFile('com_easysdi_shop.order', null, array('debug' => false, 'client' => 1, 'component' => 'com_easysdi_shop'));
    Easysdi_shopHelper::extractionsBySupplierGrouping($this->item->basket);
    Easysdi_shopHelper::basketReloadSavedPricing($this->item->basket);
    ?> 

    <h1><?php echo JText::_('COM_EASYSDI_SHOP_ORDER_TITLE'); ?> <span id="sdi-order-title-id"><?php echo $this->item->id; ?></span></h1>

    <div class="order-edit front-end-edit">
        <form class="form-inline form-validate" action="<?php echo JRoute::_('index.php?option=com_easysdi_shop&view=order'); ?>" method="post" id="adminForm" name="adminForm" enctype="multipart/form-data">


            <?php
            //load layout, set data and view type
            echo $orderLayout->render(array(
                'item' => $this->item,
                'form' => $this->form,
                'viewType' => $this->state->get('layout.validation') ? Easysdi_shopHelper::ORDERVIEW_VALIDATION : Easysdi_shopHelper::ORDERVIEW_ORDER
            ));
            ?>

            <div>

                <?php if ($this->state->get('layout.validation') && $this->item->orderstate_id == 8): ?>

                    <p id="validation_remark">
                        <label for="reason"><?php echo JText::_('COM_EASYSDI_SHOP_ORDER_VALIDATION_REMARK'); ?>:</label>
                        <textarea id="reason" name="reason" placeholder="<?php echo JText::_('COM_EASYSDI_SHOP_ORDER_VALIDATION_REMARK_PLACEHOLDER'); ?>" <?php if (!$this->isValidationManager): ?>disabled="disabled"<?php endif; ?>></textarea>
                    </p>
                    <div id="termsofuse-container">
                        <label class="checkbox">
                            <input type="checkbox" id="termsofuse" > <?php echo JText::_('COM_EASYSDI_SHOP_ORDER_VALIDATION_I_ACCEPT') ?> <a href="<?php echo $this->paramsarray['termsofuse']; ?>" target="_blank"><?php echo JText::_('COM_EASYSDI_SHOP_ORDER_VALIDATION_TERMS') ?></a> <?php echo JText::_('COM_EASYSDI_SHOP_ORDER_VALIDATION_OF_USE') ?>
                        </label>
                    </div>
                    <?php
                endif;
                echo $this->getToolbar();
                ?>
            </div>

            <?php foreach ($this->form->getFieldset('hidden') as $field): ?>
                <?php echo $field->input; ?>
            <?php endforeach; ?> 
            <input type = "hidden" name = "task" value = "" />
            <input type = "hidden" name = "id" value = "<?php echo $this->item->id; ?>" />
            <input type = "hidden" name = "option" value = "com_easysdi_shop" />
            <input type = "hidden" name = "sdiUserId" value = "<?php echo $this->user->id; ?>" />            
            <?php echo JHtml::_('form.token'); ?>

        </form>
    </div>

    <?php echo Easysdi_shopHelper::getAddToBasketModal(); ?>
    <script>
        Ext.onReady(function () {
            window.appname.on("ready", function () {
                loadPerimeter(false);
            })
        })
    </script>
    
    
    <?php if ($this->state->get('layout.validation') && $this->item->orderstate_id == 8): ?>
        <script type="text/javascript">

            var updateValidationButtonsState = function () {
                //must accept terms of use to validate
                jQuery('div#toolbar-apply>button').prop('disabled', !jQuery('#termsofuse').prop('checked'));
                //must accept terms of use AND have a 20 char remark to reject
                jQuery('div#toolbar-delete>button').prop('disabled', !(jQuery('textarea#reason').val().length >= 20 && jQuery('#termsofuse').prop('checked')));
            };

            jQuery(document).ready(function () {
                jQuery('div#toolbar-delete>button').prop('disabled', true);
                jQuery('textarea#reason').on('input propertychange', updateValidationButtonsState);
                jQuery('#termsofuse').change(updateValidationButtonsState);
                updateValidationButtonsState();
            });

        </script>
    <?php endif; ?>
        
    <?php
else:
    echo JText::_('COM_EASYSDI_SHOP_ITEM_NOT_LOADED');
endif;

//If OTP option is enabled, load the OTP Modal
if($this->params->get('otpactivated', 0) == 1)
{
    echo Easysdi_shopHelper::downloadOTPProductModal(); 
}
?>
    
