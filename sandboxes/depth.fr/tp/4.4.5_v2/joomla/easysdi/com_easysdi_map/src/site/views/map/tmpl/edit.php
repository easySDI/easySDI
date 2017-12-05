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

JHtml::_('behavior.keepalive');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');

//Load admin language file
$lang = JFactory::getLanguage();
$lang->load('com_easysdi_map', JPATH_ADMINISTRATOR);


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
        display: inline;
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
</style>

<div class="context-edit front-end-edit">
    <h1>Edit <?php echo $this->item->id; ?></h1>

    <form id="form-context" action="<?php echo JRoute::_('index.php?option=com_easysdi_map&task=context.save'); ?>" method="post" class="form-validate" enctype="multipart/form-data">
        <ul>
				<li><?php echo $this->form->getLabel('id'); ?>
				<?php echo $this->form->getInput('id'); ?></li>
				<li><?php echo $this->form->getLabel('created'); ?>
				<?php echo $this->form->getInput('created'); ?></li>
				<li><?php echo $this->form->getLabel('created_by'); ?>
				<?php echo $this->form->getInput('created_by'); ?></li>
				<li><?php echo $this->form->getLabel('modified_by'); ?>
				<?php echo $this->form->getInput('modified_by'); ?></li>
				<li><?php echo $this->form->getLabel('modified'); ?>
				<?php echo $this->form->getInput('modified'); ?></li>
				<li><?php echo $this->form->getLabel('state'); ?>
				<?php echo $this->form->getInput('state'); ?></li>
				<li><?php echo $this->form->getLabel('name'); ?>
				<?php echo $this->form->getInput('name'); ?></li>
				<li><?php echo $this->form->getLabel('srs'); ?>
				<?php echo $this->form->getInput('srs'); ?></li>
				<li><?php echo $this->form->getLabel('unit_id'); ?>
				<?php echo $this->form->getInput('unit_id'); ?></li>
				<li><?php echo $this->form->getLabel('centercoordinates'); ?>
				<?php echo $this->form->getInput('centercoordinates'); ?></li>
				<li><?php echo $this->form->getLabel('maxresolution'); ?>
				<?php echo $this->form->getInput('maxresolution'); ?></li>
				<li><?php echo $this->form->getLabel('maxextent'); ?>
				<?php echo $this->form->getInput('maxextent'); ?></li>
				<li><?php echo $this->form->getLabel('abstract'); ?>
				<?php echo $this->form->getInput('abstract'); ?></li>
				<li><?php echo $this->form->getLabel('access'); ?>
				<?php echo $this->form->getInput('access'); ?></li>

        </ul>
		<div>
			<button type="submit" class="validate"><span><?php echo JText::_('JSUBMIT'); ?></span></button>
			<?php echo JText::_('or'); ?>
			<a href="<?php echo JRoute::_('index.php?option=com_easysdi_map&task=context.cancel'); ?>" title="<?php echo JText::_('JCANCEL'); ?>"><?php echo JText::_('JCANCEL'); ?></a>

			<input type="hidden" name="option" value="com_easysdi_map" />
			<input type="hidden" name="task" value="context.save" />
			<?php echo JHtml::_('form.token'); ?>
		</div>
	</form>
</div>
