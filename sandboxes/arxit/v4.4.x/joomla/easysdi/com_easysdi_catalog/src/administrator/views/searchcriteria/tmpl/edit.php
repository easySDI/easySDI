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

$document->addScript('components/com_easysdi_catalog/assets/js/searchcriteria.js?v=' . sdiFactory::getSdiFullVersion());
?>
<script type="text/javascript">
    js = jQuery.noConflict();
    js(document).ready(function() {
        onRenderTypeChange();
        onBoundaryCategoryChange();
        filterResourceType(<?php echo $this->item->catalog_id ; ?>);
        
        js('#loader').hide();

    <?php if (isset($this->item->attributevalues)): ?>
        js('#jform_defaultvalues').empty().trigger("liszt:updated");
        <?php
        foreach ($this->item->attributevalues as $attributevalue) :

            if (isset($this->item->defaultvalues) && in_array($attributevalue->id, $this->item->defaultvalues))
                $selected = 'selected="selected"';
            else
                $selected = '';
            ?>
                    js('#jform_defaultvalues')
                            .append('<option value="<?php echo $attributevalue->id; ?>" <?php echo $selected; ?> ><?php echo $attributevalue->value; ?></option>')
                            .trigger("liszt:updated")
                            ;

            <?php
        endforeach;
    endif;
    ?>

    });

    Joomla.submitbutton = function(task)
    {
        if (task == 'searchcriteria.cancel') {
            Joomla.submitform(task, document.getElementById('searchcriteria-form'));
        }
        else {

            if (task != 'searchcriteria.cancel' && document.formvalidator.isValid(document.id('searchcriteria-form'))) {

                Joomla.submitform(task, document.getElementById('searchcriteria-form'));
            }
            else {
                alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
            }
        }
    }

    function onRenderTypeChange() {
        var rendertype = js("#jform_rendertype_id :selected").val();
        switch (rendertype) {
            case "5" :
                js('#cswdatevalue').hide();
                js('#jform_from').val('');
                js('#jform_to').val('');
                js('#cswtextvalue').show();
                js('#cswcbvalue').hide();
                break;
            case "6":
                js('#cswdatevalue').show();
                js('#cswtextvalue').hide();
                js('#jform_defaultvalue').val('');
                js('#cswcbvalue').hide();
                break;
            case "2":
                js('#cswdatevalue').hide();
                js('#jform_from').val('');
                js('#jform_to').val('');
                js('#cswtextvalue').hide();
                js('#jform_defaultvalue').val('');
                js('#cswcbvalue').show();                
                break;
        }
    }

    function onBoundaryCategoryChange() {
        var selectedValues = js('#jform_boundarycategory_id').val();

        var selectedBoundaries = js('#jform_boundary_id').val();

        if (selectedValues == null)
            return;

        js('#loader').show();
        var uriencoded = '<?php echo JURI::root() ; ?>administrator/index.php?option=com_easysdi_catalog&task=searchcriteria.getBoundaries&categories=' + JSON.stringify(selectedValues);
        js.ajax({
            type: 'Get',
            url: uriencoded,
            success: function(data) {
                var attributes = js.parseJSON(data);
                js('#jform_boundary_id').empty().trigger("liszt:updated");

                js.each(attributes, function(key, value) {
                    var selected = '';
                    if (js.inArray(value, selectedBoundaries))
                        selected = 'selected="selected"';
                    js('#jform_boundary_id')
                            .append('<option value="' + value.id + '" ' + selected + ' >' + value.name + '</option>')
                            .trigger("liszt:updated")
                            ;
                });
                js('#loader').hide();
            }
        })

    }
    
   
</script>

<form action="<?php echo JRoute::_('index.php?option=com_easysdi_catalog&layout=edit&id=' . (int) $this->item->id); ?>" method="post" enctype="multipart/form-data" name="adminForm" id="searchcriteria-form" class="form-validate">
    <div id="loader" style="">
        <img id="loader_image"  src="components/com_easysdi_core/assets/images/loader.gif" alt="">
    </div>
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
                        <div class="control-label"><?php echo $this->form->getLabel('searchtab_id'); ?></div>
                        <div class="controls"><?php echo $this->form->getInput('searchtab_id'); ?></div>
                    </div>
                    <?php if ($this->item->criteriatype_id == 3) : ?>
                        <div class="control-group">
                            <div class="control-label"><?php echo $this->form->getLabel('rendertype_id'); ?></div>
                            <div class="controls"><?php echo $this->form->getInput('rendertype_id'); ?></div>
                        </div>
                    <?php endif ?>
                    <?php if ($this->item->id == 8) : ?>
                        <div class="control-group">
                            <div class="control-label"><?php echo $this->form->getLabel('boundarycategory_id'); ?></div>
                            <div class="controls"><?php echo $this->form->getInput('boundarycategory_id'); ?></div>
                        </div>
                        <div class="control-group">
                            <div class="control-label"><?php echo $this->form->getLabel('searchboundarytype'); ?></div>
                            <div class="controls"><?php echo $this->form->getInput('searchboundarytype'); ?></div>
                        </div>
                        
                        <div class="control-group">
                            <div class="control-label"><?php echo $this->form->getLabel('boundarysearchfield'); ?></div>
                            <div class="controls"><?php echo $this->form->getInput('boundarysearchfield'); ?></div>
                        </div>
                    <?php endif ?>
                    <div class="well">
                        <?php if ($this->item->id == 1 || $this->item->id == 4) : ?>
                            <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('defaultvalue'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('defaultvalue'); ?></div>
                            </div>
                        <?php endif ?>
                        <?php if ($this->item->id == 2) : ?>
                            <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('defaultvalue'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('resourcetype_id'); ?></div>
                            </div>
                        <?php endif ?>
                        <?php if ($this->item->id == 3) : ?>
                            <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('defaultvalue'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('version'); ?></div>
                            </div>
                        <?php endif ?>
                        <?php if ($this->item->id == 5 || $this->item->id == 6) : ?>
                            <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('defaultvalue'); ?></div>
                            </div>
                            <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('from'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('from'); ?></div>
                            </div>
                            <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('to'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('to'); ?></div>
                            </div>
                        <?php endif ?>
                        <?php if ($this->item->id == 7) : ?>
                            <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('defaultvalue'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('organism_id'); ?></div>
                            </div>
                        <?php endif ?>
                        <?php if ($this->item->id == 8) : ?>
                            <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('defaultvalue'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('boundary_id'); ?></div>
                            </div>
                        <?php endif ?>
                        <?php if ($this->item->id == 9 || $this->item->id == 10 || $this->item->id == 11 || strcasecmp('isviewable',$this->item->alias) == 0) : ?>
                            <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('defaultvalue'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('is'); ?></div>
                            </div>
                        <?php endif ?>
                        <?php if ($this->item->criteriatype_id == 3) : ?>
                            <div id="cswdatevalue">
                                <div class="control-group" >
                                    <div class="control-label"><?php echo $this->form->getLabel('defaultvalue'); ?></div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label"><?php echo $this->form->getLabel('from'); ?></div>
                                    <div class="controls"><?php echo $this->form->getInput('from'); ?></div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label"><?php echo $this->form->getLabel('to'); ?></div>
                                    <div class="controls"><?php echo $this->form->getInput('to'); ?></div>
                                </div>
                            </div>
                            <div class="control-group" id="cswtextvalue">
                                <div class="control-label"><?php echo $this->form->getLabel('defaultvalue'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('defaultvalue'); ?></div>
                            </div>
                            <div class="control-group" id="cswcbvalue">
                                <div class="control-label"><?php echo $this->form->getLabel('defaultcheckbox'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('defaultcheckbox'); ?></div>
                            </div>
                        <?php endif ?>
                        <?php if ($this->item->criteriatype_id == 2) : ?>
                            <?php if ($this->item->attributestereotype_id == 5 || $this->item->attributestereotype_id == 8) : ?>
                                <div class="control-group" id="cswdatevalue">
                                    <div class="control-label"><?php echo $this->form->getLabel('defaultvalue'); ?></div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label"><?php echo $this->form->getLabel('from'); ?></div>
                                    <div class="controls"><?php echo $this->form->getInput('from'); ?></div>
                                </div>
                                <div class="control-group">
                                    <div class="control-label"><?php echo $this->form->getLabel('to'); ?></div>
                                    <div class="controls"><?php echo $this->form->getInput('to'); ?></div>
                                </div>
                            <?php elseif ($this->item->attributestereotype_id == 6) : ?>
                                <div class="control-group">
                                    <div class="control-label"><?php echo $this->form->getLabel('defaultvalues'); ?></div>
                                    <div class="controls"><?php echo $this->form->getInput('defaultvalues'); ?></div>
                                </div>
                            <?php else: ?>
                                <div class="control-group">
                                    <div class="control-label"><?php echo $this->form->getLabel('defaultvalue'); ?></div>
                                    <div class="controls"><?php echo $this->form->getInput('defaultvalue'); ?></div>
                                </div>
                            <?php endif ?>
                        <?php endif ?>
                    </div>

                    <?php if ($this->item->criteriatype_id == 3) : ?>
                    <div class="well">
                        <div class="control-group">
                            <div class="control-label"><?php echo $this->form->getLabel('searchfilter'); ?></div>
                            <div class="controls"><?php echo $this->form->getInput('searchfilter'); ?></div>
                        </div>
                    </div>
                    <?php endif ?>

                    <div class="well">
                        <div class="control-group">
                            <div class="control-label"><?php echo $this->form->getLabel('text1'); ?></div>
                            <div class="controls"><?php echo $this->form->getInput('text1'); ?></div>
                        </div>
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