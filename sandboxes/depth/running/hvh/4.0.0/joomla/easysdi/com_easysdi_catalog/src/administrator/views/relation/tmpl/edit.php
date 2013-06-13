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
        
	js('input:hidden.parent_id').each(function(){
		var name = js(this).attr('name');
		if(name.indexOf('parent_idhidden')){
			js('#jform_parent_id option[value="'+js(this).val()+'"]').attr('selected',true);
		}
	});
	js("#jform_parent_id").trigger("liszt:updated");
	js('input:hidden.attributechild_id').each(function(){
		var name = js(this).attr('name');
		if(name.indexOf('attributechild_idhidden')){
			js('#jform_attributechild_id option[value="'+js(this).val()+'"]').attr('selected',true);
		}
	});
	js("#jform_attributechild_id").trigger("liszt:updated");
	js('input:hidden.classchild_id').each(function(){
		var name = js(this).attr('name');
		if(name.indexOf('classchild_idhidden')){
			js('#jform_classchild_id option[value="'+js(this).val()+'"]').attr('selected',true);
		}
	});
	js("#jform_classchild_id").trigger("liszt:updated");
    });
    
    Joomla.submitbutton = function(task)
    {
        if(task == 'relation.cancel'){
            Joomla.submitform(task, document.getElementById('relation-form'));
        }
        else{
            
            if (task != 'relation.cancel' && document.formvalidator.isValid(document.id('relation-form'))) {
                
                Joomla.submitform(task, document.getElementById('relation-form'));
            }
            else {
                alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
            }
        }
    }
</script>

<form action="<?php echo JRoute::_('index.php?option=com_easysdi_catalog&layout=edit&id=' . (int) $this->item->id); ?>" method="post" enctype="multipart/form-data" name="adminForm" id="relation-form" class="form-validate">
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
				<div class="control-label"><?php echo $this->form->getLabel('description'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('description'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('parent_id'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('parent_id'); ?></div>
			</div>

			<?php
				foreach((array)$this->item->parent_id as $value): 
					if(!is_array($value)):
						echo '<input type="hidden" class="parent_id" name="jform[parent_idhidden]['.$value.']" value="'.$value.'" />';
					endif;
				endforeach;
			?>			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('attributechild_id'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('attributechild_id'); ?></div>
			</div>

			<?php
				foreach((array)$this->item->attributechild_id as $value): 
					if(!is_array($value)):
						echo '<input type="hidden" class="attributechild_id" name="jform[attributechild_idhidden]['.$value.']" value="'.$value.'" />';
					endif;
				endforeach;
			?>			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('classchild_id'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('classchild_id'); ?></div>
			</div>

			<?php
				foreach((array)$this->item->classchild_id as $value): 
					if(!is_array($value)):
						echo '<input type="hidden" class="classchild_id" name="jform[classchild_idhidden]['.$value.']" value="'.$value.'" />';
					endif;
				endforeach;
			?>			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('lowerbound'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('lowerbound'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('upperbound'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('upperbound'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('relationtype_id'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('relationtype_id'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('rendertype_id'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('rendertype_id'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('namespace_id'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('namespace_id'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('isocode'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('isocode'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('classassociation_id'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('classassociation_id'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('issearchfilter'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('issearchfilter'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('relationscope_id'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('relationscope_id'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('editorrelationscope_id'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('editorrelationscope_id'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('childresourcetype_id'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('childresourcetype_id'); ?></div>
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