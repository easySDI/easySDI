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
JHtml::_('behavior.calendar');
JHtml::_('behavior.modal');

JText::script('COM_EASYSDI_CATALOG_METADATA_CONTROL_OK');
JText::script('COM_EASYSDI_CATALOG_METADATA_SAVE_WARNING');
JText::script('COM_EASYSDI_CATALOG_METADATA_EMPTY_WARNING');
JText::script('ARCHIVED');
JText::script('INPROGRESS');
JText::script('PUBLISHED');
JText::script('TRASHED');
JText::script('VALIDATED');


//Load admin language file
$lang = JFactory::getLanguage();
$lang->load('com_easysdi_catalog', JPATH_ADMINISTRATOR);
$document = JFactory::getDocument();

$document->addStyleSheet('administrator/components/com_easysdi_core/libraries/ext/resources/css/ext-all.css');
$document->addStyleSheet('administrator/components/com_easysdi_core/libraries/DataTables-1.9.4/media/css/jquery.dataTables.css');
$document->addScript('administrator/components/com_easysdi_core/libraries/easysdi/catalog/bootbox.min.js');
$document->addScript('administrator/components/com_easysdi_core/libraries/openlayers/OpenLayers.debug.js');
$document->addScript('administrator/components/com_easysdi_core/libraries/proj4js-1.4.1/dist/proj4.js');
$document->addScript('administrator/components/com_easysdi_core/libraries/ext/adapter/ext/ext-base-debug.js');
$document->addScript('administrator/components/com_easysdi_core/libraries/ext/ext-all-debug.js');
$document->addScript('administrator/components/com_easysdi_core/libraries/ext/ext-all-debug.js');
$document->addScript('administrator/components/com_easysdi_core/libraries/gemetclient-2.0.0/src/thesaur.js');
$document->addScript('administrator/components/com_easysdi_core/libraries/gemetclient-2.0.0/src/HS.js');
$document->addScript('administrator/components/com_easysdi_core/libraries/gemetclient-2.0.0/src/translations.js');
$document->addScript('administrator/components/com_easysdi_core/libraries/DataTables-1.9.4/media/js/jquery.dataTables.min.js');

$document->addScript('http://maps.google.com/maps/api/js?v=3&amp;sensor=false');

$document->addScript('administrator/components/com_easysdi_core/libraries/syntaxhighlighter/scripts/shCore.js');
$document->addScript('administrator/components/com_easysdi_core/libraries/syntaxhighlighter/scripts/shBrushXml.js');

$document->addScript('administrator/components/com_easysdi_core/libraries/easysdi/catalog/editMetadata.js');

$document->addStyleSheet('administrator/components/com_easysdi_core/libraries/syntaxhighlighter/styles/shCore.css');
$document->addStyleSheet('administrator/components/com_easysdi_core/libraries/syntaxhighlighter/styles/shThemeDefault.css');
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
    
    .hidden {
        display: none;
        visibility: hidden;
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

    .syntaxhighlighter{
        overflow: visible !important;
    }

    #previewModal{
        width: 900px;
        left: 40%;
    }

    #search_table{
        display: none;
    }

    #searchModal{
        width: 900px;
        left: 40%;
    }
    
    .sdi-multi-extent-select.chzn-container-multi .chzn-choices li.search-choice {
        min-width: 89%;
    }
    
    .sdi-multi-extent-select.chzn-container-multi .chzn-choices {
        max-height: 200px;
        max-height: 200px;
        overflow:auto;
    }
</style>

<script type="text/javascript">

    js = jQuery.noConflict();
    js('document').ready(function() {

<?php
if ($this->params->get('editmetadatafieldsetstate') == "allopen"){ ?>
            Joomla.submitbutton('metadata.toggle');
            tabIsOpen = false;
<?php }else{ ?>
            tabIsOpen = true;
<?php
}
foreach ($this->validators as $validator) {

    echo $validator;
}
?>
    });
</script>

<div class="metadata-edit front-end-edit">

    <?php
    echo $this->getTopActionBar();
    $title = $this->getTitle();
    ?>

    <div>
        <h2><?php echo JText::_('COM_EASYSDI_CATALOG_TITLE_EDIT_METADATA') . ' ' . $title->resource_name ?></h2>
        <h5><?php echo $title->name . ': ' . JText::_($title->value); ?></h5>
    </div>

    <form id="form-metadata" action="<?php echo JRoute::_('index.php?option=com_easysdi_catalog&task=metadata.save'); ?>" method="post" class="form-validate form-horizontal" enctype="multipart/form-data">


        <div class ="well">
            <?php echo $this->formHtml; ?>

            <?php foreach ($this->form->getFieldset('hidden') as $field): ?>
                <?php echo $field->input; ?>
            <?php endforeach; ?>
            <input type="hidden" name="option" value="com_easysdi_catalog" />
            <input type="hidden" name="task" value="" />

        </div>

        <div>

            <?php echo $this->getActionToolbar(); ?>

            <?php echo JHtml::_('form.token'); ?>
        </div>
    </form>

    <!-- Preview XML or XHTML Modal -->
    <div class="modal fade hide" id="previewModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel"><?php echo JText::_('COM_EASYSDI_CATALOG_PREVIEW_ITEM'); ?></h4>
                </div>
                <div id="previewModalBody" class="modal-body">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Replicate modal -->
    <div class="modal fade hide" id="searchModal" tabindex="-1" role="dialog" aria-labelledby="searchModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="searchModalLabel"><?php echo JText::_('COM_EASYSDI_CATALOG_TITLE_IMPORT_METADATA') ; ?></h4>
                </div>
                <div class="modal-body">
                    <div>
                        <form id="form_search_resource" action="<?php echo JRoute::_('index.php?option=com_easysdi_catalog&task=metadata.save'); ?>" method="post" class="form-validate form-horizontal">
                            <input type="hidden" name="task" value="">
                            <div class="control-group">
                                <label class="control-label" for="inputEmail"><?php echo JText::_('COM_EASYSDI_CATALOG_IMPORT_METADATA_TYPE') ; ?></label>
                                <div class="controls">
                                    <select id="resourcetype_id" name="resourcetype_id">
                                        <?php foreach ($this->getResourceType() as $resource) { ?>
                                        <option value="<?php echo $resource->id; ?>"><?php echo EText::_($resource->guid,1,  JText::_('COM_EASYSDI_CATALOG_IMPORT_METADATA_TYPE_ALL')); ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div id="resource_name_group" class="control-group">
                                <label class="control-label" for="inputEmail"><?php echo JText::_('COM_EASYSDI_CATALOG_IMPORT_METADATA_NAME') ; ?></label>
                                <div class="controls">
                                    <input id="resource_name" name="resource_name" type="text" value="">
                                </div>
                            </div>
                            <div class="control-group">
                                <label class="control-label" for="inputEmail">Status</label>
                                <div class="controls">
                                    <select id="status_id" name="status_id">
                                        <?php foreach ($this->getStatusList() as $status) { ?>
                                            <option value="<?php echo $status->id; ?>"><?php echo JText::_($status->value); ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="control-group" id="version-control-group" style="display:none;">
                                <label class="control-label" for="version">Version</label>
                                <div class="controls">
                                    <select id="version" name="version">
                                       <option value="all"><?php echo JText::_('COM_EASYSDI_CATALOG_IMPORT_METADATA_VERSION_ALL') ; ?></option>
                                       <option value="last" selected="selected"><?php echo JText::_('COM_EASYSDI_CATALOG_IMPORT_METADATA_VERSION_LAST') ; ?></option>
                                    </select>
                                </div>
                            </div>
                            <button onclick="Joomla.submitbutton('metadata.searchresource')" type="button" class="btn btn-success btn-small pull-right"><?php echo JText::_('COM_EASYSDI_CATALOG_IMPORT_METADATA_SEARCH') ; ?></button>
                        </form>

                        <!-- Select replicate form -->
                        <form id="form_replicate_resource" method="post" class="form-validate form-horizontal">
                            <input type="hidden" name="task" value="metadata.edit"/>
                            <input type="hidden" name="id" value="<?php echo $this->item->id; ?>"/>

                            <table id="search_table" class="table table-bordered">
                                <thead>
                                    <tr><th></th><th><?php echo JText::_('COM_EASYSDI_CATALOG_IMPORT_METADATA_NAME') ; ?></th><th><?php echo JText::_('COM_EASYSDI_CATALOG_IMPORT_METADATA_VERSION') ; ?></th><th><?php echo JText::_('COM_EASYSDI_CATALOG_IMPORT_METADATA_GUID') ; ?></th><th><?php echo JText::_('COM_EASYSDI_CATALOG_IMPORT_METADATA_TYPE') ; ?></th><th><?php echo JText::_('COM_EASYSDI_CATALOG_IMPORT_METADATA_STATUS') ; ?></th></tr>
                                </thead>
                                <tbody id="search_result">

                                </tbody>
                            </table>
                        </form>
                    </div>
                </div>
                <div class="modal-footer">
                    <button id="import-btn" style="display: none" type="button" class="btn btn-success" onclick="Joomla.submitbutton('metadata.edit')">Importer</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><?php echo JText::_('COM_EASYSDI_CATALOG_IMPORT_METADATA_CLOSE') ; ?></button>
                </div>
            </div>
        </div>
    </div>

    <!-- Import XML modal -->
    <div class="modal fade hide" id="importXmlModal" tabindex="-1" role="dialog" aria-labelledby="importXmlModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="importXmlModalLabel">Import</h4>
                </div>
                <div class="modal-body">
                    <form id="form_xml_import" action="<?php echo JRoute::_('index.php?option=com_easysdi_catalog&task=metadata.edit'); ?>" method="post" class="form-validate form-horizontal" accept-charset="utf-8" enctype="multipart/form-data">
                        <input type="hidden" name="task" value="metadata.edit"/>
                        <input type="hidden" name="id" value="<?php echo $this->item->id; ?>"/>
                        <input type="hidden" name="import[importref_id]" class="import_importref_id" value=""/>

                        <div class="control-group">
                            <div class="control-label"><label id="xml_file-lbl" for="xml_file" class="" aria-invalid="false"><?php echo JText::_('COM_EASYSDI_CATALOG_IMPORT_METADATA_XML_FILE') ; ?></label></div>
                            <div class="controls">
                                <div class="input-append">
                                    <input type="file" name="xml_file" id="xml_file"/>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" onclick="Joomla.submitbutton('metadata.importxml')" ><?php echo JText::_('COM_EASYSDI_CATALOG_IMPORT_METADATA_IMPORT') ; ?></button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><?php echo JText::_('JCANCEL'); ?></button>
                </div>
            </div>
        </div>
    </div>

    <!-- Import CSW modal -->
    <div class="modal fade hide" id="importCswModal" tabindex="-1" role="dialog" aria-labelledby="importCswModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="importCswModalLabel">Import</h4>
                </div>
                <div class="modal-body">
                    <form id="form_csw_import" action="<?php echo JRoute::_('index.php?option=com_easysdi_catalog&task=metadata.edit'); ?>" method="post" class="form-validate form-horizontal">
                        <input type="hidden" name="task" value="metadata.edit"/>
                        <input type="hidden" name="id" value="<?php echo $this->item->id; ?>"/>
                        <input type="hidden" name="import[importref_id]" class="import_importref_id" value=""/>

                        <div class="control-group">
                            <div class="control-label"><label id="import_fileidentifier-lbl" for="import_fileidentifier" class="" aria-invalid="false"><?php echo JText::_('COM_EASYSDI_CATALOG_IMPORT_METADATA_FILEIDENTIFIER') ; ?></label></div>
                            <div class="controls">
                                <div class="input-append">
                                    <input class="required" type="text" name="import[fileidentifier]" id="import_fileidentifier"/>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" onclick="Joomla.submitbutton('metadata.importcsw')" ><?php echo JText::_('COM_EASYSDI_CATALOG_IMPORT_METADATA_IMPORT') ; ?></button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><?php echo JText::_('JCANCEL'); ?></button>
                </div>
            </div>
        </div>
    </div>

    <!-- Publish Modal -->
    <div class="modal fade hide" id="publishModal" tabindex="-1" role="dialog" aria-labelledby="publishModalLabel" aria-hidden="true">
        <form id="form_publish" action="<?php echo JRoute::_('index.php?option=com_easysdi_catalog&task=metadata.save'); ?>" method="post" class="form-validate form-horizontal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="publishModalLabel"><?php echo JText::_('COM_EASYSDI_CATALOG_PUBLISH_DATE'); ?></h4>
                </div>
                <div class="modal-body">

                        <div class="control-group">
                            <div class="control-label"><label id="publish_date-lbl" for="publish_date" class="" aria-invalid="false"><?php echo JText::_('COM_EASYSDI_CATALOG_PUBLISH_DATE'); ?></label></div>
                            <div class="controls"><div class="input-append">
                                    <input type="text" name="publish_date" id="publish_date" value="" class=" required  validate-sdidatetime" aria-required="true" required="required" aria-invalid="false"><button class="btn" id="publish_date_img"><i class="icon-calendar"></i></button>
                                </div>
                            </div>
                        </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" onclick="Joomla.submitbutton('metadata.publishWithDate')" ><?php echo JText::_('COM_EASYSDI_CATALOG_PUBLISH_ITEM'); ?></button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><?php echo JText::_('JCANCEL'); ?></button>
                </div>
            </div>
        </div>
            </form>
    </div>

</div>
