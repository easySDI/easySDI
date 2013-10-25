<?php
/**
 * @version     4.0.0
 * @package     com_easysdi_catalog
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org§> - http://www.easysdi.org
 */
// no direct access
defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');

//Load admin language file
$lang = JFactory::getLanguage();
$lang->load('com_easysdi_catalog', JPATH_ADMINISTRATOR);
$document = JFactory::getDocument();

$document->addStyleSheet('administrator/components/com_easysdi_core/libraries/ext/resources/css/ext-all.css');
$document->addScript('administrator/components/com_easysdi_core/libraries/easysdi/catalog/bootbox.min.js');
$document->addScript('administrator/components/com_easysdi_core/libraries/openlayers/OpenLayers.debug.js');
$document->addScript('administrator/components/com_easysdi_core/libraries/proj4js-1.4.1/dist/proj4.js');
$document->addScript('administrator/components/com_easysdi_core/libraries/ext/adapter/ext/ext-base-debug.js');
$document->addScript('administrator/components/com_easysdi_core/libraries/ext/ext-all-debug.js');
$document->addScript('administrator/components/com_easysdi_core/libraries/ext/ext-all-debug.js');
$document->addScript('administrator/components/com_easysdi_core/libraries/gemetclient-2.0.0/src/thesaur.js');
$document->addScript('administrator/components/com_easysdi_core/libraries/gemetclient-2.0.0/src/HS.js');
$document->addScript('administrator/components/com_easysdi_core/libraries/gemetclient-2.0.0/src/translations.js');

$document->addScript('http://maps.google.com/maps/api/js?v=3&amp;sensor=false');
?>

<style>

    .action-1{
        font-size: 15px;
    }
    .legend-1{
        font-size: 16px;
    }

    .action-2, .action-3{
        font-size: 13px;
    }
    .legend-2, .legend-3{
        font-size: 14px;
    }

    .inner-fds{
        padding-left:15px;
        border-left: 1px solid #BDBDBD;
    }

    .collapse-btn, .neutral-btn{
        margin-right: 10px;
    }

    .add-btn, .empty-btn, .preview-btn{
        margin-left: 10px;
    }

    legend{
        font-size: 12px;
    }

    img.olTileImage{
        max-width: none;
    }

    svg {
        max-width :none !important;
    }


</style>

<script type="text/javascript">
    js = jQuery.noConflict();
    js('document').ready(function() {

        

<?php
foreach ($this->validators as $validator) {

    echo $validator;
}
?>

        js('#btn_toogle_all').click(function() {
            var btn = js(this);
            if (btn.attr('action') == 'open') {
                btn.text('Tout fermer');
                js('.inner-fds').show();
                js('.collapse-btn').attr({'src': '/joomla/administrator/components/com_easysdi_catalog/assets/images/collapse_top.png'});
                btn.attr({'action': 'close'});
            } else {
                btn.text('Tout ouvrir');
                js('.inner-fds').hide();
                js('.collapse-btn').attr({'src': '/joomla/administrator/components/com_easysdi_catalog/assets/images/expand.png'});
                btn.attr({'action': 'open'});
            }


        });
    });
    function collapse(id) {

        var uuid = getUuid('collapse-btn-', id);
        var current_div = js('#inner-fds-' + uuid);
        var current_btn = js('#' + id);
        current_div.toggle('fast', function() {
            if (current_div.css('display') == 'none') {
                current_btn.attr({'src': '/joomla/administrator/components/com_easysdi_catalog/assets/images/expand.png'});
            } else {
                current_btn.attr({'src': '/joomla/administrator/components/com_easysdi_catalog/assets/images/collapse_top.png'});
            }
        });
    }

    function addField(id, idwi, relid, parent_path, lowerbound, upperbound) {
        js.get('<?php echo $_SERVER['PHP_SELF']; ?>' + '/?view=ajax&parent_path=' + parent_path + '&relid=' + relid, function(data) {

            js('#attribute-group-' + idwi + ':last').after(data);
            if (js(data).find('select') !== null) {
                chosenRefresh();
            }

            js(data).find('button').each(function() {
                idbtn = js(this).attr('id');
                Calendar.setup({
                    inputField: idbtn.replace('_img', ''),
                    ifFormat: "%Y-%m-%d",
                    button: idbtn,
                    align: "Tl",
                    singleClick: true,
                    firstDay: 1
                });
            });
        });
    }
    
    function addToStructure(relid, parent_path){
        js.get('<?php echo $_SERVER['PHP_SELF']; ?>' + '/?view=ajax&parent_path=' + parent_path + '&relid=' + relid);
    }

    function addFieldset(id, idwi, relid, parent_path, lowerbound, upperbound) {
        var uuid = getUuid('add-btn-', id);
        js.get('<?php echo $_SERVER['PHP_SELF']; ?>' + '/?view=ajax&parent_path=' + parent_path + '&relid=' + relid, function(data) {
            js('#bottom-' + idwi).before(data);
            if (js(data).find('select') !== null) {
                chosenRefresh();
            }

            js(data).find('button').each(function() {
                idbtn = js(this).attr('id');
                Calendar.setup({
                    inputField: idbtn.replace('_img', ''),
                    ifFormat: "%Y-%m-%d",
                    button: idbtn,
                    align: "Tl",
                    singleClick: true,
                    firstDay: 1
                });
            });
            var occurance = getOccuranceCount('.outer-fds-' + idwi);
            if (upperbound > occurance) {
                js('.add-btn-' + idwi).show();
            }

            if (occurance > lowerbound) {
                js('.remove-btn-' + idwi).show();
            }

            if (upperbound == occurance) {
                js('.add-btn-' + idwi).hide();
            }
        });
    }

    function confirmFieldset(id, idwi, lowerbound, upperbound) {
        bootbox.confirm("Are you sure?", function(result) {
            if (result) {
                removeFieldset(id, idwi, lowerbound, upperbound);
            }
        });
    }

    function confirmField(id, idwi, lowerbound, upperbound) {
        bootbox.confirm("Are you sure?", function(result) {
            if (result) {
                removeField(id, idwi, lowerbound, upperbound);
            }
        });
    }
    
    function removeFromStructure(id){
        var uuid = getUuid('remove-btn-', id);
        js.get('<?php echo $_SERVER['PHP_SELF']; ?>' + '/?task=ajax.removeNode&uuid=' + uuid, function(data) {
            var response = js.parseJSON(data);
            return response.success;
        });
    }

    function removeField(id, idwi, lowerbound, upperbound) {
        var uuid = getUuid('remove-btn-', id);
        js.get('<?php echo $_SERVER['PHP_SELF']; ?>' + '/?task=ajax.removeNode&uuid=' + uuid, function(data) {
            var response = js.parseJSON(data);
            if (response.success) {
                var toRemove = js('#attribute-group-' + uuid);
                toRemove.remove();
            }
        });
    }

    function removeFieldset(id, idwi, lowerbound, upperbound) {
        var uuid = getUuid('remove-btn-', id);
        js.get('<?php echo $_SERVER['PHP_SELF']; ?>' + '/?task=ajax.removeNode&uuid=' + uuid, function(data) {
            var response = js.parseJSON(data);
            if (response.success) {

                var toRemove = js('#outer-fds-' + uuid);
                toRemove.remove();
                var occurance = getOccuranceCount('.outer-fds-' + idwi);
                if (lowerbound == occurance) {
                    js('.remove-btn-' + idwi).hide();
                }

                if (upperbound > occurance) {
                    js('.add-btn-' + idwi).show();
                }
            }
        });
    }

    function confirmEmptyFile(id) {
        bootbox.confirm("Are you sure?", function(result) {
            if (result) {
                emptyFile(id);
            }
        });
    }

    function emptyFile(id) {
        var uuid = getUuid('empty-btn-', id);
        var replaceUuid = uuid.replace(/-/g, '_');
        js('#jform_' + replaceUuid + '_filetext').attr('value', '');
        js('#preview-' + uuid).hide();
        js('#empty-file-' + uuid).hide();
    }

    /**
     * 
     * @param {string} prefix
     * @param {string} string
     * @returns {array}
     */
    function getUuid(prefix, string) {
        string = string.replace(prefix, '');
        return string;
    }

    function getOccuranceCount(className) {
        var nbr = js(className).length;
        return nbr;
    }

    function chosenRefresh() {
        js('select').chosen({
            disable_search_threshold: 10,
            allow_single_deselect: true
        });
    }

    function filterBoundary(parentPath, value) {
        js.get('<?php echo $_SERVER['PHP_SELF']; ?>' + '/?task=ajax.getBoundaryByCategory&value=' + value, function(data) {
            var response = js.parseJSON(data);
            var replaceId = parentPath.replace(/-/g, '_');
            var selectList = js('#jform_' + replaceId + '_sla_gmd_dp_description_sla_gco_dp_CharacterString');
            selectList.empty();
            var items = "";
            js.each(response, function() {
                items += "<option value=\"" + this.option_value + "\">" + this.option_value + "</option>";
            });
            selectList.html(items);
            selectList.trigger("liszt:updated");
            //selectList.change();

            js('#jform_' + replaceId + '_sla_gmd_dp_geographicElement_la_1_ra__sla_gmd_dp_EX_GeographicBoundingBox_sla_gmd_dp_northBoundLatitude_sla_gco_dp_Decimal').attr('value', response['0'].northbound);
            js('#jform_' + replaceId + '_sla_gmd_dp_geographicElement_la_1_ra__sla_gmd_dp_EX_GeographicBoundingBox_sla_gmd_dp_southBoundLatitude_sla_gco_dp_Decimal').attr('value', response['0'].southbound);
            js('#jform_' + replaceId + '_sla_gmd_dp_geographicElement_la_1_ra__sla_gmd_dp_EX_GeographicBoundingBox_sla_gmd_dp_eastBoundLongitude_sla_gco_dp_Decimal').attr('value', response['0'].eastbound);
            js('#jform_' + replaceId + '_sla_gmd_dp_geographicElement_la_1_ra__sla_gmd_dp_EX_GeographicBoundingBox_sla_gmd_dp_westBoundLongitude_sla_gco_dp_Decimal').attr('value', response['0'].westbound);

            var map_parent_path = replaceId + '_sla_gmd_dp_geographicElement_la_1_ra__sla_gmd_dp_EX_GeographicBoundingBox';

            drawBB(map_parent_path);
        });
    }

    function setBoundary(parentPath, value) {
        js.get('<?php echo $_SERVER['PHP_SELF']; ?>' + '/?task=ajax.getBoundaryByName&value=' + value, function(data) {
            var response = js.parseJSON(data);
            var replaceId = parentPath.replace(/-/g, '_');
            js('#jform_' + replaceId + '_sla_gmd_dp_geographicElement_la_1_ra__sla_gmd_dp_EX_GeographicBoundingBox_sla_gmd_dp_northBoundLatitude_sla_gco_dp_Decimal').attr('value', response.northbound);
            js('#jform_' + replaceId + '_sla_gmd_dp_geographicElement_la_1_ra__sla_gmd_dp_EX_GeographicBoundingBox_sla_gmd_dp_southBoundLatitude_sla_gco_dp_Decimal').attr('value', response.southbound);
            js('#jform_' + replaceId + '_sla_gmd_dp_geographicElement_la_1_ra__sla_gmd_dp_EX_GeographicBoundingBox_sla_gmd_dp_eastBoundLongitude_sla_gco_dp_Decimal').attr('value', response.eastbound);
            js('#jform_' + replaceId + '_sla_gmd_dp_geographicElement_la_1_ra__sla_gmd_dp_EX_GeographicBoundingBox_sla_gmd_dp_westBoundLongitude_sla_gco_dp_Decimal').attr('value', response.westbound);

            var map_parent_path = replaceId + '_sla_gmd_dp_geographicElement_la_1_ra__sla_gmd_dp_EX_GeographicBoundingBox';

            drawBB(map_parent_path);
        });
    }

</script>

<script type="text/javascript">

    function drawBB(parent_path) {
        var top = js('#jform_' + parent_path + '_sla_gmd_dp_northBoundLatitude_sla_gco_dp_Decimal').attr('value');
        var bottom = js('#jform_' + parent_path + '_sla_gmd_dp_southBoundLatitude_sla_gco_dp_Decimal').attr('value');
        var right = js('#jform_' + parent_path + '_sla_gmd_dp_eastBoundLongitude_sla_gco_dp_Decimal').attr('value');
        var left = js('#jform_' + parent_path + '_sla_gmd_dp_westBoundLongitude_sla_gco_dp_Decimal').attr('value');

        if (top != '' && bottom != '' && left != '' && right != '') {

            var map = window['map_' + parent_path];

            var dest = new proj4.Proj(map.getProjection());
            var source = new proj4.Proj("EPSG:4326");

            var bottom_left = new proj4.Point(left, bottom);
            var top_right = new proj4.Point(right, top);

            proj4.transform(source, dest, bottom_left);
            proj4.transform(source, dest, top_right);

            var bounds = new OpenLayers.Bounds(bottom_left.x, bottom_left.y, top_right.x, top_right.y);

            var box = new OpenLayers.Feature.Vector(bounds.toGeometry());

            var layer = window['polygonLayer_' + parent_path];

            layer.addFeatures([box]);
        }
    }
</script>

<div class="metadata-edit front-end-edit">
    
    <button id="btn_toogle_all" action="open" class="btn">Tout ouvrir</button>
    <h2><?php echo JText::_('COM_EASYSDI_CATALOGE_TITLE_EDIT_METADATA') . ' ' . $this->item->guid; ?></h2>

    <form id="form-metadata" action="<?php echo JRoute::_('index.php?option=com_easysdi_catalog&task=metadata.save'); ?>" method="post" class="form-validate form-horizontal" enctype="multipart/form-data">
        <?php foreach ($this->form->getFieldset('hidden') as $field): ?>
            <?php echo $field->input; ?>
        <?php endforeach; ?>
        <div class ="well">
            <?php //echo htmlspecialchars($this->item->csw);      ?>

            <?php echo $this->formHtml; ?>

        </div>

        <div>
            <button type="submit" class="validate"><span><?php echo JText::_('JSUBMIT'); ?></span></button>
                    <?php echo JText::_('or'); ?>
            <a href="<?php echo JRoute::_('index.php?option=com_easysdi_catalog&task=metadata.cancel'); ?>" title="<?php echo JText::_('JCANCEL'); ?>"><?php echo JText::_('JCANCEL'); ?></a>

            <input type="hidden" name="option" value="com_easysdi_catalog" />
            <input type="hidden" name="task" value="metadata.save" />
            <?php echo JHtml::_('form.token'); ?>
        </div>
    </form>
</div>
