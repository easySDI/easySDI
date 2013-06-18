<?php
/**
 * @version     4.0.0
 * @package     com_easysdi_catalog
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org§> - http://www.easysdi.org
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
$document->addStyleSheet('components/com_easysdi_catalog/assets/css/easysdi_catalog.css');
?>
<script type="text/javascript">
    js = jQuery.noConflict();
    js(document).ready(function(){
        
    });
    
    Joomla.submitbutton = function(task)
    {
        if(task == 'profile.cancel'){
            Joomla.submitform(task, document.getElementById('profile-form'));
        }
        else{
            
            if (task != 'profile.cancel' && document.formvalidator.isValid(document.id('profile-form'))) {
                
                Joomla.submitform(task, document.getElementById('profile-form'));
            }
            else {
                alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
            }
        }
    }
</script>

<form action="<?php echo JRoute::_('index.php?option=com_easysdi_catalog&layout=edit&id=' . (int) $this->item->id); ?>" method="post" enctype="multipart/form-data" name="adminForm" id="profile-form" class="form-validate">
    <div class="row-fluid">
        <div class="span10 form-horizontal">
             <ul class="nav nav-tabs">
                <li class="active"><a href="#details" data-toggle="tab"><?php echo empty($this->item->id) ? JText::_('COM_EASYSDI_CATALOG_TAB_NEW_BOUNDARY') : JText::sprintf('COM_EASYSDI_CATALOG_TAB_EDIT_BOUNDARY', $this->item->id); ?></a></li>
                <li><a href="#publishing" data-toggle="tab"><?php echo JText::_('COM_EASYSDI_CATALOG_TAB_PUBLISHING'); ?></a></li>
                 <?php if (JFactory::getUser()->authorise('core.admin','easysdi_catalog')): ?>
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
				<div class="control-label"><?php echo $this->form->getLabel('class_id'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('class_id'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('metadataidentifier'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('metadataidentifier'); ?></div>
                    <div class="well">
                        <?php echo $this->form->getInput('text1'); ?>
                    </div>
                    <div class="control-group">
                        <div class="control-label"><?php echo $this->form->getLabel('id'); ?></div>
                        <div class="controls"><?php echo $this->form->getInput('id'); ?></div>
                    </div>
                    
                    <?php
                    foreach ($this->form->getFieldset('hidden') as $field):
                        ?>
                        <div class="controls"><?php echo $field->input; ?></div>

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
                <?php if (JFactory::getUser()->authorise('core.admin','easysdi_catalog')): ?>
                    <div class="tab-pane" id="permissions">
                        <fieldset>
                            <?php echo $this->form->getInput('rules'); ?>
                        </fieldset>
                    </div>
                <?php endif; ?>
            </div>
        </div>
            <fieldset class="adminform">

                			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('id'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('id'); ?></div>
			</div>
			
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('alias'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('alias'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('created_by'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('created_by'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('created'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('created'); ?></div>
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
				<div class="control-label"><?php echo $this->form->getLabel('state'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('state'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('name'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('name'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('description'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('description'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('class_id'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('class_id'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('metadataidentifier'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('metadataidentifier'); ?></div>
			</div>


            </fieldset>
        </div>

        <div class="clr"></div>

<?php if (JFactory::getUser()->authorise('core.admin','easysdi_catalog')): ?>
	<div class="fltlft" style="width:86%;">
		<fieldset class="panelform">
			<?php echo JHtml::_('sliders.start', 'permissions-sliders-'.$this->item->id, array('useCookie'=>1)); ?>
			<?php echo JHtml::_('sliders.panel', JText::_('ACL Configuration'), 'access-rules'); ?>
			<?php echo $this->form->getInput('rules'); ?>
			<?php echo JHtml::_('sliders.end'); ?>
		</fieldset>
	</div>
<?php endif; ?>

        <input type="hidden" name="task" value="" />
        <?php echo JHtml::_('form.token'); ?>

    </div>
</form>