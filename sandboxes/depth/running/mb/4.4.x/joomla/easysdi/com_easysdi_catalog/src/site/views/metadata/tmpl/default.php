<?php
/**
 * @version     4.4.5
 * @package     com_easysdi_catalog
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
// no direct access
defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('behavior.calendar');
JHtml::_('behavior.modal');

//Load admin language file
$lang = JFactory::getLanguage();
$lang->load('com_easysdi_catalog', JPATH_ADMINISTRATOR);
$lang->load('com_easysdi_core', JPATH_ADMINISTRATOR);
$document = JFactory::getDocument();

JText::script('COM_EASYSDI_CATALOG_METADATA_CONTROL_OK');
JText::script('COM_EASYSDI_CATALOG_METADATA_SAVE_WARNING');
JText::script('COM_EASYSDI_CATALOG_METADATA_EMPTY_WARNING');
JText::script('COM_EASYSDI_CATALOG_DELETE_RELATION_CONFIRM');
JText::script('COM_EASYSDI_CATALOG_ERROR_ADD_RELATION');
JText::script('COM_EASYSDI_CATALOG_ERROR_REMOVE_RELATION');
JText::script('COM_EASYSDI_CATALOG_ERROR_ADD_ATTRIBUTE_RELATION');
JText::script('COM_EASYSDI_CATALOG_ERROR_REMOVE_ATTRIBUTE_RELATION');
JText::script('COM_EASYSDI_CATALOG_ERROR_RETRIEVE_VERSION');
JText::script('COM_EASYSDI_CATALOG_ERROR_RETRIEVE_PUBLISHING_RIGHT');
JText::script('COM_EASYSDI_CATALOG_ERROR_RETRIEVE_IMPORT_REF');
JText::script('COM_EASYSDI_CATALOG_ERROR_REMOVE');
JText::script('COM_EASYSDI_CATALOG_ERROR_MD_LOCKED_TITLE');
JText::script('COM_EASYSDI_CATALOG_ERROR_MD_LOCKED_MESSAGE');
JText::script('COM_EASYSDI_CATALOG_METADATA_ARE_YOU_SURE');
JText::script('COM_EASYSDI_CATALOG_UNPUBLISHED_OR_UNVALIDATED_CHILDREN');


JText::script('ARCHIVED');
JText::script('INPROGRESS');
JText::script('PUBLISHED');
JText::script('VALIDATED');

JText::script('COM_EASYSDI_CATALOG_GEMET_HOME');
JText::script('COM_EASYSDI_CATALOG_GEMET_SEARCH');
JText::script('COM_EASYSDI_CATALOG_GEMET_BT');
JText::script('COM_EASYSDI_CATALOG_GEMET_NT');
JText::script('COM_EASYSDI_CATALOG_GEMET_RT');
JText::script('COM_EASYSDI_CATALOG_GEMET_TH');
JText::script('COM_EASYSDI_CATALOG_GEMET_USE');
JText::script('COM_EASYSDI_CATALOG_GEMET_THEMES');
JText::script('COM_EASYSDI_CATALOG_GEMET_GROUPS');
JText::script('COM_EASYSDI_CATALOG_GEMET_WARNING');
JText::script('COM_EASYSDI_CATALOG_GEMET_CHARACTERS_REQUIRED');
JText::script('COM_EASYSDI_CATALOG_GEMET_TOP_CONCEPTS');
JText::script('COM_EASYSDI_CATALOG_GEMET_FOUND');
JText::script('COM_EASYSDI_CATALOG_GEMET_INSPIRE_THEMES');
JText::script('COM_EASYSDI_CATALOG_GEMET_GEMET_TOP_CONCEPTS');

JText::script('COM_EASYSDI_CATALOG_OPEN_ALL');
JText::script('COM_EASYSDI_CATALOG_CLOSE_ALL');

JText::script('COM_EASYSDI_CATALOG_FILE_VALIDATE_OK');
JText::script('COM_EASYSDI_CATALOG_FILE_VALIDATE_KO');
JText::script('COM_EASYSDI_CATALOG_FILE_VALIDATE_UNABLE');
JText::script('COM_EASYSDI_CATALOG_FILE_UPLOAD_SUCCES');

JText::script('JGLOBAL_SELECT_SOME_OPTIONS');
JText::script('JGLOBAL_SELECT_AN_OPTION');
JText::script('JGLOBAL_SELECT_NO_RESULTS_MATCH');

JText::script('COM_EASYSDI_CORE_BOOTBOX_OVERRIDE_OK');
JText::script('COM_EASYSDI_CORE_BOOTBOX_OVERRIDE_CANCEL');
JText::script('COM_EASYSDI_CORE_BOOTBOX_OVERRIDE_CONFIRM');

/* bootbox language */
$ldao = new SdiLanguageDao();
$user = new sdiUser();
$userParams = json_decode($user->juser->params);
$defaultLanguage = $ldao->getDefaultLanguage();
$bbLanguage = $defaultLanguage->gemet;
$dtLanguage = $defaultLanguage->datatable;
if (isset($ldao) && isset($userParams)) {
    foreach ($ldao->getAll() as $bbLang) {
        if ($bbLang->code === $userParams->language) {
            $bbLanguage = $bbLang->gemet;
            $dtLanguage = $bbLang->datatable;
        }
    }
}

$base_url = Juri::root(true) . '/components/com_easysdi_core/libraries';

if (JDEBUG) {
    $document->addScript($base_url.'/OpenLayers-2.13.1/OpenLayers.debug.js');
    $document->addScript($base_url.'/ext/adapter/ext/ext-base-debug.js');
    $document->addScript($base_url.'/ext/ext-all-debug.js');
} else {
    $document->addScript($base_url.'/OpenLayers-2.13.1/OpenLayers.js');
    $document->addScript($base_url.'/ext/adapter/ext/ext-base.js');
    $document->addScript($base_url.'/ext/ext-all.js');
}
$document->addStyleSheet($base_url.'/ext/resources/css/ext-all.css');
$document->addStyleSheet($base_url.'/DataTables-1.9.4/media/css/jquery.dataTables.css');
$document->addScript($base_url.'/easysdi/catalog/bootbox.min.js');
$document->addScript($base_url.'/proj4js-1.1.0/lib/proj4js.js');
$document->addScript($base_url.'/gemetclient-2.0.0/src/thesaur.js');
$document->addScript($base_url.'/gemetclient-2.0.0/src/HS.js');
$document->addScript($base_url.'/DataTables-1.9.4/media/js/jquery.dataTables.min.js');

$document->addScript('http://maps.google.com/maps/api/js?v=3&amp;sensor=false');

$document->addScript($base_url.'/syntaxhighlighter/scripts/shCore.js');
$document->addScript($base_url.'/syntaxhighlighter/scripts/shBrushXml.js');

$document->addScript($base_url.'/jQuery-File-Upload-9.9.3/js/vendor/jquery.ui.widget.js');
$document->addScript($base_url.'/jQuery-File-Upload-9.9.3/js/jquery.iframe-transport.js');
$document->addScript($base_url.'/jQuery-File-Upload-9.9.3/js/jquery.fileupload.js');

$document->addScript($base_url.'/easysdi/catalog/editMetadata.js?v=' . sdiFactory::getSdiFullVersion());


$document->addStyleSheet($base_url.'/syntaxhighlighter/styles/shCore.css');
$document->addStyleSheet($base_url.'/syntaxhighlighter/styles/shThemeDefault.css');
$document->addStyleSheet(Juri::root(true) .'/components/com_easysdi_catalog/assets/css/easysdi_catalog.css?v=' . sdiFactory::getSdiFullVersion());
$document->addStyleSheet($base_url.'/jQuery-File-Upload-9.9.3/css/jquery.fileupload.css');
$document->addStyleSheet($base_url.'/jQuery-File-Upload-9.9.3/css/jquery.fileupload-ui.css');
?>

<script type="text/javascript">
    var dtLang = "<?php echo ucfirst(strtolower($dtLanguage)); ?>";
    var baseUrl = "<?php echo JUri::base(); ?>index.php?";
    var iframewidth = "<?php echo JComponentHelper::getParams('com_easysdi_catalog')->get('iframewidth'); ?>";
    var iframeheight = "<?php echo JComponentHelper::getParams('com_easysdi_catalog')->get('iframeheight'); ?>";
    var resetMetadataUrl = "<?php echo JRoute::_('index.php?option=com_easysdi_catalog&task=metadata.edit&id=' . $this->item->id); ?>";
    js = jQuery.noConflict();
    js('document').ready(function () {
        //override or create locale
        bootbox.addLocale('<?php echo $bbLanguage; ?>', {
            OK: Joomla.JText._('COM_EASYSDI_CORE_BOOTBOX_OVERRIDE_OK', 'OK'),
            CANCEL: Joomla.JText._('COM_EASYSDI_CORE_BOOTBOX_OVERRIDE_CANCEL', 'Cancel'),
            CONFIRM: Joomla.JText._('COM_EASYSDI_CORE_BOOTBOX_OVERRIDE_CONFIRM', 'Confirm')
        });
        //set locale
        bootbox.setLocale("<?php echo $bbLanguage; ?>");
<?php if ($this->params->get('editmetadatafieldsetstate') == "allopen") { ?>
            toogleAll(js('#btn_toggle_all'));
            tabIsOpen = true;
<?php } else { ?>
            tabIsOpen = false;
    <?php
}
?>
    });
</script>

<?php
require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_core/libraries/easysdi/catalog/validators.js.php';
?>

<div class="metadata-edit front-end-edit">

    <?php
    echo $this->getActionToolbar();
    $title = $this->getTitle();
    ?>

    <div>
        <h1><?php echo JText::_('COM_EASYSDI_CATALOG_TITLE_EDIT_METADATA') . ' ' . $title->resource_name ?></h1>
        <h5><span class="sdi-md-version-title"><?php echo $title->name; ?></span><span class="sdi-md-version-separator"> : </span><span class="sdi-md-version-state"><?php echo $title->state_label; ?></span></h5>
    </div>

    <form id="form-metadata" action="<?php echo JRoute::_('index.php?option=com_easysdi_catalog&task=metadata.save'); ?>" method="post" class="form-validate form-horizontal" enctype="multipart/form-data">

        <div class ="well">

            <?php echo $this->getToggleCollapseButton(); ?>
            <?php echo $this->formHtml; ?>

            <?php foreach ($this->form->getFieldset('hidden') as $field): ?>
                <?php echo $field->input; ?>
            <?php endforeach; ?>
            <input type="hidden" name="option" value="com_easysdi_catalog" />
            <input type="hidden" name="task" value="" />
            <input type="hidden" name="preview" value="public" />
            <input type="hidden" name="viral" id="jform_viral" value="0" />

        </div>

        <div>
            <?php echo JHtml::_('form.token'); ?>
        </div>
    </form>

    <?php echo $this->getActionToolbar(); ?>

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
                    <h4 class="modal-title" id="searchModalLabel"><?php echo JText::_('COM_EASYSDI_CATALOG_TITLE_IMPORT_METADATA'); ?></h4>
                </div>
                <div class="modal-body">
                    <div>
                        <form id="form_search_resource" action="<?php echo JRoute::_('index.php?option=com_easysdi_catalog&task=metadata.save'); ?>" method="post" class="form-validate form-horizontal">
                            <input type="hidden" name="task" value="">
                            <div class="control-group">
                                <label class="control-label" for="resourcetype_id"><?php echo JText::_('COM_EASYSDI_CATALOG_IMPORT_METADATA_TYPE'); ?></label>
                                <div class="controls">
                                    <select id="resourcetype_id" name="resourcetype_id">
                                        <?php foreach ($this->getResourceType() as $resource) { ?>
                                            <option value="<?php echo $resource->id; ?>"<?php if ($this->item->resourcetype_id == $resource->id): ?> selected="selected"<?php endif; ?>><?php echo EText::_($resource->guid, 1, JText::_('COM_EASYSDI_CATALOG_IMPORT_METADATA_TYPE_ALL')); ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div id="resource_name_group" class="control-group">
                                <label class="control-label" for="resource_name"><?php echo JText::_('COM_EASYSDI_CATALOG_IMPORT_METADATA_NAME'); ?></label>
                                <div class="controls">
                                    <input id="resource_name" name="resource_name" type="text" value="">
                                </div>
                            </div>
                            <div class="control-group">
                                <label class="control-label" for="status_id">Status</label>
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
                                        <option value="all"><?php echo JText::_('COM_EASYSDI_CATALOG_IMPORT_METADATA_VERSION_ALL'); ?></option>
                                        <option value="last" selected="selected"><?php echo JText::_('COM_EASYSDI_CATALOG_IMPORT_METADATA_VERSION_LAST'); ?></option>
                                    </select>
                                </div>
                            </div>
                            <div id="resource_organism_group" class="control-group">
                                <label class="control-label" for="organism_id"><?php echo JText::_('COM_EASYSDI_CATALOG_IMPORT_ORGANISM'); ?></label>
                                <div class="controls">
                                    <select id="organism_id" name="organism_id">
                                        <?php
                                        $userOrganism = $user->getMemberOrganisms();
                                        $coreHelper = new Easysdi_coreHelper();
                                        $organisms = $coreHelper->getOrganisms(true);

                                        foreach ($organisms as $organism):
                                            ?>
                                            <option value="<?php echo $organism->id; ?>"
                                                    <?php if ($organism->id === $userOrganism[0]->id): ?>selected="selected"<?php endif; ?>
                                                    ><?php echo $organism->name; ?></option>
                                                <?php endforeach; ?>

                                    </select>
                                </div>
                            </div>
                            <button onclick="Joomla.submitbutton('metadata.searchresource')" type="button" class="btn btn-success btn-small pull-right"><?php echo JText::_('COM_EASYSDI_CATALOG_IMPORT_METADATA_SEARCH'); ?></button>
                        </form>

                        <!-- Select replicate form -->
                        <form id="form_replicate_resource" method="post" class="form-validate form-horizontal">
                            <input type="hidden" name="task" value="metadata.edit"/>
                            <input type="hidden" name="id" value="<?php echo $this->item->id; ?>"/>

                            <table id="search_table" class="table table-bordered">
                                <thead>
                                    <tr><th></th><th><?php echo JText::_('COM_EASYSDI_CATALOG_IMPORT_METADATA_NAME'); ?></th><th><?php echo JText::_('COM_EASYSDI_CATALOG_IMPORT_METADATA_VERSION'); ?></th><th><?php echo JText::_('COM_EASYSDI_CATALOG_IMPORT_METADATA_GUID'); ?></th><th><?php echo JText::_('COM_EASYSDI_CATALOG_IMPORT_METADATA_TYPE'); ?></th><th><?php echo JText::_('COM_EASYSDI_CATALOG_IMPORT_METADATA_STATUS'); ?></th></tr>
                                </thead>
                                <tbody id="search_result">

                                </tbody>
                            </table>
                        </form>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn" data-dismiss="modal"><?php echo JText::_('COM_EASYSDI_CATALOG_IMPORT_METADATA_CLOSE'); ?></button>
                    <button id="import-btn" style="display: none" type="button" class="btn btn-primary" onclick="Joomla.submitbutton('metadata.edit')"><?php echo JText::_('COM_EASYSDI_CATALOG_IMPORT_METADATA_IMPORT'); ?></button>
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
                            <div class="control-label"><label id="xml_file-lbl" for="xml_file" class="" aria-invalid="false"><?php echo JText::_('COM_EASYSDI_CATALOG_IMPORT_METADATA_XML_FILE'); ?></label></div>
                            <div class="controls">
                                <div class="input-append">
                                    <input type="file" name="xml_file" id="xml_file"/>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn" data-dismiss="modal"><?php echo JText::_('JCANCEL'); ?></button>
                    <button type="button" class="btn btn-primary" onclick="Joomla.submitbutton('metadata.importxml')" ><?php echo JText::_('COM_EASYSDI_CATALOG_IMPORT_METADATA_IMPORT'); ?></button>
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
                            <div class="control-label"><label id="import_fileidentifier-lbl" for="import_fileidentifier" class="" aria-invalid="false"><?php echo JText::_('COM_EASYSDI_CATALOG_IMPORT_METADATA_FILEIDENTIFIER'); ?></label></div>
                            <div class="controls">
                                <div class="input-append">
                                    <input class="required" type="text" name="import[fileidentifier]" id="import_fileidentifier"/>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn" data-dismiss="modal"><?php echo JText::_('JCANCEL'); ?></button>
                    <button type="button" class="btn btn-primary" onclick="Joomla.submitbutton('metadata.importcsw')" ><?php echo JText::_('COM_EASYSDI_CATALOG_IMPORT_METADATA_IMPORT'); ?></button>
                </div>
            </div>
        </div>
    </div>

    <!-- Publish Modal -->
    <div class="modal fade hide" id="publishModal" tabindex="-1" role="dialog" aria-labelledby="publishModalLabel" aria-hidden="true">
        <form id="form_publish" action="<?php echo JRoute::_('index.php?option=com_easysdi_catalog&task=metadata.save'); ?>" method="post" class="form-validate form-horizontal">
            <input type="hidden" id="viral" name="viral" value="0"/>
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
                                    <input type="text" name="publish_date" id="publish_date" value="" class=" required validate-sdidatetime" aria-required="true" required="required" aria-invalid="false">
                                </div>
                            </div>
                        </div>
                        <?php echo JText::_('COM_EAYSDI_CORE_PUBLISH_CONFIRM'); ?>
                        <span id="publishModalCurrentMetadata"></span>
                        <div id="publishModalChildrenDiv" style="display:none">
                            <input type="checkbox" id="publishModalViralPublication"> <?php echo JText::_('COM_EAYSDI_CORE_PUBLISH_CHILDREN_CONFIRM'); ?>
                            <span id="publishModalChildrenList"></span>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn" data-dismiss="modal"><?php echo JText::_('JCANCEL'); ?></button>
                        <button type="button" class="btn btn-primary" onclick="Joomla.submitbutton('metadata.publishWithDate')" ><?php echo JText::_('COM_EASYSDI_CATALOG_PUBLISH_ITEM'); ?></button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="modal fade hide" id="fileModal" tabindex="-1" role="dialog" aria-labelledby="publishModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="fileModalLabel"><?php echo JText::_('COM_EASYSDI_CATALOG_FILE_TITLE'); ?></h4>
                </div>
                <form class="form-inline">
                    <div class="modal-body">
                        <input type="hidden" id="file_source_field" value=""/>
                        <input type="hidden" id="file_url" value=""/>
                        <ul id="fileTabs" class="nav nav-tabs">
                            <li class="active upload">
                                <a href="#upload" data-toggle="tab"><?php echo JText::_('COM_EASYSDI_CATALOG_FILE_UPLOAD_TAB'); ?></a>
                            </li>
                            <li class="url"><a href="#url" data-toggle="tab"><?php echo JText::_('COM_EASYSDI_CATALOG_FILE_URL_TAB'); ?></a></li>
                        </ul>
                        <div id="fileTabsContent" class="tab-content">
                            <div class="tab-pane fade active in upload" id="upload">
                                <div id="fileUploadPreview" style="display: none"><a href="#" target="_blank"><img src=""/></a></div>
                                <div class="btn btn-info fileinput-button">
                                    <span><?php echo JText::_('COM_EASYSDI_CATALOG_FILE_CHOOSE_FILE'); ?></span>
                                    <input type="file" name="fileUpload" id="fileUpload" data-url="<?php echo JRoute::_('index.php?option=com_easysdi_catalog&task=ajax.uploadFile'); ?>"/> 
                                </div>

                                <br/><br/>
                                <div class="progress" style="display: none">
                                    <div class="bar" style="width: 0%;"></div>
                                </div>

                                <div id="fileUploadValidate">

                                </div>
                            </div>
                            <div class="tab-pane fade url" id="url">

                                <div class="control-group">
                                    <div class="control-label">
                                        <label id="fileUrl-lbl" for="fileUrl" class=""><?php echo JText::_('COM_EASYSDI_CATALOG_FILE_URL_LABEL'); ?></label>
                                    </div>
                                    <div class="controls">
                                        <input type="text" name="fileUrl" id="fileUrl"/>
                                        <button type="button" class="btn btn-info"><?php echo JText::_('COM_EASYSDI_CATALOG_FILE_VALIDATE'); ?></button>
                                    </div>
                                </div>
                                <div id="fileUrlValidate">

                                </div>

                            </div>
                        </div>
                    </div>
                </form>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success"><?php echo JText::_('COM_EASYSDI_CATALOG_FILE_FINISH'); ?></button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><?php echo JText::_('JCANCEL'); ?></button>
                </div>
            </div>
        </div>
        </form>
    </div>

</div>
