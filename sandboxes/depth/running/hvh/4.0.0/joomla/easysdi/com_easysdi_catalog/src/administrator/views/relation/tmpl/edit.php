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
    js(document).ready(function() {
            onChangeChildType();
//        js('input:hidden.parent_id').each(function() {
//            var name = js(this).attr('name');
//            if (name.indexOf('parent_idhidden')) {
//                js('#jform_parent_id option[value="' + js(this).val() + '"]').attr('selected', true);
//            }
//        });
//        js("#jform_parent_id").trigger("liszt:updated");
//        js('input:hidden.attributechild_id').each(function() {
//            var name = js(this).attr('name');
//            if (name.indexOf('attributechild_idhidden')) {
//                js('#jform_attributechild_id option[value="' + js(this).val() + '"]').attr('selected', true);
//            }
//        });
//        js("#jform_attributechild_id").trigger("liszt:updated");
//        js('input:hidden.classchild_id').each(function() {
//            var name = js(this).attr('name');
//            if (name.indexOf('classchild_idhidden')) {
//                js('#jform_classchild_id option[value="' + js(this).val() + '"]').attr('selected', true);
//            }
//        });
//        js("#jform_classchild_id").trigger("liszt:updated");
    });

    Joomla.submitbutton = function(task)
    {
        if (task == 'relation.cancel') {
            Joomla.submitform(task, document.getElementById('relation-form'));
        }
        else {

            if (task != 'relation.cancel' && document.formvalidator.isValid(document.id('relation-form'))) {

                Joomla.submitform(task, document.getElementById('relation-form'));
            }
            else {
                alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
            }
        }
    }
    
    function onChangeChildType(){
        var childtype = js("#jform_childtype_id :selected").val(); 
        switch (childtype){
            case "0":
                js("#classchilddefinition").hide();
                js("#commondefinition").hide();
                js("#attributechilddefinition").hide();
                js("#resourcetypedefinition").hide();
                break;
            case "1":
                js("#classchilddefinition").show();
                js("#commondefinition").show();
                js("#attributechilddefinition").hide();
                js("#resourcetypedefinition").hide();
                break;
            case "2":
                js("#classchilddefinition").hide();
                js("#commondefinition").hide();
                js("#attributechilddefinition").show();
                js("#resourcetypedefinition").hide();
                break;
            case "3":
                js("#classchilddefinition").hide();
                js("#commondefinition").show();
                js("#attributechilddefinition").hide();
                js("#resourcetypedefinition").show();
                break;
                
        }
        
    }
</script>

<form action="<?php echo JRoute::_('index.php?option=com_easysdi_catalog&layout=edit&id=' . (int) $this->item->id); ?>" method="post" enctype="multipart/form-data" name="adminForm" id="relation-form" class="form-validate">
    <div class="row-fluid">
        <div class="span10 form-horizontal">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#details" data-toggle="tab"><?php echo empty($this->item->id) ? JText::_('COM_EASYSDI_CATALOG_TAB_NEW') : JText::sprintf('COM_EASYSDI_CATALOG_TAB_EDIT', $this->item->id); ?></a></li>
                <li><a href="#publishing" data-toggle="tab"><?php echo JText::_('COM_EASYSDI_CATALOG_TAB_PUBLISHING'); ?></a></li>
                <?php if (JFactory::getUser()->authorise('core.admin', 'easysdi_catalog')): ?>
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
                        <div class="control-label"><?php echo $this->form->getLabel('relationscope_id'); ?></div>
                        <div class="controls"><?php echo $this->form->getInput('relationscope_id'); ?></div>
                    </div>
                    <div class="control-group">
                        <div class="control-label"><?php echo $this->form->getLabel('editorrelationscope_id'); ?></div>
                        <div class="controls"><?php echo $this->form->getInput('editorrelationscope_id'); ?></div>
                    </div>
                    <div class="control-group">
                        <div class="control-label"><?php echo $this->form->getLabel('parent_id'); ?></div>
                        <div class="controls"><?php echo $this->form->getInput('parent_id'); ?></div>
                    </div>
                    <div class="control-group">
                        <div class="control-label"><?php echo $this->form->getLabel('lowerbound'); ?></div>
                        <div class="controls"><?php echo $this->form->getInput('lowerbound'); ?></div>
                    </div>
                    <div class="control-group">
                        <div class="control-label"><?php echo $this->form->getLabel('upperbound'); ?></div>
                        <div class="controls"><?php echo $this->form->getInput('upperbound'); ?></div>
                    </div>
                    <div class="control-group">
                        <div class="control-label"><?php echo $this->form->getLabel('childtype_id'); ?></div>
                        <div class="controls"><?php echo $this->form->getInput('childtype_id'); ?></div>
                    </div>

                    <div id="classchilddefinition">
                        <div class="control-group">
                            <div class="control-label"><?php echo $this->form->getLabel('classchild_id'); ?></div>
                            <div class="controls"><?php echo $this->form->getInput('classchild_id'); ?></div>
                        </div>
                    </div>

                    <div id="resourcetypedefinition">
                        <div class="control-group">
                            <div class="control-label"><?php echo $this->form->getLabel('childresourcetype_id'); ?></div>
                            <div class="controls"><?php echo $this->form->getInput('childresourcetype_id'); ?></div>
                        </div>
                    </div>

                    <div id="commondefinition">
                        <div class="control-group">
                            <div class="control-label"><?php echo $this->form->getLabel('isocode'); ?></div>
                            <div class="controls"><?php echo $this->form->getInput('namespace_id'); ?><?php echo $this->form->getInput('isocode'); ?></div>
                        </div>

                        <div class="control-group">
                            <div class="control-label"><?php echo $this->form->getLabel('relationtype_id'); ?></div>
                            <div class="controls"><?php echo $this->form->getInput('relationtype_id'); ?></div>
                        </div>
                        <div class="control-group">
                            <div class="control-label"><?php echo $this->form->getLabel('classassociation_id'); ?></div>
                            <div class="controls"><?php echo $this->form->getInput('classassociation_id'); ?></div>
                        </div>
                    </div>

                    <div id="attributechilddefinition">
                        <div class="control-group">
                            <div class="control-label"><?php echo $this->form->getLabel('attributechild_id'); ?></div>
                            <div class="controls"><?php echo $this->form->getInput('attributechild_id'); ?></div>
                        </div>
                        <div class="control-group">
                            <div class="control-label"><?php echo $this->form->getLabel('rendertype_id'); ?></div>
                            <div class="controls"><?php echo $this->form->getInput('rendertype_id'); ?></div>
                        </div>
                        <div class="control-group">
                            <div class="control-label"><?php echo $this->form->getLabel('issearchfilter'); ?></div>
                            <div class="controls"><?php echo $this->form->getInput('issearchfilter'); ?></div>
                        </div>
                    </div>
                    <div class="well">
                        <?php echo $this->form->getInput('text1'); ?>
                    </div>
                    <div class="well">
                        <?php echo $this->form->getInput('text2'); ?>
                    </div>
                    <div class="control-group">
                        <div class="control-label"><?php echo $this->form->getLabel('id'); ?></div>
                        <div class="controls"><?php echo $this->form->getInput('id'); ?></div>
                    </div>
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
                <?php if (JFactory::getUser()->authorise('core.admin', 'easysdi_catalog')): ?>
                    <div class="tab-pane" id="permissions">
                        <fieldset>
                            <?php echo $this->form->getInput('rules'); ?>
                        </fieldset>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="clr"></div>

        <?php foreach ($this->form->getFieldset('hidden') as $field): ?>
            <div class="controls"><?php echo $field->input; ?></div>
        <?php endforeach; ?>    
        <input type="hidden" name="task" value="" />
        <?php echo JHtml::_('form.token'); ?>

        <!-- Begin Sidebar -->
        <div class="span2">
            <h4><?php echo JText::_('JDETAILS'); ?></h4>
            <hr />
            <fieldset class="form-vertical">
                <div class="control-group">
                    <?php if (JFactory::getUser()->authorise('core.edit.state', 'easysdi_catalog')): ?>
                        <div class="control-label">
                            <?php echo $this->form->getLabel('state'); ?>
                        </div>
                        <div class="controls">
                            <?php echo $this->form->getInput('state'); ?>
                        </div>
                    <?php endif; ?>
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