<?php
/**
*** @version     4.0.0
* @package     com_easysdi_contact
 * @copyright   Copyright (C) 2013-2015. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */


// no direct access
defined('_JEXEC') or die;
$order= $this->item;
$user_roles=Easysdi_processingHelper::getCurrentUserRolesOnData($order);

$doc = JFactory::getDocument();
$base_url=Juri::base(true) . '/components/com_easysdi_processing/assets';
$base_easysdiMap_url = Juri::base(true) . '/administrator/components/com_easysdi_core/libraries';

/*$doc->addStyleSheet($base_easysdiMap_url . '/leaflet/libs/leaflet/leaflet.css');
$doc->addStyleSheet($base_easysdiMap_url . '/leaflet/libs/easySDI_leaflet.pack/main.css');

$doc->addScript(Juri::base(true) . '/media/jui/js/jquery.min.js');
$doc->addScript(Juri::base(true) . '/media/jui/js/jquery-noconflict.js');
$doc->addScript($base_easysdiMap_url . '/leaflet/libs/leaflet/leaflet.js');
//$doc->addScript($base_easysdiMap_url . '/leaflet/libs/easySDI_leaflet.pack/easySDI_leaflet.pack.min.js');
$doc->addScript($base_easysdiMap_url . '/leaflet/libs/easysdi_leaflet/easysdi_leaflet.js');
$doc->addScript('https://maps.google.com/maps/api/js?v=3&sensor=false');
*/

JHtml::_('jquery.framework');
if (JDEBUG) {
    $doc->addStyleSheet($base_easysdiMap_url . '/leaflet/libs/leaflet/leaflet.css');
    $doc->addStyleSheet($base_easysdiMap_url . '/leaflet/libs/Leaflet.ZoomBox/L.Control.ZoomBox.css');
    $doc->addStyleSheet($base_easysdiMap_url . '/leaflet/libs/leaflet-measure/leaflet-measure.css');
    $doc->addStyleSheet($base_easysdiMap_url . '/leaflet/libs/leaflet-control-geocoder/Control.Geocoder.css');
    $doc->addStyleSheet($base_easysdiMap_url . '/leaflet/libs/sidebar-v2/css/leaflet-sidebar.css');
    $doc->addStyleSheet($base_easysdiMap_url . '/leaflet/libs/leaflet-EasyPrint/L.Control.EasyPrint.css');
    $doc->addStyleSheet($base_easysdiMap_url . '/leaflet/libs/leaflet-EasyLayer/easyLayer.css');
    $doc->addStyleSheet($base_easysdiMap_url . '/leaflet/libs/leaflet-EasyAddLayer/easyAddLayer.css');
    $doc->addStyleSheet($base_easysdiMap_url . '/leaflet/libs/leaflet-EasyLegend/easyLegend.css');
    $doc->addStyleSheet($base_easysdiMap_url . '/leaflet/libs/leaflet-EasyGetFeature/easyGetFeature.css');
    $doc->addStyleSheet($base_easysdiMap_url . '/leaflet/libs/leaflet-Easy/easyLeaflet.css');
    $doc->addStyleSheet($base_easysdiMap_url . '/leaflet/libs/font-awesome-4.3.0/css/font-awesome.css');

    $doc->addScript($base_easysdiMap_url . '/leaflet/libs/i18next-1.9.0/i18next-1.9.0.min.js');
    $doc->addScript('https://maps.google.com/maps/api/js?v=3&sensor=false');
    $doc->addScript($base_easysdiMap_url . '/leaflet/libs/leaflet/leaflet.js');
    $doc->addScript($base_easysdiMap_url . '/leaflet/libs/shramov/tile/Google.js');
    $doc->addScript($base_easysdiMap_url . '/leaflet/libs/shramov/tile/Bing.js');
    $doc->addScript($base_easysdiMap_url . '/leaflet/libs/leaflet.TileLayer.WMTS-master/leaflet-tilelayer-wmts-src.js');
    $doc->addScript($base_easysdiMap_url . '/leaflet/libs/Leaflet.ZoomBox/L.Control.ZoomBox.js');
    $doc->addScript($base_easysdiMap_url . '/leaflet/libs/leaflet-measure/leaflet-measure.js');
    $doc->addScript($base_easysdiMap_url . '/leaflet/libs/leaflet-control-geocoder/Control.Geocoder.js');
    $doc->addScript($base_easysdiMap_url . '/leaflet/libs/leaflet-EasyPrint/L.Control.EasyPrint.js');
    $doc->addScript($base_easysdiMap_url . '/leaflet/libs/sidebar-v2/js/leaflet-sidebar.js');
    $doc->addScript($base_easysdiMap_url . '/leaflet/libs/leaflet-EasyLayer/easyLayer.js');
    $doc->addScript($base_easysdiMap_url . '/leaflet/libs/leaflet-EasyAddLayer/easyAddLayer.js');
    $doc->addScript($base_easysdiMap_url . '/leaflet/libs/leaflet-EasyLegend/easyLegend.js');
    $doc->addScript($base_easysdiMap_url . '/leaflet/libs/leaflet-EasyGetFeature/easyGetFeature.js');
    $doc->addScript($base_easysdiMap_url . '/leaflet/libs/wms-capabilities/wms-capabilities.min.js');
    $doc->addScript($base_easysdiMap_url . '/proj4js-1.1.0/lib/proj4js-compressed.js');
    $doc->addScript($base_easysdiMap_url . '/leaflet/libs/leaflet-proj4Leaflet/proj4-compressed.js');
    $doc->addScript($base_easysdiMap_url . '/leaflet/libs/leaflet-proj4Leaflet/proj4leaflet.js');
}else{
    $doc->addStyleSheet($base_easysdiMap_url . '/leaflet/libs/leaflet/leaflet.css');
    $doc->addStyleSheet($base_easysdiMap_url . '/leaflet/libs/easySDI_leaflet.pack/main.css');

    $doc->addScript($base_easysdiMap_url . '/leaflet/libs/leaflet/leaflet.js');
    $doc->addScript($base_easysdiMap_url . '/leaflet/libs/leaflet-proj4Leaflet/proj4-compressed.js');
    $doc->addScript($base_easysdiMap_url . '/leaflet/libs/leaflet-proj4Leaflet/proj4leaflet.js');


    $doc->addScript($base_easysdiMap_url . '/leaflet/libs/easySDI_leaflet.pack/easySDI_leaflet.pack.min.js');
    $doc->addScript('https://maps.google.com/maps/api/js?v=3&sensor=false');
}

//****************
$doc->addScript($base_url . '/js/easysdi_processing.js');

$dispatcher = JDispatcher::getInstance();
$plugin_results = $dispatcher->trigger( 'onRenderProcessingOrderItem' ,array($order));
?>
<?php //include_once(dirname(__FILE__).'/../../header.php');?>
<div  data-processingplugin=<?php echo $order->plugins ?> class="<?php
    foreach ($plugin_results as $k=>$plugin_result) {
        if (isset($plugin_result['plugin'])) echo ' plugin_'.$plugin_result['plugin'];
    }
    ?>">

    <h1><?php echo $order->name ?>
        &nbsp;<?php echo Easysdi_processingStatusHelper::status($order->status) ?>
        <?php
        foreach ($plugin_results as $k=>$plugin_result) {
            if (isset($plugin_result['status'])) echo ' <span class="'.$plugin_result['plugin'].'_'.$order->id.'_status">'.$plugin_result['status'].'</span>';
        }
        ?>
    </h1>
    <h2><?php echo JText::_('COM_EASYSDI_PROCESSING_TITLE'); ?>: <?php echo $order->processing_label ?></h2>

    <ul class="nav nav-tabs">
        <li class='active'><a href="#order" data-toggle="tab"><?php echo JText::_('COM_EASYSDI_PROCESSING_LBL_ORDER'); ?></a></li>
        <?php if ($order->status=='active') { ?>
        <li><a href="#active" data-toggle="tab"></a></li>
        <?php } ?>
        <?php if ($order->status=='done' || $order->status=='achived' || $order->status=='fail' ) { ?>
        <li><a href="#output" data-toggle="tab"><?php echo JText::_('COM_EASYSDI_PROCESSING_LBL_RESULTS'); ?></a></li>
        <?php } ?>
        <?php
        foreach ($plugin_results as $k=>$plugin_result) {
            if (isset($plugin_result['tabtitle'])) {
                ?>
                <li><a href="#<?php echo $plugin_result['plugin']; ?>" data-toggle="tab"><?php echo $plugin_result['tabtitle']; ?></a></li>
                <?php
            }
        }
        ?>
    </ul>

    <div class="tab-content">
      <div class="tab-pane active" id="order">
        <?php echo JText::_('COM_EASYSDI_PROCESSING_LBL_ORDER_DATE'); ?> <?php echo $order->created ?>
        <?php if (count(array_intersect(['contact','obs','superuser'], $user_roles))>0) { ?>
        <?php echo JText::_('COM_EASYSDI_PROCESSING_LBL_ORDER_BY'); ?> <?php echo $order->user_label ?><br/>
        <?php } ?>
        <h3><?php echo JText::_('COM_EASYSDI_PROCESSING_LBL_ORDER_DATA'); ?></h3>
        <ul class="unstyled">
            <li><?php echo Easysdi_processingParamsHelper::file_link($file, $order,'input'); ?></li>
        </ul>
        <h3><?php echo JText::_('COM_EASYSDI_PROCESSING_LBL_ORDER_PARAMS'); ?></h3>
        <?php echo Easysdi_processingParamsHelper::table($order->processing_parameters,$order->parameters,$order) ?>
    </div>



    <?php //if (in_array($order->status, ['done','achived'])) { ?>
    <div class="tab-pane" id="output">

        <?php if ($order->output != '') {
            ?>
            <?php echo Easysdi_processingParamsHelper::file_link($order->output, $order,'output'); ?><br>
            <?php
        }

        if ($order->outputpreview != '') {
            ?>
            <?php echo Easysdi_processingParamsHelper::file_link($order->outputpreview, $order, 'outputpreview'); ?><br>
            <?php
        }
        if ($order->info != '') {
            ?>
            <?php echo $order->info; ?><br>
            <?php
        }
        ?>
        <?php
        foreach ($plugin_results as $k=>$plugin_result) {
            if (isset($plugin_result['output'])) echo ' <div class="'.$plugin_result['plugin'].'_'.$order->id.'_output">'.$plugin_result['output'].'</div>';
        }
        ?>

        <hr>
        publié le <?php echo $order->sent ?> par <?php echo $order->processing_contact_label ?>
    </div>
    <?php //} ?>


    <?php
    foreach ($plugin_results as $k=>$plugin_result) {
        if (isset($plugin_result['tabcontent'])) {
            ?>
            <div class="tab-pane" id="<?php echo $plugin_result['plugin']; ?>">
                <?php echo $plugin_result['tabcontent']; ?>
            </div>
            <?php
        }
    }
    ?>

</div>

</div>
