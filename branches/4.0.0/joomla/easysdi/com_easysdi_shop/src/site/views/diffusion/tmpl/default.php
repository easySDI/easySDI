<?php
/**
 * @version     4.0.0
 * @package     com_easysdi_shop
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
// no direct access
defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');

//Load admin language file
$lang = JFactory::getLanguage();
$lang->load('com_easysdi_shop', JPATH_ADMINISTRATOR);
$document = JFactory::getDocument();
$document->addScript('administrator/components/com_easysdi_core/libraries/easysdi/view/view.js')
?>

<!-- Styling for making front end forms look OK -->
<!-- This should probably be moved to the template CSS file -->
<style>
    .front-end-edit ul {
        padding: 0 !important;
    }
    .front-end-edit li {
        list-style: none;
        margin-bottom: 6px !important;
    }
    .front-end-edit label {
        margin-right: 10px;
        display: block;
        float: left;
        width: 200px !important;
    }
    .front-end-edit .radio label {
        float: none;
    }
    .front-end-edit .readonly {
        border: none !important;
        color: #666;
    }    
    .front-end-edit #editor-xtd-buttons {
        height: 50px;
        width: 600px;
        float: left;
    }
    .front-end-edit .toggle-editor {
        height: 50px;
        width: 120px;
        float: right;
    }

    #jform_rules-lbl{
        display:none;
    }

    #access-rules a:hover{
        background:#f5f5f5 url('../images/slider_minus.png') right  top no-repeat;
        color: #444;
    }

    fieldset.radio label{
        width: 50px !important;
    }

    .bar {
        height: 18px;
        background: green;
    }
</style>
<script type="text/javascript">
    js = jQuery.noConflict();
    js(document).ready(function() {
        enableAccessScope();
        onProductStorageChange();
        onPricingChange();
        js('#adminForm').submit(function(event) {

            if (js('#jform_deposit').val() != '') {
                js('#jform_deposit_hidden').val(js('#jform_deposit').val());
            }
            if (js('#jform_file').val() != '') {
                js('#jform_file_hidden').val(js('#jform_file').val());
            }
        });
    });
    function onProductStorageChange() {
        var storage = js("#jform_productstorage_id :selected").val();
        switch (storage) {
            case "1":
                
                js('#fileurl').hide();
                js('#jform_fileurl').val('');
                js('#perimeter_id').hide();
                js('#jform_perimeter_id :selected').removeAttr('selected');
                break;
            case "2":
                
                js('#fileurl').show();
                js('#perimeter_id').hide();
                js('#jform_perimeter_id :selected').removeAttr('selected');
                break;
            case "3":
                
                js('#fileurl').hide();
                js('#jform_fileurl').val('');
                js('#perimeter_id').show();
                break;
        }

    }

    function onPricingChange() {
        if (js('#jform_pricing_id').val() == 1) {
            js('#fieldset_download').show();
        } else {
            js('#fieldset_download').hide();
            js('#jform_file').val('');
            js('#jform_file_hidden').val('');
            js('#jform_fileurl').val('');
            js('#jform_perimeter_id :selected').removeAttr('selected');
            js('#jform_productstorage_id :selected').removeAttr('selected');

        }
    }

</script>
<link rel="stylesheet" href="http://blueimp.github.io/cdn/css/bootstrap.min.css">
<!-- Generic page styles -->
<link rel="stylesheet" href="components/com_easysdi_shop/views/diffusion/tmpl/css/style.css">
<!-- Bootstrap styles for responsive website layout, supporting different screen sizes -->
<link rel="stylesheet" href="http://blueimp.github.io/cdn/css/bootstrap-responsive.min.css">
<!-- Bootstrap CSS fixes for IE6 -->
<!--[if lt IE 7]><link rel="stylesheet" href="http://blueimp.github.io/cdn/css/bootstrap-ie6.min.css"><![endif]-->
<!-- CSS to style the file input field as button and adjust the Bootstrap progress bars -->
<link rel="stylesheet" href="components/com_easysdi_shop/views/diffusion/tmpl/css/jquery.fileupload-ui.css">
<!--<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>-->
<!-- The jQuery UI widget factory, can be omitted if jQuery UI is already included -->
<!-- The Templates plugin is included to render the upload/download listings -->
<script src="http://blueimp.github.io/JavaScript-Templates/js/tmpl.min.js"></script>
<!-- The Load Image plugin is included for the preview images and image resizing functionality -->
<script src="http://blueimp.github.io/JavaScript-Load-Image/js/load-image.min.js"></script>
<!-- The Canvas to Blob plugin is included for image resizing functionality -->
<script src="http://blueimp.github.io/JavaScript-Canvas-to-Blob/js/canvas-to-blob.min.js"></script>
<!-- Bootstrap JS is not required, but included for the responsive demo navigation -->
<script src="http://blueimp.github.io/cdn/js/bootstrap.min.js"></script>
<!-- blueimp Gallery script -->
<script src="http://blueimp.github.io/Gallery/js/jquery.blueimp-gallery.min.js"></script>
<!-- The Iframe Transport is required for browsers without support for XHR file uploads -->
<script src="components/com_easysdi_shop/views/diffusion/tmpl/js/vendor/jquery.ui.widget.js"></script>
<!-- The Iframe Transport is required for browsers without support for XHR file uploads -->
<script src="components/com_easysdi_shop/views/diffusion/tmpl/js/jquery.iframe-transport.js"></script>
<!-- The basic File Upload plugin -->
<script src="components/com_easysdi_shop/views/diffusion/tmpl/js/jquery.fileupload.js"></script>
<!-- The File Upload processing plugin -->
<script src="components/com_easysdi_shop/views/diffusion/tmpl/js/jquery.fileupload-process.js"></script>
<!-- The File Upload image preview & resize plugin -->
<script src="components/com_easysdi_shop/views/diffusion/tmpl/js/jquery.fileupload-image.js"></script>
<!-- The File Upload audio preview plugin -->
<script src="components/com_easysdi_shop/views/diffusion/tmpl/js/jquery.fileupload-audio.js"></script>
<!-- The File Upload video preview plugin -->
<script src="components/com_easysdi_shop/views/diffusion/tmpl/js/jquery.fileupload-video.js"></script>
<!-- The File Upload validation plugin -->
<script src="components/com_easysdi_shop/views/diffusion/tmpl/js/jquery.fileupload-validate.js"></script>
<!-- The template to display files available for upload -->
<!-- The File Upload user interface plugin -->
<script src="components/com_easysdi_shop/views/diffusion/tmpl/js/jquery.fileupload-ui.js"></script>
<!-- The main application script -->
<script src="components/com_easysdi_shop/views/diffusion/tmpl/js/main.js"></script>
<script id="template-upload" type="text/x-tmpl">
    {% for (var i=0, file; file=o.files[i]; i++) { %}
    <tr class="template-upload fade">
    <td>
    <span class="preview"></span>
    </td>
    <td>
    <p class="name">{%=file.name%}</p>
    {% if (file.error) { %}
    <div><span class="label label-important">Error</span> {%=file.error%}</div>
    {% } %}
    </td>
    <td>
    <p class="size">{%=o.formatFileSize(file.size)%}</p>
    {% if (!o.files.error) { %}
    <div class="progress progress-success progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"><div class="bar" style="width:0%;"></div></div>
    {% } %}
    </td>
    <td>
    {% if (!o.files.error && !i && !o.options.autoUpload) { %}
    <button class="btn btn-primary start">
    <i class="icon-upload icon-white"></i>
    <span>Start</span>
    </button>
    {% } %}
    {% if (!i) { %}
    <button class="btn btn-warning cancel">
    <i class="icon-ban-circle icon-white"></i>
    <span>Cancel</span>
    </button>
    {% } %}
    </td>
    </tr>
    {% } %}
</script>
<!-- The template to display files available for download -->
<script id="template-download" type="text/x-tmpl">
    {% for (var i=0, file; file=o.files[i]; i++) { %}
    <tr class="template-download fade">
    <td>
    <span class="preview">
    {% if (file.thumbnailUrl) { %}
    <a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" data-gallery><img src="{%=file.thumbnailUrl%}"></a>
    {% } %}
    </span>
    </td>
    <td>
    <p class="name">
    <a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" {%=file.thumbnailUrl?'data-gallery':''%}>{%=file.name%}</a>
    </p>
    {% if (file.error) { %}
    <div><span class="label label-important">Error</span> {%=file.error%}</div>
    {% } %}
    </td>
    <td>
    <span class="size">{%=o.formatFileSize(file.size)%}</span>
    </td>
    <td>
    <button id="btn-delete" class="btn btn-danger delete" data-type="{%=file.deleteType%}" data-url="{%=file.deleteUrl%}"{% if (file.deleteWithCredentials) { %} data-xhr-fields='{"withCredentials":true}'{% } %}>
    <i class="icon-trash icon-white"></i>
    <span>Delete</span>
    </button>
    <input type="checkbox" name="delete" value="1" class="toggle">
    </td>
    </tr>
    {% } %}
</script>


<div class="diffusion-edit front-end-edit">
    <?php if (!empty($this->item->id)): ?>
        <h1><?php echo JText::_('COM_EASYSDI_SHOP_TITLE_EDIT_DIFFUSION'); ?></h1>
    <?php else: ?>
        <h1><?php echo JText::_('COM_EASYSDI_SHOP_TITLE_NEW_DIFFUSION'); ?></h1>
    <?php endif; ?>



    <form class="form-inline form-validate" action="<?php echo JRoute::_('index.php?option=com_easysdi_shop&task=diffusion.save'); ?>" method="post" id="adminForm" name="adminForm" enctype="multipart/form-data">
        <div class="row-fluid">
            <div >
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#details" data-toggle="tab"><?php echo JText::_('COM_EASYSDI_SHOP_TAB_DETAILS'); ?></a></li>
                    <li><a href="#publishing" data-toggle="tab"><?php echo JText::_('COM_EASYSDI_SHOP_TAB_PUBLISHING'); ?></a></li>
                </ul>

                <div class="tab-content">
                    <!-- Begin Tabs -->
                    <div class="tab-pane active" id="details">
                        <?php foreach ($this->form->getFieldset('details') as $field): ?>
                            <div class="control-group" id="<?php echo $field->fieldname; ?>">
                                <div class="control-label"><?php echo $field->label; ?></div>
                                <div class="controls"><?php echo $field->input; ?></div>
                            </div>
                        <?php endforeach; ?>
                        <fieldset id ="fieldset_download">
                            <legend><?php echo JText::_('COM_EASYSDI_SHOP_FORM_FIELDSET_LEGEND_DOWNLOAD'); ?>
                                <?php echo $this->form->getInput('hasdownload'); ?></legend>
                            <?php foreach ($this->form->getFieldset('download') as $field): ?>
                                <div class="control-group" id="<?php echo $field->fieldname; ?>">
                                    <div class="control-label"><?php echo $field->label; ?></div>
                                    <div class="controls"><?php echo $field->input; ?></div>
                                </div>
                            <?php endforeach; ?>
                            <!-- The file upload div used as target for the file upload widget -->
                            <div id="fileupload" class="offset1" >
                                <!-- The fileupload-buttonbar contains buttons to add/delete files and start/cancel the upload -->
                                <div class="row fileupload-buttonbar">
                                    <div class="span10">
                                        <!-- The fileinput-button span is used to style the file input field as button -->
                                        <span class="btn btn-success fileinput-button">
                                            <i class="icon-plus icon-white"></i>
                                            <span>Add files...</span>
                                            <input type="file" name="files[]" multiple>
                                        </span>
                                        <button type="submit" class="btn btn-primary start">
                                            <i class="icon-upload icon-white"></i>
                                            <span>Start upload</span>
                                        </button>
                                        <button type="reset" class="btn btn-warning cancel">
                                            <i class="icon-ban-circle icon-white"></i>
                                            <span>Cancel upload</span>
                                        </button>
                                        <button id="btn-delete" type="button" class="btn btn-danger delete">
                                            <i class="icon-trash icon-white"></i>
                                            <span>Delete</span>
                                        </button>
                                        <input type="checkbox" class="toggle">
                                        <!-- The loading indicator is shown during file processing -->
                                        <span class="fileupload-loading"></span>
                                    </div>
                                    <!-- The global progress information -->
                                    <div class="span10 fileupload-progress fade">
                                        <!-- The global progress bar -->
                                        <div class="progress progress-success progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100">
                                            <div class="bar" style="width:0%;"></div>
                                        </div>
                                        <!-- The extended global progress information -->
                                        <div class="progress-extended">&nbsp;</div>
                                    </div>
                                </div>
                                <!-- The table listing the files available for upload/download -->
                                <table role="presentation" class="table table-striped"><tbody class="files"></tbody></table>
                            </div>
                        </fieldset>
                        <fieldset id ="fieldset_extraction">
                            <legend><?php echo JText::_('COM_EASYSDI_SHOP_FORM_FIELDSET_LEGEND_EXTRACTION'); ?>
                                <?php echo $this->form->getInput('hasextraction'); ?></legend>
                            <?php foreach ($this->form->getFieldset('extraction') as $field): ?>
                                <div class="control-group" id="<?php echo $field->fieldname; ?>">
                                    <div class="control-label"><?php echo $field->label; ?></div>
                                    <div class="controls"><?php echo $field->input; ?></div>
                                </div>
                            <?php endforeach; ?>
                            <div class="control-group" id="deposit">
                                <div class="control-label"><?php echo $this->form->getLabel('file'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('file'); ?>
                                    <?php if (!empty($this->item->deposit)) : ?>
                                        <a id="jform_deposit_hidden_href" href="<?php echo JRoute::_($this->params->get('depositFolder') . '/' . $this->item->deposit, false); ?>"><?php echo JText::_("COM_EASYSDI_SHOP_VIEW_FILE"); ?></a>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <input type="hidden" name="jform[deposit]" id="jform_deposit_hidden" value="<?php echo $this->item->deposit ?>" />			

                        </fieldset>
                    </div>

                    <div class="tab-pane" id="publishing">
                        <?php foreach ($this->form->getFieldset('publishing') as $field): ?>
                            <div class="control-group" id="<?php echo $field->fieldname; ?>">
                                <div class="control-label"><?php echo $field->label; ?></div>
                                <div class="controls"><?php echo $field->input; ?></div>
                            </div>
                        <?php endforeach; ?>
                        <?php if ($this->item->modified_by) : ?>
                            <?php foreach ($this->form->getFieldset('publishing_update') as $field): ?>
                                <div class="control-group" id="<?php echo $field->fieldname; ?>">
                                    <div class="control-label"><?php echo $field->label; ?></div>
                                    <div class="controls"><?php echo $field->input; ?></div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                </div>
            </div>
        </div>

        <?php foreach ($this->form->getFieldset('hidden') as $field): ?>
            <?php echo $field->input; ?>
        <?php endforeach; ?>  

        <?php echo $this->getToolbar(); ?>
        <input type = "hidden" name = "task" value = "" />
        <input type = "hidden" name = "option" value = "com_easysdi_shop" />
        <?php echo JHtml::_('form.token'); ?>
    </form>
</div>
