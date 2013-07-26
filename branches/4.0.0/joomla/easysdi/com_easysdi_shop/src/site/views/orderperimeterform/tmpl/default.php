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
            js('#form-orderperimeter').submit(function(event){
                 
            }); 
        
            
					js('input:hidden.order_id').each(function(){
						var name = js(this).attr('name');
						if(name.indexOf('order_idhidden')){
							js('#jform_order_id option[value="'+js(this).val()+'"]').attr('selected',true);
						}
					});
					js("#jform_order_id").trigger("liszt:updated");
					js('input:hidden.perimeter_id').each(function(){
						var name = js(this).attr('name');
						if(name.indexOf('perimeter_idhidden')){
							js('#jform_perimeter_id option[value="'+js(this).val()+'"]').attr('selected',true);
						}
					});
					js("#jform_perimeter_id").trigger("liszt:updated");
        });
    });
    
</script>

<div class="orderperimeter-edit front-end-edit">
    <?php if (!empty($this->item->id)): ?>
        <h1>Edit <?php echo $this->item->id; ?></h1>
    <?php else: ?>
        <h1>Add</h1>
    <?php endif; ?>

    <form id="form-orderperimeter" action="<?php echo JRoute::_('index.php?option=com_easysdi_shop&task=orderperimeter.save'); ?>" method="post" class="form-validate" enctype="multipart/form-data">
        <ul>
            			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('id'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('id'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('order_id'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('order_id'); ?></div>
			</div>

			<?php
				foreach((array)$this->item->order_id as $value): 
					if(!is_array($value)):
						echo '<input type="hidden" class="order_id" name="jform[order_idhidden]['.$value.']" value="'.$value.'" />';
					endif;
				endforeach;
			?>			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('perimeter_id'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('perimeter_id'); ?></div>
			</div>

			<?php
				foreach((array)$this->item->perimeter_id as $value): 
					if(!is_array($value)):
						echo '<input type="hidden" class="perimeter_id" name="jform[perimeter_idhidden]['.$value.']" value="'.$value.'" />';
					endif;
				endforeach;
			?>			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('value'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('value'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('text'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('text'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('created_by'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('created_by'); ?></div>
			</div>

        </ul>

        <div>
            <button type="submit" class="validate"><span><?php echo JText::_('JSUBMIT'); ?></span></button>
            <?php echo JText::_('or'); ?>
            <a href="<?php echo JRoute::_('index.php?option=com_easysdi_shop&task=orderperimeter.cancel'); ?>" title="<?php echo JText::_('JCANCEL'); ?>"><?php echo JText::_('JCANCEL'); ?></a>

            <input type="hidden" name="option" value="com_easysdi_shop" />
            <input type="hidden" name="task" value="orderperimeterform.save" />
            <?php echo JHtml::_('form.token'); ?>
        </div>
    </form>
</div>
