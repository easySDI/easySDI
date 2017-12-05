<?php
/**
 * @version     4.4.5
 * @package     com_easysdi_shop
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


$document = JFactory::getDocument();
$document->addScript('components/com_easysdi_core/libraries/easysdi/view/view.js?v=' . sdiFactory::getSdiFullVersion());
$document->addScript('components/com_easysdi_shop/views/diffusion/tmpl/diffusion.js?v=' . sdiFactory::getSdiFullVersion());
JText::script('COM_EASYSDI_SHOP_FORM_MSG_DIFFUSION_DOWNLOAD_DISABLED_WITH_PAID');
?>

<script type="text/javascript">
    var msgNoPerimeter = '<?php echo $this->escape(JText::_('COM_EASYSDI_SHOP_FORM_MSG_DIFFUSION_NO_PERIMETER_SELECTED', true)); ?>';
    var msgFormValidationFailed = '<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED', true)); ?>';
    var urlProfiles = "<?php echo JRoute::_('index.php?option=com_easysdi_shop&task=diffusion.getAvailableProfiles') ?>";
    var version = <?php echo $this->item->version_id; ?>;
    var freePerimeter = <?php
if (!$this->isDiffusionManager)
    echo 'true';
else
    echo 'false';
?>;
    var testOk = '<?php echo JText::_('COM_EASYSDI_SHOP_TEST_URL_AUTHENTICATION_OK', true); ?>';
    var testKo = '<?php echo JText::_('COM_EASYSDI_SHOP_TEST_URL_AUTHENTICATION_FAILURE', true); ?>';
    var sdiPricingFreeVal = <?php echo Easysdi_shopHelper::PRICING_FREE; ?>;
    var sdiPricingActivated = <?php echo $this->pricingisActivated ? 'true' : 'false' ?>;
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
                                <?php
                                foreach ($this->form->getFieldset('download') as $field):
                                    if ($field->fieldname == 'hasdownload') {
                                        continue;
                                    }
                                    if ($field->fieldname == 'file' && !empty($this->item->file)) {
                                        ?>
                                        <div class="control-group" id="existingfile">
                                            <div class="control-label">
                                                <label id="jform_file_hidden_href-lbl" 
                                                       for="jform_file_hidden_href" 
                                                       class="hasPopover" 
                                                       title="<?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_DIFFUSION_EXISTINGFILE'); ?>"
                                                       >
                                                           <?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_DIFFUSION_EXISTINGFILE'); ?>
                                                </label>
                                            </div>
                                            <div class="controls">
                                                <p>
                                                    <a id="jform_file_hidden_href" href="<?php echo Juri::base(true) . "/component/easysdi_shop/download/download?id=" . $this->item->id; ?>"><?php echo '[' . substr($this->item->file, 33) . ']'; ?></a>
                                                </p>                                            
                                            </div>
                                        </div>
                                        <?php
                                    }
                                    ?>                                
                                    <div class="control-group" id="<?php echo $field->fieldname; ?>">
                                        <div class="control-label"><?php echo $field->label; ?></div>
                                        <div class="controls">
                                            <?php echo $field->input; ?>
                                            <?php if ($field->fieldname == 'file'): ?>
                                                <input type="hidden" name="jform[file]" id="jform_file_hidden" value="<?php echo $this->item->file ?>" />
                                            <?php endif; ?>                                              
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </fieldset>
                        <fieldset id ="fieldset_extraction">
                            <legend><?php echo JText::_('COM_EASYSDI_SHOP_FORM_FIELDSET_LEGEND_EXTRACTION'); ?>
                                <?php echo $this->form->getInput('hasextraction'); ?></legend>
                            <div id="div_extraction">
                                <?php
                                foreach ($this->form->getFieldset('extraction') as $field):
                                    if (in_array($field->fieldname, array('hasextraction', 'restrictedperimeter', 'otp')))
                                        continue;
                                    ?>
                                    <div class="control-group" id="<?php echo $field->fieldname; ?>">
                                        <div class="control-label"><?php echo $field->label; ?></div>
                                        <div class="controls">
                                            <?php echo $field->input; ?>
                                            <?php if ($field->fieldname == 'deposit'): ?>
                                                <?php if (!empty($this->item->deposit)): ?>
                                                    <a id="jform_deposit_hidden_href" href="<?php echo JRoute::_($this->params->get('depositFolder') . '/' . $this->item->deposit, false); ?>"><?php echo '[' . substr($this->item->deposit, 33) . ']'; ?></a>
                                                <?php endif; ?>
                                                <input type="hidden" name="jform[deposit]" id="jform_deposit_hidden" value="<?php echo $this->item->deposit ?>" />
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>

                                <?php if ($this->params->get('userperimeteractivated') == 1) : ?>
                                    <div class="control-group" id="<?php echo $this->form->getField('restrictedperimeter')->fieldname; ?>">
                                        <div class="control-label"><?php echo $this->form->getField('restrictedperimeter')->label; ?></div>
                                        <div class="controls"><?php echo $this->form->getField('restrictedperimeter')->input; ?></div>
                                    </div>
                                <?php endif; ?>

                                <?php if ($this->params->get('otpactivated', 0) == 1) : ?>
                                    <div class="control-group" id="<?php echo $this->form->getField('otp')->fieldname; ?>">
                                        <div class="control-label"><?php echo $this->form->getField('otp')->label; ?></div>
                                        <div class="controls"><?php echo $this->form->getField('otp')->input; ?></div>
                                    </div>
                                <?php endif; ?>

                                <fieldset id ="fieldset_perimeters" >
                                    <legend><?php echo JText::_('COM_EASYSDI_SHOP_FORM_FIELDSET_LEGEND_PERIMETERS'); ?></legend>

                                    <?php
                                    foreach ($this->orderperimeters as $orderperimeter):
                                        if ($orderperimeter->id == 2 && $this->params->get('userperimeteractivated') != 1) {
                                            continue;
                                        }
                                        if ($orderperimeter->id == 1) {
                                            $orderperimeterlabel = JText::_('FREEPERIMETER');
                                        } elseif ($orderperimeter->id == 2) {
                                            $orderperimeterlabel = JText::_('MYPERIMETER');
                                        } else {
                                            $orderperimeterlabel = $orderperimeter->name;
                                        }
                                        ?>
                                        <div class="control-group" >
                                            <div class="control-label">
                                                <label id="jform_perimeter<?php echo $orderperimeter->id; ?>-lbl" for="jform_perimeter<?php echo $orderperimeter->id; ?>"><?php echo $orderperimeterlabel; ?></label>                                                
                                            </div>
                                            <div class="controls">
                                                <fieldset id="jform_perimeter<?php echo $orderperimeter->id ?>" class="radio btn-group btn-group-yesno">
                                                    <input type="radio" id="jform_perimeter<?php echo $orderperimeter->id; ?>_0" name="jform[perimeter][<?php echo $orderperimeter->id ?>]" value="0" <?php if (!in_array($orderperimeter->id, $this->item->perimeter)): ?>checked="checked"<?php endif; ?> <?php if (!$this->isDiffusionManager): ?>disabled="disabled"<?php endif; ?>>
                                                    <label for="jform_perimeter<?php echo $orderperimeter->id ?>_0" <?php if (!$this->isDiffusionManager): ?>disabled="disabled"<?php endif; ?>><?php echo JText::_('JNO'); ?></label>                                            
                                                    <input type="radio" id="jform_perimeter<?php echo $orderperimeter->id; ?>_1" name="jform[perimeter][<?php echo $orderperimeter->id ?>]" value="1" <?php if (in_array($orderperimeter->id, $this->item->perimeter)): ?>checked="checked"<?php endif; ?> <?php if (!$this->isDiffusionManager): ?>disabled="disabled"<?php endif; ?>>
                                                    <label for="jform_perimeter<?php echo $orderperimeter->id ?>_1" <?php if (!$this->isDiffusionManager): ?>disabled="disabled"<?php endif; ?>><?php echo JText::_('JYES'); ?></label>
                                                </fieldset>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>

                                    <?php foreach ($this->form->getFieldset('perimeters_params') as $field): ?>
                                        <div class="control-group" id="<?php echo $field->fieldname; ?>">
                                            <div class="control-label"><?php echo $field->label; ?></div>
                                            <div class="controls"><?php echo $field->input; ?></div>
                                        </div>
                                    <?php endforeach; ?>

                                </fieldset>
                                <fieldset id ="fieldset_properties" >
                                    <legend><?php echo JText::_('COM_EASYSDI_SHOP_FORM_FIELDSET_LEGEND_PROPERTIES'); ?></legend>
                                    <?php foreach ($this->properties as $property): ?>
                                        <div class="control-group" >
                                            <div class="control-label">
                                                <label id="jform_property<?php echo $property->id ?>-lbl" for="jform_property<?php echo $property->id ?>" ><?php echo sdiMultilingual::getTranslation($property->guid); ?></label>
                                            </div>
                                            <div class="controls">
                                                <?php
                                                switch ($property->propertytype_id):
                                                    case 1:
                                                    case 2:
                                                    case 3:
                                                        ?>
                                                        <select id="jform_property<?php echo $property->id ?>" name="jform[property][<?php echo $property->id ?>][]" class="inputbox input-xlarge" multiple="multiple"  <?php if (!$this->isDiffusionManager): ?>disabled="disabled"<?php endif; ?>>
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
                                                        <select id="jform_property<?php echo $property->id ?>" name="jform[property][<?php echo $property->id ?>]" class="inputbox input-xlarge"  <?php if (!$this->isDiffusionManager): ?>disabled="disabled"<?php endif; ?> >
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
