<?php
/**
 * @version     4.0.0
 * @package     com_easysdi_map
 * @copyright   Copyright (C) 2013. All rights reserved.
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

<div class="map-edit front-end-edit">
    <?php if(!empty($this->item->id)): ?>
        <h1>Edit <?php echo $this->item->id; ?></h1>
    <?php else: ?>
        <h1>Add</h1>
    <?php endif; ?>

    <form id="form-map" action="<?php echo JRoute::_('index.php?option=com_easysdi_map&task=map.save'); ?>" method="post" class="form-validate" enctype="multipart/form-data">
        <ul>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('id'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('id'); ?></div>
			</div>
				<input type="hidden" name="jform[guid]" value="<?php echo $this->item->guid; ?>" />
				<input type="hidden" name="jform[alias]" value="<?php echo $this->item->alias; ?>" />
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('created'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('created'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('created_by'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('created_by'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('modified_by'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('modified_by'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('modified'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('modified'); ?></div>
			</div>

			<div class="control-group">
				<?php $canState = false; ?>
					<?php $canState = $canState = JFactory::getUser()->authorise('core.edit.state','com_easysdi_map'); ?>				<?php if(!$canState): ?>
				<div class="control-label"><?php echo $this->form->getLabel('state'); ?></div>
					<?php
						$state_string = 'Unpublish';
						$state_value = 0;
						if($this->item->state == 1):
							$state_string = 'Publish';
							$state_value = 1;
						endif;
					?>
					<div class="controls"><?php echo $state_string; ?></div>
					<input type="hidden" name="jform[state]" value="<?php echo $state_value; ?>" />
				<?php else: ?>
					<div class="control-label"><?php echo $this->form->getLabel('state'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('state'); ?></div>					<?php endif; ?>
				</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('name'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('name'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('srs'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('srs'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('unit_id'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('unit_id'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('centercoordinates'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('centercoordinates'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('maxresolution'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('maxresolution'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('maxextent'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('maxextent'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('abstract'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('abstract'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('access'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('access'); ?></div>
			</div>
				<input type="hidden" name="jform[asset_id]" value="<?php echo $this->item->asset_id; ?>" />

        </ul>
        
		<div>
			<button type="submit" class="validate"><span><?php echo JText::_('JSUBMIT'); ?></span></button>
			<?php echo JText::_('or'); ?>
			<a href="<?php echo JRoute::_('index.php?option=com_easysdi_map&task=map.cancel'); ?>" title="<?php echo JText::_('JCANCEL'); ?>"><?php echo JText::_('JCANCEL'); ?></a>

			<input type="hidden" name="option" value="com_easysdi_map" />
			<input type="hidden" name="task" value="mapform.save" />
			<?php echo JHtml::_('form.token'); ?>
		</div>
	</form>
</div>
