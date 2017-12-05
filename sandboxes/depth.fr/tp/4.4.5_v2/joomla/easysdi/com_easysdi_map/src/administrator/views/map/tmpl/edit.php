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
$document->addScript('components/com_easysdi_map/views/map/tmpl/edit.js?v=' . sdiFactory::getSdiFullVersion());
$document->addScript(JURI::root() . '/components/com_easysdi_core/libraries/tablednd/jquery.tablednd.0.7.min.js');
?>
<script type="text/javascript">
    js = jQuery.noConflict();
    js(document).ready(function() {

        js("#tab-dyn").tableDnD();


    });
    Joomla.submitbutton = function(task)
    {
        if (task == 'map.cancel' || document.formvalidator.isValid(document.id('map-form'))) {
            Joomla.submitform(task, document.getElementById('map-form'));
        }
        else {
            alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
        }
    }
</script>

<form action="<?php echo JRoute::_('index.php?option=com_easysdi_map&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="map-form" class="form-validate">

    <div class="row-fluid">
        <div class="span10 form-horizontal">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#details" data-toggle="tab"><?php echo empty($this->item->id) ? JText::_('COM_EASYSDI_MAP_TAB_NEW_MAP') : JText::sprintf('COM_EASYSDI_MAP_TAB_EDIT_MAP', $this->item->id); ?></a></li>
                <li><a href="#tools" data-toggle="tab"><?php echo JText::_('COM_EASYSDI_MAP_TAB_TOOLS'); ?></a></li>
                <li><a href="#publishing" data-toggle="tab"><?php echo JText::_('COM_EASYSDI_MAP_TAB_PUBLISHING'); ?></a></li>
                <?php if ($this->canDo->get('core.admin')): ?>
                    <li><a href="#permissions" data-toggle="tab"><?php echo JText::_('COM_EASYSDI_MAP_TAB_RULES'); ?></a></li>
                <?php endif ?>
            </ul>

            <div class="tab-content">
                <!-- Begin Tabs -->
                <div class="tab-pane active" id="details">
                    <?php foreach ($this->form->getFieldset('details') as $field): ?>
                        <div class="control-group">
                            <div class="control-label"><?php echo $field->label; ?></div>
                            <div class="controls"><?php echo $field->input; ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="tab-pane" id="tools">
                    <div class="span10 form-horizontal">
                        <ul class="nav nav-tabs">
                            <li class="active"><a href="#misc" data-toggle="tab"><?php echo JText::_('COM_EASYSDI_MAP_TAB_TOOLS_MISC'); ?></a></li>
                            <li><a href="#scale" id="scaletab" data-toggle="tab"><?php echo JText::_('COM_EASYSDI_MAP_TAB_TOOLS_SCALE'); ?></a></li>
                            <li><a href="#wfs" id="wfstab" data-toggle="tab"><?php echo JText::_('COM_EASYSDI_MAP_TAB_TOOLS_WFS'); ?></a></li>
                            <li><a href="#indoor" id="indoortab" data-toggle="tab"><?php echo JText::_('COM_EASYSDI_MAP_TAB_TOOLS_INDOOR'); ?></a></li>                            
                        </ul>

                        <div class="tab-content">
                            <!-- Begin Tabs -->
                            <div class="tab-pane active" id="misc">
                                <?php foreach ($this->form->getFieldset('toolsstate') as $field): ?>
                                    <div class="control-group">
                                        <div class="control-label"><?php echo $field->label; ?></div>
                                        <div class="row controls form-inline"><span><?php echo $field->input; ?></span>
                                            <?php if ($field->fieldname == 'tool17') : ?>
                                                <span><?php echo $this->form->getField('catalog_id')->input; ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <div class="tab-pane" id="scale">                                
                                <h5><?php echo JText::_('COM_EASYSDI_MAP_FORM_TITLE_TOOLS_SCALE'); ?></h5>
                                <p><?php echo JText::_('COM_EASYSDI_MAP_FORM_TEXT_TOOLS_SCALE'); ?></p>
                                <br />
                                <?php foreach ($this->form->getFieldset('scaleline') as $field): ?>
                                    <div class="control-group">
                                        <div class="control-label"><?php echo $field->label; ?></div>
                                        <div class="controls"><?php echo $field->input; ?></div>
                                    </div>

                                <?php endforeach; ?>

                            </div>
                            <div class="tab-pane" id="wfs">
                                <h5><?php echo JText::_('COM_EASYSDI_MAP_FORM_TITLE_TOOLS_WFS'); ?></h5>
                                <p><?php echo JText::_('COM_EASYSDI_MAP_FORM_TEXT_TOOLS_WFS'); ?></p>
                                <br />
                                <?php foreach ($this->form->getFieldset('wfslocator') as $field): ?>
                                    <div class="control-group">
                                        <div class="control-label"><?php echo $field->label; ?></div>
                                        <div class="controls"><?php echo $field->input; ?></div>
                                    </div>
                                <?php endforeach; ?>

                            </div>
                            <div class="tab-pane" id="indoor">
                                <h5><?php echo JText::_('COM_EASYSDI_MAP_FORM_TITLE_TOOLS_INDOOR'); ?></h5>
                                <p><?php echo JText::_('COM_EASYSDI_MAP_FORM_TEXT_TOOLS_INDOOR'); ?></p>
                                <br />
                                <div class="control-group">
                                    <div class="control-label"><?php echo $this->form->getLabel('tool21'); ?></div>
                                    <div class="controls"><?php echo $this->form->getInput('tool21'); ?></div>
                                </div>
                               
                                    <div><?php echo $this->form->getInput('levellabel'); ?></div>
                               
                            </div>
                        </div>
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
                <?php if ($this->canDo->get('core.admin')): ?>
                    <div class="tab-pane" id="permissions">
                        <fieldset>
                            <?php echo $this->form->getInput('rules'); ?>
                        </fieldset>
                    </div>
                <?php endif; ?>
            </div>
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
                            <?php echo $this->form->getValue('name'); ?>
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


