<?php
/**
 * @version     4.4.5
 * @package     com_easysdi_map
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
$document->addStyleSheet('components/com_easysdi_map/assets/css/easysdi_map.css?v=' . sdiFactory::getSdiFullVersion());
$document->addScript('components/com_easysdi_map/views/layer/tmpl/edit.js?v=' . sdiFactory::getSdiFullVersion());
$document->addScript(Juri::root(true) . '/components/com_easysdi_core/libraries/easysdi/view/view.js?v=' . sdiFactory::getSdiFullVersion());
?>
<script type="text/javascript">
    js = jQuery.noConflict();
    js(document).ready(function() {
        enableAccessScope();        
    });
    
    Joomla.submitbutton = function(task)
    {
        if (task == 'layer.cancel' || document.formvalidator.isValid(document.id('layer-form'))) {
            Joomla.submitform(task, document.getElementById('layer-form'));
        }
        else {
            alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
        }
    }
</script>

<form action="<?php echo JRoute::_('index.php?option=com_easysdi_map&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="layer-form" class="form-validate">
    <div id="progress">
        <img id="progress_image"  src="components/com_easysdi_service/assets/images/loader.gif" alt="">
    </div>
    <div class="row-fluid">

        <div class="span10 form-horizontal">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#details" data-toggle="tab"><?php echo empty($this->item->id) ? JText::_('COM_EASYSDI_MAP_TAB_NEW_LAYER') : JText::sprintf('COM_EASYSDI_MAP_TAB_EDIT_LAYER', $this->item->id); ?></a></li>
                <li><a href="#asOL" data-toggle="tab"><?php echo JText::_('COM_EASYSDI_MAP_TAB_ASOL'); ?></a></li>
                <li><a href="#indoor" data-toggle="tab"><?php echo JText::_('COM_EASYSDI_MAP_TAB_INDOOR_NAVIGATION'); ?></a></li>
                <li><a href="#publishing" data-toggle="tab"><?php echo JText::_('COM_EASYSDI_MAP_TAB_PUBLISHING'); ?></a></li>
                <?php if ($this->canDo->get('core.admin')): ?>
                    <li><a href="#permissions" data-toggle="tab"><?php echo JText::_('COM_EASYSDI_MAP_TAB_RULES'); ?></a></li>
                <?php endif ?>
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
                </div>
                <div class="tab-pane" id="asOL">
                    <div  class="control-group" id="WMTS-info">
                        <?php echo JText::_("COM_EASYSDI_MAP_FORM_LBL_LAYER_WMTS_ASOL"); ?>
                    </div>
                    <div class="control-group">
                        <div class="control-label"><?php echo $this->form->getLabel('asOL'); ?></div>
                        <div class="controls"><?php echo $this->form->getInput('asOL'); ?></div>
                    </div>
                    <div class="control-group">
                        <div class="control-label"><?php echo $this->form->getLabel('asOLstyle'); ?></div>
                        <div class="controls"><?php echo $this->form->getInput('asOLstyle'); ?></div>
                    </div>
                    <div class="control-group">
                        <div class="control-label"><?php echo $this->form->getLabel('asOLmatrixset'); ?></div>
                        <div class="controls"><?php echo $this->form->getInput('asOLmatrixset'); ?></div>
                    </div>
                    <div class="control-group">
                        <div class="control-label"><?php echo $this->form->getLabel('asOLoptions'); ?></div>
                        <div class="controls"><?php echo $this->form->getInput('asOLoptions'); ?></div>
                    </div>
                </div>
                <div class="tab-pane" id="indoor">
                    <?php foreach ($this->form->getFieldset('indoornavigation') as $field): ?>
                         <div class="control-group" id="<?php echo $field->fieldname; ?>">
                            <div class="control-label"><?php echo $field->label; ?></div>
                            <div class="controls"><?php echo $field->input; ?></div>
                        </div>
                    <?php endforeach; ?>
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
                <?php if ($this->canDo->get('core.admin')): ?>
                    <div class="tab-pane" id="permissions">
                        <fieldset>
                            <?php echo $this->form->getInput('rules'); ?>
                        </fieldset>
                    </div>
                <?php endif; ?>
            </div>
            <!-- End Tabs -->
        </div>

        <input type="hidden" name="task" value="" />
        <?php echo JHtml::_('form.token'); ?>

        <!-- Begin Sidebar -->
        <div class="span2">
            <h4><?php echo JText::_('JDETAILS'); ?></h4>
            <hr />
            <fieldset class="form-vertical">
                <div class="control-group">
                    <div class="control-group">
                        <div class="controls">
                            <?php echo $this->form->getValue('group'); ?>
                        </div>
                    </div>
                    <?php
                    if ($this->canDo->get('core.edit.state')) {
                        ?>
                        <div class="control-label">
                        <?php echo $this->form->getLabel('state'); ?>
                        </div>
                        <div class="controls">
    <?php echo $this->form->getInput('state'); ?>
                        </div>
                            <?php
                        }
                        ?>
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
