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
$document->addStyleSheet('components/com_easysdi_core/assets/css/easysdi_core.css');
?>
<script type="text/javascript">
    js = jQuery.noConflict();
    js(document).ready(function() {
        onClassChange();
        onChangeInheritance();
    });

    Joomla.submitbutton = function(task)
    {
        if (task == 'resourcetypelink.cancel') {
            Joomla.submitform(task, document.getElementById('resourcetypelink-form'));
        }
        else {

            if (task != 'resourcetypelink.cancel' && document.formvalidator.isValid(document.id('resourcetypelink-form'))) {

                Joomla.submitform(task, document.getElementById('resourcetypelink-form'));
            }
            else {
                alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
            }
        }
    }

    function onClassChange() {
        js('#loader').show();
        var class_id = js("#jform_class_id :selected").val();
        if (class_id == '') {
            js('#loader').hide();
            return;
        }
        var uriencoded = 'http://localhost/sdi4/administrator/index.php?option=com_easysdi_catalog&task=resourcetypelink.getAttributes&class_id=' + class_id;
        js.ajax({
            type: 'Get',
            url: uriencoded,
            success: function(data) {
                var attributes = js.parseJSON(data);
                js('#jform_attribute_id').empty().trigger("liszt:updated");

                js.each(attributes, function(key, value) {
                    js('#jform_attribute_id')
                            .append('<option value="' + value.id + '">' + value.name + '</option>')
                            .trigger("liszt:updated")
                            ;
                });
                js('#loader').hide();
            }

        })
    }

    function onChangeInheritance() {
        var value = js('#jform_inheritance0:checked').val();
        if (value == 1) {
            js('#xpath').show();
        }
        else {
            js('#xpath').hide();
        }
    }

    var xpathindex = 0;

    function addXPath() {
        xpathindex += 1;
        html = "<div class='control-group' id='" + xpathindex + "'> <div class='controls'>";
        html += "<input type='text' name='jform[xpath][]' id='jform_xpath[" + xpathindex + "]' value='' class='inputbox' size='40'>";
        html += "<span class='btn btn-danger btn-small' name='xpathminus" + xpathindex + "' id='xpathminus" + xpathindex + "' onclick='removeXPath(" + xpathindex + ");'><i class='icon-white icon-minus'></i></span>";
        html += "</div></div>";
        js('#xpath').append(html);

    }

    function removeXPath(index) {
        js('#' + index).remove();
    }
</script>

<form action="<?php echo JRoute::_('index.php?option=com_easysdi_catalog&layout=edit&id=' . (int) $this->item->id); ?>" method="post" enctype="multipart/form-data" name="adminForm" id="resourcetypelink-form" class="form-validate">
    <div id="loader" style="">
        <img id="loader_image"  src="components/com_easysdi_core/assets/images/loader.gif" alt="">
    </div>
    <div class="row-fluid">
        <div class="span10 form-horizontal">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#details" data-toggle="tab"><?php echo empty($this->item->id) ? JText::_('COM_EASYSDI_CATALOG_TAB_NEW_NAMESPACE') : JText::sprintf('COM_EASYSDI_CATALOG_TAB_EDIT_NAMESPACE', $this->item->id); ?></a></li>
                <li><a href="#publishing" data-toggle="tab"><?php echo JText::_('COM_EASYSDI_CATALOG_TAB_PUBLISHING'); ?></a></li>
                <?php if (JFactory::getUser()->authorise('core.admin', 'easysdi_catalog')): ?>
                    <li><a href="#permissions" data-toggle="tab"><?php echo JText::_('COM_EASYSDI_CATALOG_TAB_RULES'); ?></a></li>
                <?php endif ?>
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="details">
                    <?php foreach ($this->form->getFieldset('details') as $field): ?> 
                        <div class="control-group" id="<?php echo $field->fieldname; ?>">
                            <div class="control-label"><?php echo $field->label; ?></div>
                            <div class="controls"><?php echo $field->input; ?></div>
                        </div>
                    <?php endforeach; ?>

                    <div class="well">
                        <label><?php echo JText::_('COM_EASYSDI_CATALOG_FORM_LBL_RESOURCETYPELINK_PARENT_GUID'); ?></label>
                        <div class="control-group">
                            <div class="control-label"><?php echo $this->form->getLabel('class_id'); ?></div>
                            <div class="controls"><?php echo $this->form->getInput('class_id'); ?></div>
                        </div>
                        <div class="control-group">
                            <div class="control-label"><?php echo $this->form->getLabel('attribute_id'); ?></div>
                            <div class="controls"><?php echo $this->form->getInput('attribute_id'); ?></div>
                        </div>
                    </div>

                    <div class="control-group">
                        <div class="control-label"><?php echo $this->form->getLabel('viralversioning'); ?></div>
                        <div class="controls"><?php echo $this->form->getInput('viralversioning'); ?></div>
                    </div>
                    <div class="well">
                        <label><?php echo JText::_('COM_EASYSDI_CATALOG_FORM_LBL_RESOURCETYPELINK_PARENT'); ?></label>
                        <div class="control-group">
                            <div class="control-label"><?php echo $this->form->getLabel('parentboundlower'); ?></div>
                            <div class="controls"><?php echo $this->form->getInput('parentboundlower'); ?></div>
                        </div>
                        <div class="control-group">
                            <div class="control-label"><?php echo $this->form->getLabel('parentboundupper'); ?></div>
                            <div class="controls"><?php echo $this->form->getInput('parentboundupper'); ?></div>
                        </div>
                    </div>
                    <div class="well">
                        <label><?php echo JText::_('COM_EASYSDI_CATALOG_FORM_LBL_RESOURCETYPELINK_CHILD'); ?></label>
                        <div class="control-group">
                            <div class="control-label"><?php echo $this->form->getLabel('childboundlower'); ?></div>
                            <div class="controls"><?php echo $this->form->getInput('childboundlower'); ?></div>
                        </div>
                        <div class="control-group">
                            <div class="control-label"><?php echo $this->form->getLabel('childboundupper'); ?></div>
                            <div class="controls"><?php echo $this->form->getInput('childboundupper'); ?></div>
                        </div>
                    </div>

                    <div class="control-group">
                        <div class="control-label"><?php echo $this->form->getLabel('inheritance'); ?></div>
                        <div class="controls"><?php echo $this->form->getInput('inheritance'); ?></div>
                    </div>

                    <div class="control-group" id="xpath">
                        <div class="control-group">
                            <div class="control-label"><?php echo $this->form->getLabel('addxpath'); ?></div>
                            <div class="controls"><?php echo $this->form->getInput('addxpath'); ?></div>
                        </div>
                    </div>

                    <div class="control-group">
                        <div class="control-label"><?php echo $this->form->getLabel('id'); ?></div>
                        <div class="controls"><?php echo $this->form->getInput('id'); ?></div>
                    </div>

                    <?php foreach ($this->form->getFieldset('hidden') as $field): ?>
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