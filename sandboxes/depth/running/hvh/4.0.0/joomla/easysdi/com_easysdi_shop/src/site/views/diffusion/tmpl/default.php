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
</style>
<script type="text/javascript">
//    function getScript(url, success) {
//        var script = document.createElement('script');
//        script.src = url;
//        var head = document.getElementsByTagName('head')[0],
//                done = false;
//        // Attach handlers for all browsers
//        script.onload = script.onreadystatechange = function() {
//            if (!done && (!this.readyState
//                    || this.readyState == 'loaded'
//                    || this.readyState == 'complete')) {
//                done = true;
//                success();
//                script.onload = script.onreadystatechange = null;
//                head.removeChild(script);
//            }
//        };
//        head.appendChild(script);
//    }
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
                js('#file').show();
                js('#jform_file_hidden_href').show();
                js('#fileurl').hide();
                js('#jform_fileurl').val('');
                js('#perimeter_id').hide();
                js('#jform_perimeter_id :selected').removeAttr('selected');
                break;
            case "2":
                js('#file').hide();
                js('#jform_file_hidden_href').hide();
                js('#jform_file').val('');
                js('#jform_file_hidden').val('');
                js('#fileurl').show();
                js('#perimeter_id').hide();
                js('#jform_perimeter_id :selected').removeAttr('selected');
                break;
            case "3":
                js('#file').hide();
                js('#jform_file_hidden_href').hide();
                js('#jform_file').val('');
                js('#jform_file_hidden').val('');
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
                            <legend><?php echo JText::_('COM_EASYSDI_SHOP_FORM_FIELDSET_LEGEND_DOWNLOAD'); ?></legend>
                            <?php foreach ($this->form->getFieldset('download') as $field): ?>
                                <div class="control-group" id="<?php echo $field->fieldname; ?>">
                                    <div class="control-label"><?php echo $field->label; ?></div>
                                    <div class="controls"><?php echo $field->input; ?></div>
                                </div>
                            <?php endforeach; ?>
                            <div class="control-group" id="file">
                                <div class="control-label"><?php echo $this->form->getLabel('file'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('file'); ?>
                                    <?php if (!empty($this->item->file)) : ?>
                                        <a id="jform_file_hidden_href" href="<?php echo JRoute::_($this->params->get('fileFolder') . '/' . $this->item->file, false); ?>"><?php echo JText::_("COM_EASYSDI_SHOP_VIEW_FILE"); ?></a>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <input type="hidden" name="jform[file]" id="jform_file_hidden" value="<?php echo $this->item->file ?>" />			
                        </fieldset>
                        <fieldset id ="fieldset_extraction">
                            <legend><?php echo JText::_('COM_EASYSDI_SHOP_FORM_FIELDSET_LEGEND_EXTRACTION'); ?></legend>
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
