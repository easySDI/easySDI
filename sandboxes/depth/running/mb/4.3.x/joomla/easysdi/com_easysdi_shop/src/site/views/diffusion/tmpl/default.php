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

<script type="text/javascript">
    
    js = jQuery.noConflict();
    js(document).ready(function() {
        enableAccessScope();
        onProductStorageChange();
        onPricingChange();
        enableDownload();
        enableExtraction();
        enableFreePerimeter();
        
        js('#adminForm').submit(function(event) { console.log('here');return false;
            if (js('#jform_deposit').val() != '') {
                js('#jform_deposit_hidden').val(js('#jform_deposit').val());
            }
            if (js('#jform_file').val() != '') {
                js('#jform_file_hidden').val(js('#jform_file').val());
            }
        });
        js('#jform_restrictedperimeter').change(enableFreePerimeter);
        
        js('#jform_testurlauthentication').click(onTestUrlAuthenticationClick);
        js('#jform_testurlauthentication').parent().append('<span id="result_testurlauthentication"></span>');
    });
    Joomla.submitbutton = function(task)
    {
        if (task == 'diffusion.cancel') {
            Joomla.submitform(task, document.getElementById('adminForm'));
        }
        else {

            if (task != 'diffusion.cancel' && document.formvalidator.isValid(document.id('adminForm'))) {
                if (js('#jform_hasextraction').is(':checked')) {
                    var perimeterselected = false;
                    js('.perimeterselect').each(function() {
                        var currentElement = js(this);
                        if (currentElement.val() != -1) {
                            perimeterselected = true;
                        }
                    })
                    if (perimeterselected == false) {
                        alert('<?php echo $this->escape(JText::_('COM_EASYSDI_SHOP_FORM_MSG_DIFFUSION_NO_PERIMETER_SELECTED',true)); ?>');
                    } else {
                        Joomla.submitform(task, document.getElementById('adminForm'));
                    }
                } else {
                    Joomla.submitform(task, document.getElementById('adminForm'));
                }
            }
            else {
                alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED',true)); ?>');
            }
        }
    }
    function onProductStorageChange() {
        var storage = js("#jform_productstorage_id :selected").val();
        switch (storage) {
            case "1":
                js('#file').show();
                js('#fileurl, #userurl, #passurl, #testurlauthentication').hide();
                js('#perimeter_id').hide();
                break;
            case "2":
                js('#file').hide();
                js('#fileurl, #userurl, #passurl, #testurlauthentication').show();
                js('#perimeter_id').hide();
                break;
            case "3":
                js('#file').hide();
                js('#fileurl, #userurl, #passurl, #testurlauthentication').hide();
                js('#perimeter_id').show();
                break;
        }
    }
var globdata;
    function onPricingChange() {
        
        switch(js('#jform_pricing_id').val()){
            case '1': // FREE
                js('#fieldset_download').show();
                js('#pricing_profile_id').hide();
                break;
                
            case '2': // FEE WITHOUT PRICING PROFILE
                js('#fieldset_download').hide();
                js('#pricing_profile_id').hide();
                break;
                
            case '3': // FEE WITH PRICING PROFILE
                js('#fieldset_download').hide();
                
                if(!js('#pricing_profile_id option').length){
                    js.ajax({
                        url: "<?php echo JRoute::_('index.php?option=com_easysdi_shop&task=diffusion.getAvailableProfiles') ?>",
                        type: "POST",
                        data: {
                            version_id: <?php echo $this->item->version_id;?>
                        }
                        }).fail(function(){
                        console.log('todo');
                    }).done(function(data){
                        data.each(function(item){
                            js('#pricing_profile_id select').append(js('<option>', {
                                value: item.id,
                                text: item.name
                            })).trigger('liszt:updated');
                        });
                    });
                }
                
                js('#pricing_profile_id').show();
                break;
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

    function enableFreePerimeter() {
        if (js('#jform_restrictedperimeter0').is(':checked') == true) {
            js('#jform_perimeter1').removeAttr('disabled', 'disabled');
        } else {
            js('#jform_perimeter1').attr('disabled', 'disabled');
            js('#jform_perimeter1 option[value=-1]').attr("selected", "selected");
            
        }
        js('#jform_perimeter1').trigger("liszt:updated");
    }
    
    function onTestUrlAuthenticationClick(){
        js.ajax({
            url: "<?php echo JRoute::_('index.php?option=com_easysdi_shop&task=diffusion.testURLAccessibility') ?>",
            type: "POST",
            data: {
                url: js('#jform_fileurl').val(),
                user: js('#jform_userurl').val(),
                password: js('#jform_passurl').val()
            }
        }).fail(function(){
            console.log('todo');
        }).done(function(data){
            js('#result_testurlauthentication').removeClass('success error');
            if(data == 1)
                js('#result_testurlauthentication').html('<?php echo JText::_('COM_EASYSDI_SHOP_TEST_URL_AUTHENTICATION_OK',true); ?>').addClass('success');
            else{
                js('#result_testurlauthentication').html('<?php echo JText::_('COM_EASYSDI_SHOP_TEST_URL_AUTHENTICATION_FAILURE',true); ?>').addClass('error');
                console.log(data);
            }
        }).always(function(){
            js('#jform_testurlauthentication').blur();
        })
        ;
        
        return false;
    };
    
    
</script>

<style type="text/css">
    #result_testurlauthentication{
        padding: 5px 0 0 15px;
        display: inline-block;
    }
    
    #result_testurlauthentication.success{
        color: green;
    }
    
    #result_testurlauthentication.error{
        color: red;
    }
</style>

<div class="diffusion-edit front-end-edit">
    <?php if (!empty($this->item->id)): ?>
        <h1><?php echo JText::_('COM_EASYSDI_SHOP_TITLE_EDIT_DIFFUSION'); ?></h1>
    <?php else: ?>
        <h1><?php echo JText::_('COM_EASYSDI_SHOP_TITLE_NEW_DIFFUSION'); ?></h1>
    <?php endif; ?>

    <form class="form-horizontal form-inline form-validate" action="<?php echo JRoute::_('index.php?option=com_easysdi_shop&task=diffusion.save'); ?>" method="post" id="adminForm" name="adminForm" enctype="multipart/form-data">

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
                                <fieldset id ="fieldset_perimeters" >
                                    <legend><?php echo JText::_('COM_EASYSDI_SHOP_FORM_FIELDSET_LEGEND_PERIMETERS'); ?></legend>
                                    <?php foreach ($this->orderperimeters as $orderperimeter): 
                                            if ($orderperimeter->id == 1){
                                                $orderperimeterlabel = JText::_('FREEPERIMETER');
                                            }elseif ($orderperimeter->id == 2){
                                                $orderperimeterlabel = JText::_('MYPERIMETER');
                                            }else {
                                                $orderperimeterlabel = $orderperimeter->name;
                                            }
?>
                                        <div class="control-group" >
                                            <div class="control-label">
                                                <label id="jform_perimeter<?php echo $orderperimeter->id; ?>-lbl" for="jform_perimeter<?php echo $orderperimeter->id; ?>" class="hasTip" title=""><?php echo $orderperimeterlabel; ?></label>
                                            </div>
                                            <div class="controls">
                                                <select id="jform_perimeter<?php echo $orderperimeter->id ?>" name="jform[perimeter][<?php echo $orderperimeter->id ?>]" class="inputbox input-xlarge perimeterselect"  >
                                                    <option value="-1" ><?php echo JText::_("COM_EASYSDI_SHOP_FORM_DONOT_DISPLAY_PERIMETER"); ?></option>
                                                    <option value="1" <?php if (array_key_exists($orderperimeter->id, $this->item->perimeter) && $this->item->perimeter[$orderperimeter->id] == 0) echo 'selected'; ?>><?php echo JText::_("COM_EASYSDI_SHOP_FORM_DO_DISPLAY_PERIMETER"); ?></option>
                                                    <option value="0" <?php if (array_key_exists($orderperimeter->id, $this->item->perimeter) && $this->item->perimeter[$orderperimeter->id] == 1) echo 'selected'; ?>><?php echo JText::_("COM_EASYSDI_SHOP_FORM_DO_DISPLAY_PERIMETER_WITH_BUFFER"); ?></option>
                                                </select>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>

                                </fieldset>
                                <fieldset id ="fieldset_properties" >
                                    <legend><?php echo JText::_('COM_EASYSDI_SHOP_FORM_FIELDSET_LEGEND_PROPERTIES'); ?></legend>
                                    <?php foreach ($this->properties as $property): ?>
                                        <div class="control-group" >
                                            <div class="control-label">
                                                <label id="jform_property<?php echo $property->id ?>-lbl" for="jform_property<?php echo $property->id ?>" class="hasTip" title=""><?php echo sdiMultilingual::getTranslation($property->guid); ?></label>
                                            </div>
                                            <div class="controls">
                                                <?php
                                                switch ($property->propertytype_id):
                                                    case 1:
                                                    case 2:
                                                    case 3:
                                                        ?>
                                                        <select id="jform_property<?php echo $property->id ?>" name="jform[property][<?php echo $property->id ?>][]" class="inputbox input-xlarge" multiple="multiple" >
                                                            <?php
                                                            foreach ($this->propertyvalues as $propertyvalue):
                                                                if ($propertyvalue->property_id == $property->id):
                                                                    ?>
                                                                    <option value="<?php echo $propertyvalue->id; ?>" <?php if (array_key_exists($property->id, $this->item->property) && in_array($propertyvalue->id, $this->item->property[$property->id])) echo 'selected'; ?>><?php echo sdiMultilingual::getTranslation($propertyvalue->guid); ?></option>
                                                                    <?php
                                                                endif;
                                                            endforeach;
                                                            ?>
                                                        </select>
                                                        <?php
                                                        break;
                                                    case 4:
                                                    case 5:
                                                    case 6 :
                                                        ?>
                                                        <select id="jform_property<?php echo $property->id ?>" name="jform[property][<?php echo $property->id ?>]" class="inputbox input-xlarge"  >
                                                            <option value="-1"><?php echo JText::_("COM_EASYSDI_SHOP_FORM_DONOT_DISPLAY_FIELD"); ?></option>
                                                            <?php
                                                            foreach ($this->propertyvalues as $propertyvalue):
                                                                if ($propertyvalue->property_id == $property->id):
                                                                    ?>
                                                                    <option value="<?php echo $propertyvalue->id; ?>" <?php if (array_key_exists($property->id, $this->item->property) && in_array($propertyvalue->id, $this->item->property[$property->id])) echo 'selected'; ?>><?php echo JText::_('COM_EASYSDI_SHOP_PROPERTYVALUE_LABEL'); ?></option>
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
