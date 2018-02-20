<?php

/**
 * @version     4.4.5
 * @package     com_easysdi_contact
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */

// no direct access
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('behavior.keepalive');

// Import CSS
$document = JFactory::getDocument();
$document->addStyleSheet('components/com_easysdi_contact/assets/css/easysdi_contact.css?v=' . sdiFactory::getSdiFullVersion());
?>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'category.cancel' || document.formvalidator.isValid(document.id('category-form'))) {
			Joomla.submitform(task, document.getElementById('category-form'));
		}
		else {
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
	}

	function disableAddressType(disable, type)
	{
		var elem = document.getElementById('category-form').elements;
        for(var i = 0; i < elem.length; i++)
        {
        	var tofind = 'jform['+type+'_';
        	if(elem[i].getAttribute('name') != null ){
	            if(		   elem[i].getAttribute('name').indexOf(tofind) != -1 
	    	            && elem[i].getAttribute('name').indexOf('sameascontact') == -1 
	    	            && elem[i].getAttribute('type') != 'hidden' )
	            {
		            elem[i].disabled = disable;
		            elem[i].value = ''; 
	            }
            }
        } 
	}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_easysdi_contact&layout=edit&id='.(int) $this->item->id); ?>" method="post" enctype="multipart/form-data" name="adminForm" id="category-form" class="form-validate">
	<div class="row-fluid">
		<div class="span10 form-horizontal">
		
		<ul class="nav nav-tabs">
				<li class="active"><a href="#details" data-toggle="tab"><?php echo empty($this->item->id) ? JText::_('COM_EASYSDI_CONTACT_TAB_NEW_CATEGORY') : JText::sprintf('COM_EASYSDI_CONTACT_TAB_EDIT_CATEGORY', $this->item->id); ?></a></li>
				<li><a href="#publishing" data-toggle="tab"><?php echo JText::_('COM_EASYSDI_CONTACT_TAB_PUBLISHING');?></a></li>
				<?php if ($this->canDo->get('core.admin')): ?>
				<li><a href="#permissions" data-toggle="tab"><?php echo JText::_('COM_EASYSDI_CONTACT_TAB_RULES');?></a></li>
			<?php endif ?>
			</ul>
				
            <div class="tab-content">
					<!-- Begin Tabs -->
					<div class="tab-pane active" id="details">
						<?php foreach($this->form->getFieldset('details') as $field): ?>
							<div class="control-group">
								<div class="control-label"><?php echo $field->label; ?></div>
								<div class="controls"><?php echo $field->input;?> <?php if($field->fieldname == 'overall_fee') echo $this->currency; ?></div>
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
			<h4><?php echo JText::_('JDETAILS');?></h4>
			<hr />
			<fieldset class="form-vertical">
				<div class="control-group">
					<div class="control-group">
						<div class="controls">
							<?php echo $this->form->getValue('name'); ?>
						</div>
					</div>
					<?php
					if($this->canDo->get('core.edit.state'))
					{
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