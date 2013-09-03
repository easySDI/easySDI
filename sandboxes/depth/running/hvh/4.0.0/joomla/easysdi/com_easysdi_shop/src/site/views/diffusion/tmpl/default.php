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
    js = jQuery.noConflict();
    js(document).ready(function() {
        enableAccessScope();
        onProductStorageChange();
        onPricingChange();
        enableDownload();
        enableExtraction();
        js('#adminForm').submit(function(event) {
            if (js('#jform_deposit').val() != '') {
                js('#jform_deposit_hidden').val(js('#jform_deposit').val());
            }
            if (js('#jform_file').val() != '') {
                js('#jform_file_hidden').val(js('#jform_file').val());
            }
        });
    });
    Joomla.submitbutton = function(task)
    {
        if (task == 'diffusion.cancel') {
            Joomla.submitform(task, document.getElementById('adminForm'));
        }
        else {

            if (task != 'diffusion.cancel' && document.formvalidator.isValid(document.id('adminForm'))) {

                Joomla.submitform(task, document.getElementById('adminForm'));
            }
            else {
                alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
            }
        }
    }
    function onProductStorageChange() {
        var storage = js("#jform_productstorage_id :selected").val();
        switch (storage) {
            case "1":
                js('#file').show();
                js('#fileurl').hide();
                js('#perimeter_id').hide();
                break;
            case "2":
                js('#file').hide();
                js('#fileurl').show();
                js('#perimeter_id').hide();
                break;
            case "3":
                js('#file').hide();
                js('#fileurl').hide();
                js('#perimeter_id').show();
                break;
        }
    }

    function onPricingChange() {
        if (js('#jform_pricing_id').val() == 1) {
            js('#fieldset_download').show();
        } else {
            js('#fieldset_download').hide();
        }
    }

    function enableDownload() {
        if (js('#jform_hasdownload').is(':checked')) {
            js('#div_download').show();
        } else {
            js('#div_download').hide();
        }
    }

    function enableExtraction() {
        if (js('#jform_hasextraction').is(':checked')) {
            js('#div_extraction').show();
        } else {
            js('#div_extraction').hide();
        }
    }

    function cleanDownload() {
        js('#jform_productstorage_id').find("option").attr("selected", false);
        js('#jform_fileurl').val('');
        js('#jform_perimeter_id').find("option").attr("selected", false);
        cleanFile();
    }

    function cleanExtraction() {
        js('#jform_productmining_id').find("option").attr("selected", false);
        js('#jform_surfacemin').val('');
        js('#jform_surfacemax').val('');
        cleanDeposit();
    }

    function cleanFile() {
        js('#jform_file').val('');
        js('#jform_file_hidden').val('');
    }
    function cleanDeposit() {
        js('#jform_deposit').val('');
        js('#jform_deposit_hidden').val('');
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
                            <legend><?php echo JText::_('COM_EASYSDI_SHOP_FORM_FIELDSET_LEGEND_DOWNLOAD'); ?>
                                <?php echo $this->form->getInput('hasdownload'); ?></legend>
                            <div id="div_download">
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
                                            <a id="jform_file_hidden_href" href="<?php echo JRoute::_($this->params->get('fileFolder') . '/' . $this->item->file, false); ?>"><?php echo '[' . substr($this->item->file, 33) . ']'; ?></a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <input type="hidden" name="jform[file]" id="jform_file_hidden" value="<?php echo $this->item->file ?>" />	
                            </div>
                        </fieldset>
                        <fieldset id ="fieldset_extraction">
                            <legend><?php echo JText::_('COM_EASYSDI_SHOP_FORM_FIELDSET_LEGEND_EXTRACTION'); ?>
                                <?php echo $this->form->getInput('hasextraction'); ?></legend>
                            <div id="div_extraction">
                                <div class="control-group" id="deposit">
                                    <div class="control-label"><?php echo $this->form->getLabel('deposit'); ?></div>
                                    <div class="controls"><?php echo $this->form->getInput('deposit'); ?>
                                        <?php if (!empty($this->item->deposit)) : ?>
                                            <a id="jform_deposit_hidden_href" href="<?php echo JRoute::_($this->params->get('depositFolder') . '/' . $this->item->deposit, false); ?>"><?php echo '[' . substr($this->item->deposit, 33) . ']'; ?></a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <input type="hidden" name="jform[deposit]" id="jform_deposit_hidden" value="<?php echo $this->item->deposit ?>" />	
                                <?php foreach ($this->form->getFieldset('extraction') as $field): ?>
                                    <div class="control-group" id="<?php echo $field->fieldname; ?>">
                                        <div class="control-label"><?php echo $field->label; ?></div>
                                        <div class="controls"><?php echo $field->input; ?></div>
                                    </div>
                                <?php endforeach; ?>
                                <fieldset id ="fieldset_perimeters" class="span0 offset1">
                                    <legend><?php echo JText::_('COM_EASYSDI_SHOP_FORM_FIELDSET_LEGEND_PERIMETERS'); ?></legend>
                                    <?php foreach ($this->orderperimeters as $orderperimeter): ?>
                                        <div class="control-group" >
                                            <div class="control-label">
                                                <label id="jform_perimeter<?php echo $orderperimeter->id; ?>-lbl" for="jform_perimeter<?php echo $orderperimeter->id; ?>" class="hasTip" title=""><?php echo $orderperimeter->name; ?></label>
                                            </div>
                                            <div class="controls">
                                                <select id="jform_perimeter<?php echo $orderperimeter->id ?>" name="jform[perimeter][<?php echo $orderperimeter->id ?>]" class="inputbox"  >
                                                    <option value="-1" ><?php echo JText::_("COM_EASYSDI_SHOP_FORM_DONOT_DISPLAY_PERIMETER"); ?></option>
                                                    <option value="1" <?php if(array_key_exists($orderperimeter->id,$this->item->perimeter ) && $this->item->perimeter[$orderperimeter->id] == 1) echo 'selected';  ?>><?php echo JText::_("COM_EASYSDI_SHOP_FORM_DO_DISPLAY_PERIMETER"); ?></option>
                                                    <option value="0" <?php if(array_key_exists($orderperimeter->id,$this->item->perimeter ) && $this->item->perimeter[$orderperimeter->id] == 0) echo 'selected';  ?>><?php echo JText::_("COM_EASYSDI_SHOP_FORM_DO_DISPLAY_PERIMETER_WITH_BUFFER"); ?></option>
                                                </select>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>

                                </fieldset>
                                <fieldset id ="fieldset_properties" class="span0 offset1">
                                    <legend><?php echo JText::_('COM_EASYSDI_SHOP_FORM_FIELDSET_LEGEND_PROPERTIES'); ?></legend>
                                    <?php foreach ($this->properties as $property): ?>
                                        <div class="control-group" >
                                            <div class="control-label">
                                                <label id="jform_property<?php echo $property->id ?>-lbl" for="jform_property<?php echo $property->id ?>" class="hasTip <?php if($property->mandatory) echo 'required' ; ?>" title=""><?php echo sdiMultilingual::getTranslation($property->guid); ?><?php if($property->mandatory) echo '<span class="star">&#160;*</span>' ; ?></label>
                                            </div>
                                            <div class="controls">
                                                <?php
                                                switch ($property->propertytype_id):
                                                    case 1:
                                                    case 2:
                                                    case 3:
                                                        ?>
                                                        <select id="jform_property<?php echo $property->id ?>" name="jform[property][<?php echo $property->id ?>][]" class="inputbox <?php if($property->mandatory) echo 'required' ; ?>" multiple="multiple" >
                                                            <?php
                                                            foreach ($this->propertyvalues as $propertyvalue):
                                                                if ($propertyvalue->property_id == $property->id):
                                                                    ?>
                                                                    <option value="<?php echo $propertyvalue->id; ?>" <?php if(array_key_exists($property->id,$this->item->property ) && in_array($propertyvalue->id, $this->item->property[$property->id])) echo 'selected';  ?>><?php echo sdiMultilingual::getTranslation($propertyvalue->guid); ?></option>
                                                                    <?php
                                                                endif;
                                                            endforeach;
                                                            ?>
                                                        </select>
                                                        <?php
                                                        break;
                                                    case 4:
                                                        ?>
                                                        <select id="jform_property<?php echo $property->id ?>" name="jform[property][<?php echo $property->id ?>]" class="inputbox <?php if($property->mandatory) echo 'required' ; ?>"  >
                                                            <?php if (!$property->mandatory): ?>
                                                                <option value="-1"><?php echo JText::_("COM_EASYSDI_SHOP_FORM_DONOT_DISPLAY_FIELD"); ?></option>
                                                                <?php
                                                                foreach ($this->propertyvalues as $propertyvalue):
                                                                    if ($propertyvalue->property_id == $property->id):
                                                                        ?>
                                                                        <option value="<?php echo $propertyvalue->id; ?>" <?php if(array_key_exists($property->id,$this->item->property ) && in_array($propertyvalue->id, $this->item->property[$property->id])) echo 'selected';  ?>><?php echo JText::_("COM_EASYSDI_SHOP_FORM_DO_DISPLAY_FIELD"); ?></option>
                                                                        <?php
                                                                        break;
                                                                    endif;
                                                                endforeach;
                                                                ?>
                                                            <?php
                                                            else:
                                                                foreach ($this->propertyvalues as $propertyvalue):
                                                                    if ($propertyvalue->property_id == $property->id):
                                                                        ?>
                                                                        <option value="<?php echo $propertyvalue->id; ?>" <?php if(array_key_exists($property->id,$this->item->property ) && in_array($propertyvalue->id, $this->item->property[$property->id])) echo 'selected';  ?>><?php echo sdiMultilingual::getTranslation($propertyvalue->guid); ?></option>
                                                                        <?php
                                                                    endif;
                                                                endforeach;
                                                            endif;
                                                            ?>
                                                        </select>
                                                        <?php
                                                        break;
                                                    case 5:
                                                    case 6 :
                                                        ?>
                                                        <select id="jform_property<?php echo $property->id ?>" name="jform[property][<?php echo $property->id; ?>]" class="inputbox <?php if($property->mandatory) echo 'required' ; ?>"  >
                                                            <?php if (!$property->mandatory): ?>
                                                                <option value="-1"><?php echo JText::_("COM_EASYSDI_SHOP_FORM_DONOT_DISPLAY_FIELD"); ?></option>
                                                            <?php endif; ?>
                                                            <?php
                                                            foreach ($this->propertyvalues as $propertyvalue):
                                                                if ($propertyvalue->property_id == $property->id):
                                                                    ?>
                                                                    <option value="<?php echo $propertyvalue->id; ?>"><?php echo sdiMultilingual::getTranslation($propertyvalue->guid); ?></option>
                                                                    <?php
                                                                endif;
                                                            endforeach;
                                                            ?>
                                                        </select>
                                                        <?php
                                                        break;
                                                endswitch;
                                                ?>
                                            </div>
                                        </div>
<?php endforeach; ?>
                                </fieldset>
                            </div>
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


        <input type = "hidden" name = "task" value = "" />
        <input type = "hidden" name = "option" value = "com_easysdi_shop" />
<?php echo JHtml::_('form.token'); ?>
    </form>
        
        <?php echo $this->getToolbar(); ?>
</div>
