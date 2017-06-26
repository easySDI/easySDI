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

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('behavior.keepalive');

// Import CSS
$document = JFactory::getDocument();
$document->addStyleSheet(Juri::root(true) .'/components/com_easysdi_catalog/assets/css/easysdi_catalog.css?v=' . sdiFactory::getSdiFullVersion());
?>
<script type="text/javascript">
    js = jQuery.noConflict();
    js(document).ready(function() {
        onStereotypeChange()
    });

    Joomla.submitbutton = function(task)
    {
        if (task == 'attribute.cancel') {
            Joomla.submitform(task, document.getElementById('attribute-form'));
        }
        else {

            if (task != 'attribute.cancel' && document.formvalidator.isValid(document.id('attribute-form'))) {

                Joomla.submitform(task, document.getElementById('attribute-form'));
            }
            else {
                alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
            }
        }
    }

    function onStereotypeChange() {
        if (jQuery('#jform_stereotype_id').val() == 6) {
            jQuery("#type_isocode").show();
            jQuery("#codelist").show();
            jQuery("#length").show();
            jQuery("#pattern").show();
        }
        else if (jQuery('#jform_stereotype_id').val() == 11) {
            jQuery("#jform_listnamespace_id").val("");
            jQuery("#jform_type_isocode").val("");
            jQuery("#type_isocode").hide();
            jQuery("#jform_length").val("");
            jQuery("#length").hide();
            jQuery("#jform_pattern").val("");
            jQuery("#pattern").hide();
            jQuery("#jform_codelist").val("");
            jQuery("#codelist").hide();
        } else {
            jQuery("#jform_listnamespace_id").val("");
            jQuery("#jform_type_isocode").val("");
            jQuery("#type_isocode").hide();
            jQuery("#jform_codelist").val("");
            jQuery("#codelist").hide();
            jQuery("#length").show();
            jQuery("#pattern").show();
            
        }

    }
</script>

<form action="<?php echo JRoute::_('index.php?option=com_easysdi_catalog&layout=edit&id=' . (int) $this->item->id); ?>" method="post" enctype="multipart/form-data" name="adminForm" id="attribute-form" class="form-validate">
    <div class="row-fluid">
        <div class="span10 form-horizontal">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#details" data-toggle="tab"><?php echo empty($this->item->id) ? JText::_('COM_EASYSDI_CATALOG_TAB_NEW') : JText::sprintf('COM_EASYSDI_CATALOG_TAB_EDIT', $this->item->id); ?></a></li>
                <li><a href="#publishing" data-toggle="tab"><?php echo JText::_('COM_EASYSDI_CATALOG_TAB_PUBLISHING'); ?></a></li>
                <?php if (JFactory::getUser()->authorise('core.admin', 'easysdi_catalog')): ?>
                    <li><a href="#permissions" data-toggle="tab"><?php echo JText::_('COM_EASYSDI_CATALOG_TAB_RULES'); ?></a></li>
                <?php endif ?>
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="details">
                    <div class="control-group">
                        <div class="control-label"><?php echo $this->form->getLabel('name'); ?></div>
                        <div class="controls"><?php echo $this->form->getInput('name'); ?></div>
                    </div>
                    <div class="control-group">
                        <div class="control-label"><?php echo $this->form->getLabel('alias'); ?></div>
                        <div class="controls"><?php echo $this->form->getInput('alias'); ?></div>
                    </div>
                    <div class="control-group">
                        <div class="control-label"><?php echo $this->form->getLabel('description'); ?></div>
                        <div class="controls"><?php echo $this->form->getInput('description'); ?></div>
                    </div>
                    <div class="control-group">
                        <div class="control-label"><?php echo $this->form->getLabel('issystem'); ?></div>
                        <div class="controls"><?php echo $this->form->getInput('issystem'); ?></div>
                    </div>
                    <div class="control-group">
                        <div class="control-label"><?php echo $this->form->getLabel('isocode'); ?></div>
                        <div class="controls"><?php echo $this->form->getInput('namespace_id'); ?><?php echo $this->form->getInput('isocode'); ?></div>
                    </div>
                    <div class="control-group">
                        <div class="control-label"><?php echo $this->form->getLabel('stereotype_id'); ?></div>
                        <div class="controls"><?php echo $this->form->getInput('stereotype_id'); ?></div>
                    </div>
                    <div class="control-group" id="type_isocode">
                        <div class="control-label"><?php echo $this->form->getLabel('type_isocode'); ?></div>
                        <div class="controls"><?php echo $this->form->getInput('listnamespace_id'); ?><?php echo $this->form->getInput('type_isocode'); ?></div>
                    </div>
                    <div class="control-group" id="codelist">
                        <div class="control-label"><?php echo $this->form->getLabel('codelist'); ?></div>
                        <div class="controls"><?php echo $this->form->getInput('codelist'); ?></div>
                    </div>
                    <div class="control-group" id="length">
                        <div class="control-label"><?php echo $this->form->getLabel('length'); ?></div>
                        <div class="controls"><?php echo $this->form->getInput('length'); ?></div>
                    </div>
                    <div class="control-group" id="pattern">
                        <div class="control-label"><?php echo $this->form->getLabel('pattern'); ?></div>
                        <div class="controls"><?php echo $this->form->getInput('pattern'); ?></div>
                    </div>
                    <div class="well">
                        <div class="control-group">
                            <div class="control-label"><?php echo $this->form->getLabel('text1'); ?></div>
                            <div class="controls"><?php echo $this->form->getInput('text1'); ?></div>
                        </div>
                    </div>
                    <div class="well">
                        <div class="control-group">
                            <div class="control-label"><?php echo $this->form->getLabel('text2'); ?></div>
                            <div class="controls"><?php echo $this->form->getInput('text2'); ?></div>
                        </div>
                    </div>
                    <div class="control-group">
                        <div class="control-label"><?php echo $this->form->getLabel('id'); ?></div>
                        <div class="controls"><?php echo $this->form->getInput('id'); ?></div>
                    </div>
                </div>
                <div class="tab-pane" id="publishing">
                    <div class="control-group">
                        <div class="control-label"><?php echo $this->form->getLabel('created_by'); ?></div>
                        <div class="controls"><?php echo $this->form->getInput('created_by'); ?></div>
                    </div>
                    <div class="control-group">
                        <div class="control-label"><?php echo $this->form->getLabel('created'); ?></div>
                        <div class="controls"><?php echo $this->form->getInput('created'); ?></div>
                    </div>
                    <?php if ($this->item->modified_by) : ?>
                        <div class="control-group">
                            <div class="control-label"><?php echo $this->form->getLabel('modified_by'); ?></div>
                            <div class="controls"><?php echo $this->form->getInput('modified_by'); ?></div>
                        </div>
                        <div class="control-group">
                            <div class="control-label"><?php echo $this->form->getLabel('modified'); ?></div>
                            <div class="controls"><?php echo $this->form->getInput('modified'); ?></div>
                        </div>
                    <?php endif; ?>
                </div>
                <?php if (JFactory::getUser()->authorise('core.admin', 'easysdi_catalog')): ?>
                    <div class="tab-pane" id="permissions">
                        <fieldset>
                            <?php echo $this->form->getInput('rules'); ?>
                        </fieldset>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="clr"></div>

        <?php foreach ($this->form->getFieldset('hidden') as $field): ?>
            <?php echo $field->input; ?>
        <?php endforeach; ?>    
        <input type="hidden" name="task" value="" />
        <?php echo JHtml::_('form.token'); ?>

        <!-- Begin Sidebar -->
        <div class="span2">
            <h4><?php echo JText::_('JDETAILS'); ?></h4>
            <hr />
            <fieldset class="form-vertical">
                <div class="control-group">
                    <?php if (JFactory::getUser()->authorise('core.edit.state', 'easysdi_catalog')): ?>
                        <div class="control-label">
                            <?php echo $this->form->getLabel('state'); ?>
                        </div>
                        <div class="controls">
                            <?php echo $this->form->getInput('state'); ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="control-group">
                    <div class="control-label">
                        <?php echo $this->form->getLabel('access'); ?>
                    </div>
                    <div class="controls">
                        <?php echo $this->form->getInput('access'); ?>
                    </div>
                </div>
            </fieldset>
        </div>
        <!-- End Sidebar -->
    </div>
</form>