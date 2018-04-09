<?php
/**
 * @version     4.4.5
 * @package     com_easysdi_catalog
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
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
$document->addStyleSheet(Juri::root(true) .'/components/com_easysdi_catalog/assets/css/easysdi_catalog.css?v=' . sdiFactory::getSdiFullVersion());
$document->addStyleSheet('components/com_easysdi_core/assets/css/easysdi_core.css?v=' . sdiFactory::getSdiFullVersion());
$document->addScript('components/com_easysdi_catalog/views/relation/tmpl/edit.js?v=' . sdiFactory::getSdiFullVersion());
?>
<script type="text/javascript">
    var url = '<?php echo JURI::root(); ?>administrator/index.php?option=com_easysdi_catalog&task=relation.getRenderType&attributechild=';
    var stereotype;
    var attributevalue = {};

    js = jQuery.noConflict();
    js(document).ready(function() {
        onChangeChildType();
        onChangeSearchFilter();
        onChangeAttributeChild();   
        
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
</script>

<form action="<?php echo JRoute::_('index.php?option=com_easysdi_catalog&layout=edit&id=' . (int) $this->item->id); ?>" method="post" enctype="multipart/form-data" name="adminForm" id="relation-form" class="form-validate">
    <div id="loader" style="">
        <img id="loader_image"  src="components/com_easysdi_core/assets/images/loader.gif" alt="">
    </div>
    <div class="row-fluid">
        <div class="span10 form-horizontal">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#details" data-toggle="tab"><?php echo empty($this->item->id) ? JText::_('COM_EASYSDI_CATALOG_TAB_NEW') : JText::sprintf('COM_EASYSDI_CATALOG_TAB_EDIT', $this->item->id); ?></a></li>
                <li><a href="#search" data-toggle="tab"><?php echo JText::_('COM_EASYSDI_CATALOG_TAB_SEARCH'); ?></a></li>
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
                        <div class="control-label"><?php echo $this->form->getLabel('profile_id'); ?></div>
                        <div class="controls"><?php echo $this->form->getInput('profile_id'); ?></div>
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
                        
                        <div class="control-group">
                            <div class="control-label"><?php echo $this->form->getLabel('accessscope_limitation'); ?></div>
                            <div class="controls"><?php echo $this->form->getInput('accessscope_limitation'); ?></div>
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
                    </div>

                    <div id="defaultvalue">
                        <div id="defaultvalue-textbox">
                            <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('defaulttextbox'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('defaulttextbox'); ?></div>
                            </div>
                        </div>
                        <div id="defaultvalue-textarea">
                            <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('defaulttextarea'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('defaulttextarea'); ?></div>
                            </div>
                        </div>
                        <div id="defaultvalue-date">
                            <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('defaultdate'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('defaultdate'); ?></div>
                            </div>
                        </div>
                        <div id="defaultvalue-list">
                            <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('defaultlist'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('defaultlist'); ?></div>
                            </div>
                        </div>
                        <div id="defaultvalue-multiplelist">
                            <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('defaultmultiplelist'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('defaultmultiplelist'); ?></div>
                            </div>
                        </div>
                        <div id="defaultvalue-localetextbox">
                            <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('defaultlocaletextbox'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('defaultlocaletextbox'); ?></div>
                            </div>
                        </div>
                        <div id="defaultvalue-localetextarea">
                            <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('defaultlocaletextarea'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('defaultlocaletextarea'); ?></div>
                            </div>
                        </div>
                    </div>

                    <div class="well">
                        <div class="control-group">
                            <div class="control-label"><?php echo $this->form->getLabel('text1'); ?></div>
                            <div class="controls"><?php echo $this->form->getInput('text1'); ?></div>
                        </div>
                    </div>
                    <div class="well">
                        <div class="control-group">
                            <div class="control-label"><?php echo $this->form->getLabel('text2'); ?></div>
                            <div class="controls"><?php echo $this->form->getInput('text2'); ?></div>
                        </div>
                    </div>
                    <div class="control-group">
                        <div class="control-label"><?php echo $this->form->getLabel('id'); ?></div>
                        <div class="controls"><?php echo $this->form->getInput('id'); ?></div>
                    </div>
                </div>
                <div class="tab-pane" id="search">
                    <div class="control-group">
                        <div class="control-label"><?php echo $this->form->getLabel('issearchfilter'); ?></div>
                        <div class="controls"><?php echo $this->form->getInput('issearchfilter'); ?></div>
                    </div> 
                    <div id="searchfilterdefinition">
                        <div class="control-group">
                            <div class="control-label"><?php echo $this->form->getLabel('catalog_id'); ?></div>
                            <div class="controls"><?php echo $this->form->getInput('catalog_id'); ?></div>
                        </div> 
                        <div class="well">
                            <?php echo $this->form->getInput('searchfilter'); ?>
                        </div>
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
            <?php echo $field->input; ?>
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