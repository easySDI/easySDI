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

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('behavior.keepalive');

// Import CSS
$document = JFactory::getDocument();
$document->addStyleSheet('components/com_easysdi_shop/assets/css/easysdi_shop.css');
?>
<script type="text/javascript">
    js = jQuery.noConflict();
    js(document).ready(function(){
        
    });
    
    Joomla.submitbutton = function(task)
    {
        if(task == 'diffusion.cancel'){
            Joomla.submitform(task, document.getElementById('diffusion-form'));
        }
        else{
            
				js = jQuery.noConflict();
				if(js('#jform_deposit').val() != ''){
					js('#jform_deposit_hidden').val(js('#jform_deposit').val());
				}
				js = jQuery.noConflict();
				if(js('#jform_file').val() != ''){
					js('#jform_file_hidden').val(js('#jform_file').val());
				}
            if (task != 'diffusion.cancel' && document.formvalidator.isValid(document.id('diffusion-form'))) {
                
                Joomla.submitform(task, document.getElementById('diffusion-form'));
            }
            else {
                alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
            }
        }
    }
</script>

<form action="<?php echo JRoute::_('index.php?option=com_easysdi_shop&layout=edit&id=' . (int) $this->item->id); ?>" method="post" enctype="multipart/form-data" name="adminForm" id="diffusion-form" class="form-validate">
    <div class="row-fluid">
        <div class="span10 form-horizontal">
            <fieldset class="adminform">

                			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('id'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('id'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('guid'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('guid'); ?></div>
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
				<div class="control-label"><?php echo $this->form->getLabel('version_id'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('version_id'); ?></div>
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
				<div class="control-label"><?php echo $this->form->getLabel('accessscope_id'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('accessscope_id'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('pricing_id'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('pricing_id'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('deposit'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('deposit'); ?></div>
			</div>

				<?php if (!empty($this->item->deposit)) : ?>
						<a href="<?php echo JRoute::_(JUri::base() . 'components' . DIRECTORY_SEPARATOR . 'com_easysdi_shop' . DIRECTORY_SEPARATOR . '/' .DIRECTORY_SEPARATOR . $this->item->deposit, false);?>">[View File]</a>
				<?php endif; ?>
				<input type="hidden" name="jform[deposit]" id="jform_deposit_hidden" value="<?php echo $this->item->deposit ?>" />			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('productmining_id'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('productmining_id'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('surfacemin'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('surfacemin'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('surfacemax'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('surfacemax'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('productstorage_id'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('productstorage_id'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('file'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('file'); ?></div>
			</div>

				<?php if (!empty($this->item->file)) : ?>
						<a href="<?php echo JRoute::_(JUri::base() . 'components' . DIRECTORY_SEPARATOR . 'com_easysdi_shop' . DIRECTORY_SEPARATOR . '/' .DIRECTORY_SEPARATOR . $this->item->file, false);?>">[View File]</a>
				<?php endif; ?>
				<input type="hidden" name="jform[file]" id="jform_file_hidden" value="<?php echo $this->item->file ?>" />			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('fileurl'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('fileurl'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('grid_id'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('grid_id'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('access'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('access'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('asset_id'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('asset_id'); ?></div>
			</div>


            </fieldset>
        </div>

        <div class="clr"></div>

<?php if (JFactory::getUser()->authorise('core.admin','easysdi_shop')): ?>
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