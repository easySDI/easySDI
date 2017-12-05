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
$order= $this->item;



//var_dump($order->access_key);


$user=sdiFactory::getSdiUser();
$jinput = JFactory::getApplication()->input;
$input_key=$jinput->get('private_key',false);
if(!$user->isEasySDI) {
    if ($input_key==false || $input_key!=$order->access_key)
        return JError::raiseWarning(403, JText::_('JERROR_ALERTNOAUTHOR'));
} else {
    $input_share=$jinput->get('share',false);
    if ($input_share==='true' && $order->access_key===null) {
        //share

        function rand_md5($length) {
          $max = ceil($length / 32);
          $random = '';
          for ($i = 0; $i < $max; $i ++) {
            $random .= md5(microtime(true).mt_rand(10000,90000));
        }
        return substr($random, 0, $length);
    }

    $order->access_key=rand_md5(16);
    $db = JFactory::getDbo();
    $query = "
    UPDATE `#__sdi_processing_order`
    SET `access_key` = '".$order->access_key."'
    WHERE `id` = '".$order->id."';
    ";
    $db->setQuery($query);
    $db->query();
    echo '<div class="alert alert-info"><strong>Le document est maintenant partagé</strong><br>'.
    "<p>Transmettez ce lien à vos partenaires pour qu'ils puissent consulter le document</p>".
    '<a target=_blank href="'. JURI::base().'index.php?option=com_easysdi_processing&amp;view=myorder&amp;id='.$order->id.'&amp;private_key='.$order->access_key.'">'. JURI::base().'index.php?option=com_easysdi_processing&amp;view=myorder&amp;id='.$order->id.'&amp;private_key='.$order->access_key.'</a>'.
    '</div>';

}
if ($input_share==='false' && $order->access_key!==null) {
    $order->access_key=null;
    $db = JFactory::getDbo();
    $query = "
    UPDATE `#__sdi_processing_order`
    SET `access_key` = NULL
    WHERE `id` = '".$order->id."';
    ";
    $db->setQuery($query);
    $db->query();
    echo '<div class="alert alert-info">Le document n\'est plus partagé</div>';
}
}

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
    $doc->addStyleSheet($base_easysdiMap_url . '/leaflet/libs/leaflet-graphicscale/Leaflet.GraphicScale.min.css');

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
    $doc->addScript($base_easysdiMap_url . '/leaflet/libs/leaflet-graphicscale/Leaflet.GraphicScale.min.js');
    $doc->addScript($base_easysdiMap_url . '/leaflet/libs/wms-capabilities/wms-capabilities.min.js');
    $doc->addScript($base_easysdiMap_url . '/proj4js-1.1.0/lib/proj4js-compressed.js');
    $doc->addScript($base_easysdiMap_url . '/leaflet/libs/leaflet-proj4Leaflet/proj4-compressed.js');
    $doc->addScript($base_easysdiMap_url . '/leaflet/libs/leaflet-proj4Leaflet/proj4leaflet.js');
    $doc->addScript($base_easysdiMap_url . '/leaflet/libs/easysdi_leaflet/easysdi_leaflet.js?v=' . sdiFactory::getSdiFullVersion());
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

$dispatcher = JDispatcher::getInstance();
$plugin_results = $dispatcher->trigger( 'onRenderProcessingOrderItem' ,array($order));

if (!$input_key)
    echo '<a href="' . JRoute::_('index.php?option=com_easysdi_processing&amp;view=myorders') . '">' . JText::_('COM_EASYSDI_PROCESSING_FORM_LBL_BACK') . '</a>'; ?>


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
    <?php

    if ($user->isEasySDI) {

        ?>
        <div class="text-right">
            <?php
            if ($order->access_key!=false) {
                ?>

                <a href="#processing_hotlink" role="button" class="btn btn-warning" data-toggle="modal"><?php echo JText::_('COM_EASYSDI_PROCESSING_SHARED_ORDER'); ?></a>
                <!-- Modal -->
                <div id="processing_hotlink" class="modal hide fade text-left" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                  <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h3 id="myModalLabel"><?php echo JText::_('COM_EASYSDI_PROCESSING_YOU_SHARED_ORDER'); ?></h3>
                </div>
                <div class="modal-body">
                    <p><?php echo JText::_('COM_EASYSDI_PROCESSING_SHARE_LINK'); ?> <strong><?php echo $order->name ?></strong></p>
                    <a target=_blank href="<?php echo JURI::base().'index.php?option=com_easysdi_processing&amp;view=myorder&amp;id='.$order->id.'&amp;private_key='.$order->access_key; ?>"><?php echo JURI::base().'index.php?option=com_easysdi_processing&amp;view=myorder&amp;id='.$order->id.'&amp;private_key='.$order->access_key; ?></a>
                </div>
                <div class="modal-footer">
                    <button class="btn" data-dismiss="modal" aria-hidden="true">Fermer</button>
                    <a href='<?php echo JRoute::_('index.php?option=com_easysdi_processing&amp;view=myorder&amp;id='.$order->id.'&amp;share=false'); ?>' class="btn btn-primary"  onclick="return confirm('<?php echo JText::_('COM_EASYSDI_PROCESSING_WARNING_SHARE_DELETE'); ?>')"><?php echo JText::_('COM_EASYSDI_PROCESSING_SHARE_DELETE'); ?></a>
                </div>
            </div>

            <?php
        } else {
            ?>

            <a href="<?php echo JRoute::_('index.php?option=com_easysdi_processing&amp;view=myorder&amp;id='.$order->id.'&amp;share=true'); ?>" class="btn btn-default"><?php echo JText::_('COM_EASYSDI_PROCESSING_SHARE_ORDER'); ?></a>

            <?php
        }
        ?>
    </div>
    <?php
}
?>

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
    <?php if (count(array_intersect(array('contact','obs','superuser'), $user_roles))>0) { ?>
    <?php echo JText::_('COM_EASYSDI_PROCESSING_LBL_ORDER_BY'); ?> <?php echo $order->user_label ?><br/>
    <?php } ?>
    <h3><?php echo JText::_('COM_EASYSDI_PROCESSING_LBL_ORDER_DATA'); ?></h3>
    <ul class="unstyled">
        <li><?php echo Easysdi_processingParamsHelper::file_link($file, $order,'input'); ?></li>
    </ul>
    <h3><?php echo JText::_('COM_EASYSDI_PROCESSING_LBL_ORDER_PARAMS'); ?></h3>
    <?php echo Easysdi_processingParamsHelper::table($order->processing_parameters,$order->parameters,$order) ?>
    <?php
    foreach ($plugin_results as $k=>$plugin_result) {
        if (isset($plugin_result['parent_txt'])&&$plugin_result['parent_txt']) echo $plugin_result['parent_txt'];
    }
    ?>
</div>



<?php //if (in_array($order->status, ['done','achived'])) { ?>
<div class="tab-pane" id="output">

    <?php if ($order->output != '') {
        ?>
        <?php echo JText::_('COM_EASYSDI_PROCESSING_LBL_DOWNLOAD_OUTPUT') .' : '. Easysdi_processingParamsHelper::file_link($order->output, $order,'output'); ?><br>
        <?php
    }

    if ($order->outputpreview != '') {
        ?>
        <?php echo JText::_('COM_EASYSDI_PROCESSING_LBL_DOWNLOAD_OUTPUTPREVIEW') .' : '.Easysdi_processingParamsHelper::file_link($order->outputpreview, $order, 'outputpreview'); ?><br>
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
    <?php echo JText::_('COM_EASYSDI_PROCESSING_ORDER_PUBLISHED_AT')." ".$order->sent." ".JText::_('COM_EASYSDI_PROCESSING_LBL_ORDER_BY') . " ". $order->processing_contact_label ?>
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
