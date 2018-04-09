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

require_once JPATH_SITE . '/components/com_easysdi_shop/helpers/easysdi_shop.php';

$document = JFactory::getDocument();
$document->addScript(Juri::root(true) . '/components/com_easysdi_shop/helpers/helper.js?v=' . sdiFactory::getSdiFullVersion());
$base_url = Juri::base(true) . '/components/com_easysdi_core/libraries';
//TODO : do not include proj here !!
$document->addScript($base_url . '/proj4js-1.1.0/lib/proj4js-compressed.js');
$document->addScript($base_url . '/proj4js-1.1.0/lib/defs/EPSG2056.js');
$document->addScript($base_url . '/proj4js-1.1.0/lib/defs/EPSG21781.js');
$document->addScript($base_url . '/proj4js-1.1.0/lib/projCode/somerc.js');
$document->addScript($base_url . '/proj4js-1.1.0/lib/projCode/merc.js');
$document->addScript($base_url . '/proj4js-1.1.0/lib/projCode/lcc.js');
$document->addScript($base_url . '/filesaver/FileSaver.js');
$document->addStyleSheet(Juri::base(true) . '/components/com_easysdi_shop/views/basket/tmpl/basket.css?v=' . sdiFactory::getSdiFullVersion());
Easysdi_shopHelper::addMapShopConfigToDoc();
JText::script('COM_EASYSDI_SHOP_REQUEST_REJECT_MODAL_MESSAGE_PRICE');
JText::script('COM_EASYSDI_SHOP_REQUEST_REJECT_MODAL_MESSAGE_FILE_OR_REMARK');
?>
<?php
if ($this->item) :
    $orderLayout = new JLayoutFile('com_easysdi_shop.order', null, array('debug' => false, 'client' => 1, 'component' => 'com_easysdi_shop'));
    Easysdi_shopHelper::extractionsBySupplierGrouping($this->item->basket);
    Easysdi_shopHelper::basketReloadSavedPricing($this->item->basket);
    ?>

    <h1><?php echo JText::_('COM_EASYSDI_SHOP_REQUEST_TITLE'); ?> <span id="sdi-order-title-id"><?php echo $this->item->id; ?></span></h1>

    <div class="order-edit front-end-edit">
        <form class="form-inline form-validate" action="<?php echo JRoute::_('index.php?option=com_easysdi_shop&view=request'); ?>" method="post" id="adminForm" name="adminForm" enctype="multipart/form-data">


            <?php
            //load layout, set data and view type
            echo $orderLayout->render(array(
                'item' => $this->item,
                'form' => $this->form,
                'viewType' => Easysdi_shopHelper::ORDERVIEW_REQUEST,
                'authorizeddiffusion' => $this->authorizeddiffusion,
                'managedOrganismsDiffusion' => $this->managedOrganismsDiffusion
            ));
            ?>



            <div>
                <?php echo $this->getToolbar(); ?>
            </div>

            <?php foreach ($this->form->getFieldset('hidden') as $field): ?>
                <?php echo $field->input; ?>
            <?php endforeach; ?>  
            <input type="hidden" name="task" value="" />
            <input type="hidden" name="option" value="com_easysdi_shop" />
            <input type="hidden" name="jform[current_product]" id="jform_current_product" value="" />
            <?php echo JHtml::_('form.token'); ?>

            <!-- Reject modal -->
            <div class="modal fade" id="rejectModal" tabindex="-1" role="dialog" aria-labelledby="rejectModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title" id="rejectModalLabel"><?php echo JText::_('COM_EASYSDI_SHOP_REQUEST_REJECT_MODAL_TITLE'); ?></h4>
                        </div>
                        <div id="rejectModalBody" class="modal-body">
                            <?php echo JText::_('COM_EASYSDI_SHOP_REQUEST_REJECT_MODAL_TEXT'); ?><br/>
                            <span class="text-error"><?php echo JText::_('COM_EASYSDI_SHOP_REQUEST_REJECT_MODAL_TEXT_WARNING'); ?></span><br/>
                            <br/>
                            <?php echo JText::_('COM_EASYSDI_SHOP_REQUEST_REJECT_MODAL_TEXT_REASON'); ?><br/>
                            <textarea id="rejectionremark" name="jform[rejectionremark]" placeholder="<?php echo JText::_('COM_EASYSDI_SHOP_REQUEST_REJECT_MODAL_TEXT_REASON_PLACEHOLDER'); ?>" ></textarea>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn" data-dismiss="modal"><?php echo JText::_('COM_EASYSDI_SHOP_REQUEST_REJECT_MODAL_CANCEL'); ?></button>
                            <button id="rejectByProviderButton" type="button" class="btn btn-danger" onclick="Joomla.submitbutton('request.rejectproduct');"><?php echo JText::_('COM_EASYSDI_SHOP_REQUEST_REJECT_MODAL_CONFIRM'); ?></button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- post product error modal -->
            <div class="modal fade" id="productErrorModal" tabindex="-1" role="dialog" aria-labelledby="productErrorModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title" id="productErrorModalLabel"><?php echo JText::_('COM_EASYSDI_SHOP_REQUEST_ERROR_MODAL_TITLE'); ?></h4>
                        </div>
                        <div id="productErrorModalBody" class="modal-body">
                            asdfasdf
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" data-dismiss="modal"><?php echo JText::_('COM_EASYSDI_SHOP_REQUEST_ERROR_MODAL_OK'); ?></button>
                        </div>
                    </div>
                </div>
            </div>            


        </form>
    </div>

    <script>
        var isPricingActivated = <?php echo JComponentHelper::getParams('com_easysdi_shop')->get('is_activated') ? 'true' : 'false'; ?>;
        var orderType = '<?php echo $this->item->ordertype; ?>';
        jQuery(document).ready(function () {
            jQuery('#rejectByProviderButton').prop('disabled', true);
            jQuery('textarea#rejectionremark').on('input propertychange', function () {
                jQuery('#rejectByProviderButton').prop('disabled', !(this.value.length > 20));
            });
        });

        function checkAndSendProduct(productId) {
            enableCurrentProduct(productId);
            if (checkProductElements(productId)) {
                Joomla.submitbutton('request.saveproduct');
            } else {
                jQuery('#productErrorModal').modal();
            }
            return false;
        }

        function enableCurrentProduct(productId) {
            jQuery('#jform_current_product').val(productId);
        }

        function checkProductElements(productId) {
            var hasFile = jQuery('#file_' + productId).val().length > 0;
            var hasRemark = jQuery('#remark_' + productId).val().length > 0;
            var hasFee = jQuery('#fee_' + productId).val().length > 0;

            jQuery('#productErrorModalBody').empty();
            if (orderType == 'order') {
                var hasErrors = false;
                if (!hasFile && !hasRemark) {
                    jQuery('#productErrorModalBody').append('<p>' + Joomla.JText._('COM_EASYSDI_SHOP_REQUEST_REJECT_MODAL_MESSAGE_FILE_OR_REMARK') + '</p>');
                    hasErrors = true;
                }
                if (isPricingActivated && !hasFee) {
                    jQuery('#productErrorModalBody').append('<p>' + Joomla.JText._('COM_EASYSDI_SHOP_REQUEST_REJECT_MODAL_MESSAGE_PRICE') + '</p>');
                    hasErrors = true;
                }
                return !hasErrors;
            } else if (orderType == 'estimate') {
                if (!hasFee) {
                    jQuery('#productErrorModalBody').append('<p>' + Joomla.JText._('COM_EASYSDI_SHOP_REQUEST_REJECT_MODAL_MESSAGE_PRICE') + '</p>');
                    return false;
                } else {
                    return true;
                }
            }
            return false;
        }

        Ext.onReady(function () {
            window.appname.on("ready", function () {
                loadPerimeter(false);
            });
        });




    </script>
    <?php
else:
    echo JText::_('COM_EASYSDI_SHOP_ITEM_NOT_LOADED');
endif;
?>
