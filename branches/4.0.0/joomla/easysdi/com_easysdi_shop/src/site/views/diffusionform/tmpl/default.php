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

JHtml::_('behavior.keepalive');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');

//Load admin language file
$lang = JFactory::getLanguage();
$lang->load('com_easysdi_shop', JPATH_ADMINISTRATOR);
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
<script type="text/javascript">
    function getScript(url,success) {
        var script = document.createElement('script');
        script.src = url;
        var head = document.getElementsByTagName('head')[0],
        done = false;
        // Attach handlers for all browsers
        script.onload = script.onreadystatechange = function() {
            if (!done && (!this.readyState
                || this.readyState == 'loaded'
                || this.readyState == 'complete')) {
                done = true;
                success();
                script.onload = script.onreadystatechange = null;
                head.removeChild(script);
            }
        };
        head.appendChild(script);
    }
    getScript('//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js',function() {
        js = jQuery.noConflict();
        js(document).ready(function(){
            js('#form-diffusion').submit(function(event){
                
				if(js('#jform_deposit').val() != ''){
					js('#jform_deposit_hidden').val(js('#jform_deposit').val());
				}
				if(js('#jform_file').val() != ''){
					js('#jform_file_hidden').val(js('#jform_file').val());
				} 
            }); 
        
            
        });
    });
    
</script>

<div class="diffusion-edit front-end-edit">
    <?php if (!empty($this->item->id)): ?>
        <h1>Edit <?php echo $this->item->id; ?></h1>
    <?php else: ?>
        <h1>Add</h1>
    <?php endif; ?>

    <form id="form-diffusion" action="<?php echo JRoute::_('index.php?option=com_easysdi_shop&task=diffusion.save'); ?>" method="post" class="form-validate" enctype="multipart/form-data">
        <ul>
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
				<?php $canState = false; ?>
				<?php if($this->item->id): ?>
					<?php $canState = $canState = JFactory::getUser()->authorise('core.edit.state','com_easysdi_shop.diffusion'); ?>
				<?php else: ?>
					<?php $canState = JFactory::getUser()->authorise('core.edit.state','com_easysdi_shop.diffusion.'.$this->item->id); ?>
				<?php endif; ?>				<?php if(!$canState): ?>
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
						<a href="<?php echo JRoute::_(JUri::base() . 'administrator' . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_easysdi_shop' . DIRECTORY_SEPARATOR . '/' .DIRECTORY_SEPARATOR . $this->item->deposit, false);?>"><?php echo JText::_("COM_EASYSDI_SHOP_VIEW_FILE"); ?></a>
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
						<a href="<?php echo JRoute::_(JUri::base() . 'administrator' . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_easysdi_shop' . DIRECTORY_SEPARATOR . '/' .DIRECTORY_SEPARATOR . $this->item->file, false);?>"><?php echo JText::_("COM_EASYSDI_SHOP_VIEW_FILE"); ?></a>
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
				<div class="fltlft" <?php if (!JFactory::getUser()->authorise('core.admin','easysdi_shop')): ?> style="display:none;" <?php endif; ?> >
                <?php echo JHtml::_('sliders.start', 'permissions-sliders-'.$this->item->id, array('useCookie'=>1)); ?>
                <?php echo JHtml::_('sliders.panel', JText::_('ACL Configuration'), 'access-rules'); ?>
                <fieldset class="panelform">
                    <?php echo $this->form->getLabel('rules'); ?>
                    <?php echo $this->form->getInput('rules'); ?>
                </fieldset>
                <?php echo JHtml::_('sliders.end'); ?>
            </div>
				<?php if (!JFactory::getUser()->authorise('core.admin','easysdi_shop')): ?>
                <script type="text/javascript">
                    jQuery.noConflict();
                    jQuery('.tab-pane select').each(function(){
                       var option_selected = jQuery(this).find(':selected');
                       var input = document.createElement("input");
                       input.setAttribute("type", "hidden");
                       input.setAttribute("name", jQuery(this).attr('name'));
                       input.setAttribute("value", option_selected.val());
                       document.getElementById("form-diffusion").appendChild(input);
                       jQuery(this).attr('disabled',true);
                    });
                </script>
             <?php endif; ?>
        </ul>

        <div>
            <button type="submit" class="validate"><span><?php echo JText::_('JSUBMIT'); ?></span></button>
            <?php echo JText::_('or'); ?>
            <a href="<?php echo JRoute::_('index.php?option=com_easysdi_shop&task=diffusion.cancel'); ?>" title="<?php echo JText::_('JCANCEL'); ?>"><?php echo JText::_('JCANCEL'); ?></a>

            <input type="hidden" name="option" value="com_easysdi_shop" />
            <input type="hidden" name="task" value="diffusionform.save" />
            <?php echo JHtml::_('form.token'); ?>
        </div>
    </form>
</div>
