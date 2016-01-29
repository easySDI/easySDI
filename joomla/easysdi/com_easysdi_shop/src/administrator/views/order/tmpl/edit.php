<?php
/**
 * @version		4.4.0
 * @package     com_easysdi_shop
 * @copyright	
 * @license		
 * @author		
 */
// no direct access
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
require_once JPATH_SITE . '/components/com_easysdi_shop/helpers/easysdi_shop.php';

$document = JFactory::getDocument();
JHtml::_('jquery.framework'); //ensure jquery is loaded
JHtml::script(Juri::root(true) . 'components/com_easysdi_shop/helpers/helper.js');
$base_url = Juri::root(true) . 'administrator/components/com_easysdi_core/libraries';
//TODO : do not include proj here !!
JHtml::script($base_url . '/proj4js-1.1.0/lib/proj4js-compressed.js');
JHtml::script($base_url . '/proj4js-1.1.0/lib/defs/EPSG2056.js');
JHtml::script($base_url . '/proj4js-1.1.0/lib/defs/EPSG21781.js');
JHtml::script($base_url . '/proj4js-1.1.0/lib/projCode/somerc.js');
JHtml::script($base_url . '/proj4js-1.1.0/lib/projCode/merc.js');
JHtml::script($base_url . '/proj4js-1.1.0/lib/projCode/lcc.js');
JHtml::script($base_url . '/filesaver/FileSaver.js');
$document->addStyleSheet(Juri::root(true) . '/components/com_easysdi_shop/views/basket/tmpl/basket.css');
Easysdi_shopHelper::addMapShopConfigToDoc();
?>
<script type="text/javascript">
    Joomla.submitbutton = function (task)
    {
        if (task == 'order.cancel')
            location.href = './index.php?option=com_easysdi_shop&view=orders';
    }
</script>

<?php
$item = $this->item;

$orderLayout = new JLayoutFile('com_easysdi_shop.order', null, array('debug' => false, 'client' => 1, 'component' => 'com_easysdi_shop'));
Easysdi_shopHelper::extractionsBySupplierGrouping($this->item->basket);
Easysdi_shopHelper::basketReloadSavedPricing($this->item->basket);
?>
<h1><?php echo JText::_('COM_EASYSDI_SHOP_ORDER_TITLE'); ?> <span id="sdi-order-title-id"><?php echo $item->id; ?></span></h1>
<div class="order-edit back-end-edit">

    <?php
    //load layout, set data and view type
    echo $orderLayout->render(array(
        'item' => $this->item,
        'form' => $this->form,
        'viewType' => Easysdi_shopHelper::ORDERVIEW_ADMIN
    ));
    ?>

    <?php foreach ($this->form->getFieldset('hidden') as $field): ?>
        <?php echo $field->input; ?>
    <?php endforeach; ?> 
    <input type = "hidden" name = "task" value = "" />
    <input type = "hidden" name = "id" value = "<?php echo $this->item->id; ?>" />
    <input type = "hidden" name = "option" value = "com_easysdi_shop" />
    <input type = "hidden" name = "sdiUserId" value = "<?php echo $this->user->id; ?>" />            
    <?php echo JHtml::_('form.token'); ?>

    <script>
        Ext.onReady(function () {
            window.appname.on("ready", function () {
                loadPerimeter(false);
            })
        })
    </script>


</div>