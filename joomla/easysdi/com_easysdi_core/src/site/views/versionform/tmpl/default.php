<?php
/**
 * @version     4.4.5
 * @package     com_easysdi_core
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
$lang->load('com_easysdi_core', JPATH_ADMINISTRATOR);
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

    js = jQuery.noConflict();
    
    js(document).ready(function () {
        
        js('input:hidden.resource_id').each(function () {
            var name = js(this).attr('name');
            if (name.indexOf('resource_idhidden')) {
                js('#jform_resource_id option[value="' + js(this).val() + '"]').attr('selected', true);
            }
        });
        js("#jform_resource_id").trigger("liszt:updated");
        
    });


</script>

<div class="version-edit front-end-edit">
    <?php if (!empty($this->item->id)): ?>
        <h1>Edit <?php echo $this->item->id; ?></h1>
    <?php else: ?>
        <h1>Add</h1>
    <?php endif; ?>

    <form id="form-version" action="<?php echo JRoute::_('index.php?option=com_easysdi_core&task=version.save'); ?>" method="post" class="form-validate" enctype="multipart/form-data">
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
                <?php if ($this->item->id): ?>
                    <?php $canState = $canState = JFactory::getUser()->authorise('core.edit.state', 'com_easysdi_core.version'); ?>
                <?php else: ?>
                    <?php $canState = JFactory::getUser()->authorise('core.edit.state', 'com_easysdi_core.version.' . $this->item->id); ?>
                <?php endif; ?>				<?php if (!$canState): ?>
                    <div class="control-label"><?php echo $this->form->getLabel('state'); ?></div>
                    <?php
                    $state_string = 'Unpublish';
                    $state_value = 0;
                    if ($this->item->state == 1):
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
                <div class="control-label"><?php echo $this->form->getLabel('resource_id'); ?></div>
                <div class="controls"><?php echo $this->form->getInput('resource_id'); ?></div>
            </div>

            <?php
            foreach ((array) $this->item->resource_id as $value):
                if (!is_array($value)):
                    echo '<input type="hidden" name="jform[resource_idhidden][' . $value . ']" value="' . $value . '" />';
                endif;
            endforeach;
            ?>			<div class="control-group">
                <div class="control-label"><?php echo $this->form->getLabel('access'); ?></div>
                <div class="controls"><?php echo $this->form->getInput('access'); ?></div>
            </div>
            <div class="fltlft" <?php if (!JFactory::getUser()->authorise('core.admin', 'easysdi_core')): ?> style="display:none;" <?php endif; ?> >
                <?php echo JHtml::_('sliders.start', 'permissions-sliders-' . $this->item->id, array('useCookie' => 1)); ?>
                <?php echo JHtml::_('sliders.panel', JText::_('ACL Configuration'), 'access-rules'); ?>
                <fieldset class="panelform">
                    <?php echo $this->form->getLabel('rules'); ?>
                    <?php echo $this->form->getInput('rules'); ?>
                </fieldset>
                <?php echo JHtml::_('sliders.end'); ?>
            </div>
            <?php if (!JFactory::getUser()->authorise('core.admin', 'easysdi_core')): ?>
                <script type="text/javascript">
                    jQuery.noConflict();
                            jQuery('.tab-pane select').each(function () {
                        var option_selected = jQuery(this).find(':selected');
                        var input = document.createElement("input");
                        input.setAttribute("type", "hidden");
                        input.setAttribute("name", jQuery(this).attr('name'));
                        input.setAttribute("value", option_selected.val());
                        document.getElementById("form-version").appendChild(input);
                        jQuery(this).attr('disabled', true);
                    });
                </script>
            <?php endif; ?>
        </ul>

        <div>
            <button type="submit" class="validate"><span><?php echo JText::_('JSUBMIT'); ?></span></button>
                    <?php echo JText::_('or'); ?>
            <a href="<?php echo JRoute::_('index.php?option=com_easysdi_core&task=version.cancel'); ?>" title="<?php echo JText::_('JCANCEL'); ?>"><?php echo JText::_('JCANCEL'); ?></a>

            <input type="hidden" name="option" value="com_easysdi_core" />
            <input type="hidden" name="task" value="versionform.save" />
            <?php echo JHtml::_('form.token'); ?>
        </div>
    </form>
</div>
