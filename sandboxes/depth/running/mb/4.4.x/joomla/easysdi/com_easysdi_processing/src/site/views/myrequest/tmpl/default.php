<?php
/**
* @version     4.4.5
* @package     com_easysdi_processing
* @copyright   Copyright (C) 2013-2017. All rights reserved.
* @license     GNU General Public License version 3 or later; see LICENSE.txt
* @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
*/


// no direct access
defined('_JEXEC') or die;

$user=sdiFactory::getSdiUser();
if(!$user->isEasySDI) {
    return JError::raiseWarning(403, JText::_('JERROR_ALERTNOAUTHOR'));
}

$order= $this->item;
$user_roles=Easysdi_processingHelper::getCurrentUserRolesOnData($order);

$doc = JFactory::getDocument();
$base_url=Juri::base(true) . '/components/com_easysdi_processing/assets';
$base_easysdiMap_url = Juri::base(true) . '/components/com_easysdi_core/libraries';

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
    $doc->addScript($base_easysdiMap_url . '/proj4js-1.1.0/lib/proj4js.js');
    $doc->addScript($base_easysdiMap_url . '/leaflet/libs/leaflet-proj4Leaflet/proj4-compressed.js');
    $doc->addScript($base_easysdiMap_url . '/leaflet/libs/leaflet-proj4Leaflet/proj4leaflet.js');
    $doc->addScript($base_easysdiMap_url . '/leaflet/libs/easysdi_leaflet/easysdi_leaflet.js');
}else{

    $doc->addStyleSheet($base_easysdiMap_url . '/leaflet/libs/leaflet/leaflet.css');
    $doc->addStyleSheet($base_easysdiMap_url . '/leaflet/libs/easySDI_leaflet.pack/main.css?v=' . sdiFactory::getSdiFullVersion());

    $doc->addScript($base_easysdiMap_url . '/leaflet/libs/leaflet/leaflet.js');
    $doc->addScript($base_easysdiMap_url . '/leaflet/libs/leaflet-proj4Leaflet/proj4-compressed.js');
    $doc->addScript($base_easysdiMap_url . '/leaflet/libs/leaflet-proj4Leaflet/proj4leaflet.js');


    $doc->addScript($base_easysdiMap_url . '/leaflet/libs/easySDI_leaflet.pack/easySDI_leaflet.pack.min.js?v=' . sdiFactory::getSdiFullVersion());
    $doc->addScript('https://maps.google.com/maps/api/js?v=3&sensor=false');
}

//****************
$doc->addScript($base_url . '/js/easysdi_processing.js?v=' . sdiFactory::getSdiFullVersion());

echo '<a href="' . JRoute::_('index.php?option=com_easysdi_processing&amp;view=myrequests') . '">' . JText::_('COM_EASYSDI_PROCESSING_FORM_LBL_BACK') . '</a>'; ?>

?>
<h1><?php echo $order->name ?> <?php echo Easysdi_processingStatusHelper::status($order->status) ?></h1>
<h2><?php echo JText::_('COM_EASYSDI_PROCESSING_TITLE'); ?>: <?php echo $order->processing_label ?></h2>

<ul class="nav nav-tabs">
    <li class='active'><a href="#order" data-toggle="tab">Commande</a></li>
    <?php if ($order->status=='active') { ?>
    <li><a href="#active" data-toggle="tab">En cours</a></li>
    <?php } ?>
    <?php if ( $order->status=='fail' ) { ?>
    <li><a href="#active" data-toggle="tab">Echec</a></li>
    <?php } ?>
    <?php if ($order->status=='done' || $order->status=='achived' ) { ?>
    <li><a href="#output" data-toggle="tab">Résultat</a></li>
    <?php } ?>
</ul>

<div class="tab-content">
  <div class="tab-pane active" id="order">
    commande passée le <?php echo $order->created ?>
    <?php if (count(array_intersect(['contact','obs','superuser'], $user_roles))>0) { ?>
    par <?php echo $order->user_label ?><br/>
    <?php } ?>
    <h3>données</h3>
    <?php
    if ($order->input_obj->type=='url') {
        ?>
        <pre><?php echo $order->input_obj->url;
           if ($order->input_obj->user!='') echo ' USER: '.$order->input_obj->user;
           if ($order->input_obj->password!='') echo ' PASSWORD: '.$order->input_obj->password; ?>
       </pre>
       <?php }
       if ($order->input_obj->type=='upload') {?>
       <ul class="unstyled">
           <?php foreach ($order->input_obj->files as $file) {
            ?>
            <li><?php echo Easysdi_processingParamsHelper::file_link($file, $order,'input'); ?></li>
            <?php
        }
        ?></ul>
        <?php } ?>

        <h3>paramètres</h3>
        <?php echo Easysdi_processingParamsHelper::table($order->processing_parameters,$order->parameters) ?>
        <?php
        if ('new' == $order->status && in_array('contact',$user_roles)) {
            ?>
            !TODO set active<br/>
            <?php
        }
        if ('new' == $order->status && in_array('creator',$user_roles)) {
            ?>
            !TODO set delete<br/>
            <?php
        }

        ?>
    </div>


    <?php if ($order->status=='active' ) { ?>
    <div class="tab-pane" id="active">
        <?php  echo $order->info ?> !TODO<br/>

        <?php
        if (in_array('contact',$user_roles)) {
            ?>
            !TODO publish result<br/>
            !TODO set fail<br/>
            <?php
        }
        ?>
    </div>
    <?php } ?>

    <?php if ($order->status=='fail' ) { ?>
    <div class="tab-pane" id="active">
        <div class="alert alert-danger">
            <?php  echo $order->info ?> !TODO<br/>
        </div>
    </div>
    <?php } ?>

    <?php if (in_array($order->status, ['done','achived'])) { ?>
    <div class="tab-pane" id="output">
        <h2>résultat</h2>

        <?php foreach ($order->output_obj as $file) {
            ?>
            <?php echo Easysdi_processingParamsHelper::file_link($file, $order); ?><br>
            <?php
        }
        ?>
        <hr>
        publié le <?php echo $order->sent ?> par <?php echo $order->contact_name ?>
    </div>
    <?php } ?>

</div>

<?php

    //var_dump($order);
    //var_dump($userRoles);
?>
<?php //include_once(dirname(__FILE__).'/../../footer.php'); ?>