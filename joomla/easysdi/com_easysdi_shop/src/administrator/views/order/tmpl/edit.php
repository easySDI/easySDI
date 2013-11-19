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
        if(task == 'order.cancel'){
            Joomla.submitform(task, document.getElementById('order-form'));
        }
        else{
            
            if (task != 'order.cancel' && document.formvalidator.isValid(document.id('order-form'))) {
                
                Joomla.submitform(task, document.getElementById('order-form'));
            }
            else {
                alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
            }
        }
    }
</script>

<form action="<?php echo JRoute::_('index.php?option=com_easysdi_shop&layout=edit&id=' . (int) $this->item->id); ?>" method="post" enctype="multipart/form-data" name="adminForm" id="order-form" class="form-validate">
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
				<div class="control-label"><?php echo $this->form->getLabel('name'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('name'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('access'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('access'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('asset_id'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('asset_id'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('ordertype_id'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('ordertype_id'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('orderstate_id'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('orderstate_id'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('user_id'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('user_id'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('thirdparty_id'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('thirdparty_id'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('buffer'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('buffer'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('surface'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('surface'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('remark'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('remark'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('sent'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('sent'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('completed'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('completed'); ?></div>
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