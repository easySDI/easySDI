<?php
/**
 * @version     4.3.2
 * @package     com_easysdi_shop
 * @copyright   Copyright (C) 2013-2015. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
// no direct access
defined('_JEXEC') or die;

require_once JPATH_SITE . '/components/com_easysdi_shop/helpers/easysdi_shop.php';

$document = JFactory::getDocument();
$document->addScript('components/com_easysdi_shop/helpers/helper.js');
$base_url = Juri::base(true) . '/administrator/components/com_easysdi_core/libraries';
//TODO : do not include proj here !!
$document->addScript($base_url . '/proj4js-1.1.0/lib/proj4js-compressed.js');
$document->addScript($base_url . '/proj4js-1.1.0/lib/defs/EPSG2056.js');
$document->addScript($base_url . '/proj4js-1.1.0/lib/defs/EPSG21781.js');
$document->addScript($base_url . '/proj4js-1.1.0/lib/projCode/somerc.js');
$document->addScript($base_url . '/proj4js-1.1.0/lib/projCode/merc.js');
$document->addScript($base_url . '/proj4js-1.1.0/lib/projCode/lcc.js');
$document->addStyleSheet(Juri::base(true) . '/components/com_easysdi_shop/views/basket/tmpl/basket.css');
Easysdi_shopHelper::addMapShopConfigToDoc();
?>
<?php
if ($this->item) :
    $orderLayout = new JLayoutFile('com_easysdi_shop.order', null, array('debug' => false, 'client' => 1, 'component' => 'com_easysdi_shop'));
    Easysdi_shopHelper::extractionsBySupplierGrouping($this->item->basket);
    Easysdi_shopHelper::basketReloadSavedPricing($this->item->basket);
    ?>

    <h1><?php echo JText::_('COM_EASYSDI_SHOP_ORDER_TITLE'); ?></h1>

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
            <input type = "hidden" name = "task" value = "" />
            <input type = "hidden" name = "option" value = "com_easysdi_shop" />
            <?php echo JHtml::_('form.token'); ?>
        </form>
    </div>

    <script>
        Ext.onReady(function () {
            window.appname.on("ready", function () {
                loadPerimeter(false);
            })
        })
    </script>
    <?php
else:
    echo JText::_('COM_EASYSDI_SHOP_ITEM_NOT_LOADED');
endif;
?>
