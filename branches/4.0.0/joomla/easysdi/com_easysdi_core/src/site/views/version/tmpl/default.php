<?php
/**
 * @version     4.0.0
 * @package     com_easysdi_core
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
// no direct access
defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');

?>
<?php if ($this->item) : ?>

    <div class="resource-edit front-end-edit">
        <?php if (!empty($this->item->id)): ?>
            <h1><?php echo JText::_('COM_EASYSDI_CORE_TITLE_EDIT_VERSION') . ' ' . $this->item->name; ?></h1>
        <?php endif; ?>
        <form class="form-horizontal form-inline form-validate" action="<?php echo JRoute::_('index.php?option=com_easysdi_core&task=resource.save'); ?>" method="post" id="adminForm" name="adminForm" enctype="multipart/form-data">

            <div class="row-fluid">
                <div >
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#details" data-toggle="tab"><?php echo JText::_('COM_EASYSDI_CORE_TAB_DETAILS'); ?></a></li>
                        <li><a href="#publishing" data-toggle="tab"><?php echo JText::_('COM_EASYSDI_CORE_TAB_PUBLISHING'); ?></a></li>
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
                            
                            <?php echo $this->getSearchToolbar(); ?>
                            
                            <?php echo $this->getToolbar(); ?>

                            
                        </div>

                        <div class="tab-pane" id="publishing">
                            <?php foreach ($this->form->getFieldset('publishing') as $field): ?>
                                <div class="control-group" id="<?php echo $field->fieldname; ?>">
                                    <div class="control-label"><?php echo $field->label; ?></div>
                                    <div class="controls"><?php echo $field->input; ?></div>
                                </div>
                            <?php endforeach; ?>
                            <?php if (!empty($this->item->modified)) : ?>
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
            <input type = "hidden" name = "option" value = "com_easysdi_core" />
            <?php echo JHtml::_('form.token'); ?>
        </form>

        

    </div>
    <?php
else:
    echo JText::_('COM_EASYSDI_CORE_ITEM_NOT_LOADED');
endif;
?>
