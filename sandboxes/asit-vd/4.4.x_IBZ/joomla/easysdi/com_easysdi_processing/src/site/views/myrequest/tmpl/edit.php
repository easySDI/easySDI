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

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('behavior.keepalive');

// Import JS
$doc = JFactory::getDocument();

$order= $this->item;
$user_roles=Easysdi_processingHelper::getCurrentUserRolesOnData($order);

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

$dispatcher = JDispatcher::getInstance();
$plugin_results = $dispatcher->trigger( 'onRenderProcessingOrderItem' ,array($order));


$doc->addScript('components/com_easysdi_core/libraries/easysdi/view/view.js?v=' . sdiFactory::getSdiFullVersion());
$app = JFactory::getApplication();
$processing=Easysdi_processingHelper::getProcessById($app->input->get('processing', '', 'INT'));
$processing_parameters=json_decode($processing->parameters);
?>
<script>

    jQuery(function(){

        jQuery("select[data-toggleif]").each(function(){
            var obj=jQuery(this);

            var change=function(){
                var target=obj.data('toggleif');
                target=jQuery(target);
                var selected=obj.find(':selected').val();
                target.each(function(){
                    if (jQuery(this).hasClass('if_'+selected)) {
                        jQuery(this).show();
                    } else {
                        jQuery(this).hide();
                    }
                })
            };
            change();
            obj.change(change);
        });

    })


</script>

<?php echo '<a href="' . JRoute::_('index.php?option=com_easysdi_processing&amp;view=myrequests') . '">' . JText::_('COM_EASYSDI_PROCESSING_FORM_LBL_BACK') . '</a>'; ?>

<div  data-processingplugin=<?php echo $order->plugins ?> class="<?php
    foreach ($plugin_results as $k=>$plugin_result) {
        if (isset($plugin_result['plugin'])) echo ' plugin_'.$plugin_result['plugin'];
    }
    ?>">
<form action="<?php echo JRoute::_('index.php?option=com_easysdi_processing&view=myrequest&task=myrequest.save'); ?>" method="post" enctype="multipart/form-data" name="adminForm" id="processing-form" class="form-horizontal">
    <h1><?php echo $order->name ?>
        &nbsp;<?php echo Easysdi_processingStatusHelper::status($order->status) ?>
        <?php
        foreach ($plugin_results as $k=>$plugin_result) {
            if (isset($plugin_result['status'])) echo ' '.$plugin_result['status'];
        }
        ?>
    </h1>
    <h2><?php echo JText::_('COM_EASYSDI_PROCESSING_TITLE'); ?>: <?php echo $order->processing_label ?></h2>

    <ul class="nav nav-tabs">
        <li class='active'><a href="#order" data-toggle="tab"><?php echo JText::_('COM_EASYSDI_PROCESSING_LBL_ORDER'); ?></a></li>
        <li ><a href="#details" data-toggle="tab"><?php echo JText::_('COM_EASYSDI_PROCESSING_LBL_RESULTS'); ?></a></li>
        <!--<?php
        foreach ($plugin_results as $k=>$plugin_result) {
            if (isset($plugin_result['tabtitle'])) {
                ?>
                <li><a href="#<?php echo $plugin_result['plugin']; ?>" data-toggle="tab"><?php echo $plugin_result['tabtitle']; ?></a></li>
                <?php
            }
        }
        ?>-->
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
            <?php echo Easysdi_processingParamsHelper::table($order->processing_parameters,$order->parameters) ?>
            <?php
        foreach ($plugin_results as $k=>$plugin_result) {
            if (isset($plugin_result['parent_txt'])&&$plugin_result['parent_txt']) echo $plugin_result['parent_txt'];
    }
    ?>
        </div>

        <div class="tab-pane" id="details">
            <fieldset id ="fieldset_download">
                <div id="div_download">
                    <?php foreach ($this->form->getFieldset('details') as $field): ?>
                        <div class="control-group" id="<?php echo $field->fieldname; ?>">
                            <div class="control-label"><?php echo $field->label; ?></div>
                            <div class="controls">
                                <?php echo $field->input; ?>
                                <?php if($field->fieldname == 'output'):?>
                                    <?php if(!empty($this->item->output)):?>
                                        <?php echo Easysdi_processingParamsHelper::file_link($order->output, $order,'output'); ?>
                                    <?php endif;?>
                                    <input type="hidden" name="jform[output]" id="jform_output_hidden" value="<?php echo $this->item->output ?>" />
                                <?php endif;?>
                                <?php if($field->fieldname == 'outputpreview'):?>
                                    <?php if(!empty($this->item->outputpreview)):?>
                                        <?php echo Easysdi_processingParamsHelper::file_link($order->outputpreview, $order,'outputpreview'); ?>
                                    <?php endif;?>
                                    <input type="hidden" name="jform[outputpreview]" id="jform_outputpreview_hidden" value="<?php echo $this->item->outputpreview ?>" />
                                <?php endif;?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </fieldset>
                    <!--<?php foreach ($this->form->getFieldset('details') as $field): ?>
                        <div class="control-group" id="<?php echo $field->fieldname; ?>">
                            <div class="control-label"><?php echo $field->label; ?></div>
                            <div class="controls"><?php echo $field->input; ?></div>
                        </div>
                    <?php endforeach; ?>-->
            <?php echo $this->getToolbar(); ?>
                </div>
        <?php
        foreach ($plugin_results as $k=>$plugin_result) {
            if (isset($plugin_result['tabcontent'])) {
                ?>
                <div class="tab-pane" id="<?php echo $plugin_result['plugin']; ?>">
                    <?php if ($plugin_result['progression']) { ?>
                    <div class="progression"><?php echo $plugin_result['progression']; ?></div>
                    <?php } ?>
                    <?php echo $plugin_result['tabcontent']; ?>
                </div>
                <?php
            }
        }
        ?>


    </div>
</div>
        <?php foreach ($this->form->getFieldset('hidden') as $field): ?>
            <?php echo $field->input; ?>
        <?php endforeach; ?>

        <?php echo JHtml::_('form.token'); ?>
</form>
