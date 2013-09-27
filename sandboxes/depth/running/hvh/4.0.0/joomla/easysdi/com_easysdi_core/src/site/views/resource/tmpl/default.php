<?php
/**
 * @version     4.0.0
 * @package     com_easysdi_core
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
// no direct access
defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');

//Load admin language file
$lang = JFactory::getLanguage();
$lang->load('com_easysdi_core', JPATH_ADMINISTRATOR);
?>

<!-- Styling for making front end forms look OK -->
<!-- This should probably be moved to the template CSS file -->
<style>


    #loader{
        -moz-border-radius: 8px;
        border-radius: 8px;
        position:absolute; 
        background-color: #B8B9BB;
        opacity:0.3;
        filter:alpha(opacity=30);
        text-align : center; 
        vertical-align:middle;
        height:90%;
        width:70%;
        z-index:99;
    }
    #loader_image{
        padding-left:60px;
        padding-top:70px;
    }
</style>
<script type="text/javascript">
    function getScript(url, success) {
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

    js = jQuery.noConflict();
    js(document).ready(function() {
        onChangeOrganism();
        js('#form-resource').submit(function(event) {

        });
    })

    function onChangeOrganism() {
        js('#loader').show();
        var organism_id = js("#jform_organism_id :selected").val();
        if (organism_id == '') {
            js('#loader').hide();
            return;
        }
        var uriencoded = '<?php echo JURI::root(); ?>index.php?option=com_easysdi_core&task=resource.getUsers&organism=' + organism_id;
        js.ajax({
            type: 'Get',
            url: uriencoded,
            success: function(data) {
                var users = js.parseJSON(data);
                var rightsarray = js.parseJSON(js("#jform_rights").val());

                js.each(users, function(k, v) {
                    js('#jform_' + k + 'option:selected').removeAttr("selected");
                    js('#jform_' + k).empty().trigger("liszt:updated");

                    js.each(v, function(key, value) {
                        selected = "";
                        js.each(rightsarray, function(i, r) {
                            if (r.user_id == value.id && r.role_id == k) {
                                selected = "selected = selected";
                                return false;
                            }
                        })
                        js('#jform_' + k).append('<option value="' + value.id + '" ' + selected + '>' + value.name + '</option>')
                                .trigger("liszt:updated");
                    });
                });
                js('#loader').hide();
            }})
    }
</script>
<div class="resource-edit front-end-edit">
    <?php if (!empty($this->item->id)): ?>
        <h1><?php echo JText::_('COM_EASYSDI_CORE_TITLE_EDIT_RESOURCE') . ' ' . $this->item->name; ?></h1>
    <?php else: ?>
        <h1><?php echo JText::_('COM_EASYSDI_CORE_TITLE_NEW_RESOURCE'); ?></h1>
    <?php endif; ?>
    <div id="loader" style="">
        <img id="loader_image"  src="administrator/components/com_easysdi_core/assets/images/loader.gif" alt="">
    </div>
    <form class="form-horizontal form-inline form-validate" action="<?php echo JRoute::_('index.php?option=com_easysdi_core&task=resource.save'); ?>" method="post" id="adminForm" name="adminForm" enctype="multipart/form-data">

        <div class="row-fluid">
            <div >
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#details" data-toggle="tab"><?php echo JText::_('COM_EASYSDI_CORE_TAB_DETAILS'); ?></a></li>
                    <li><a href="#publishing" data-toggle="tab"><?php echo JText::_('COM_EASYSDI_CORE_TAB_PUBLISHING'); ?></a></li>
                </ul>

                <div class="tab-content">
                    <!-- Begin Tabs -->
                    <div class="tab-pane active" id="details">
                        <?php foreach ($this->form->getFieldset('details') as $field): ?>
                            <div class="control-group" id="<?php echo $field->fieldname; ?>">
                                <div class="control-label"><?php echo $field->label; ?></div>
                                <div class="controls"><?php echo $field->input; ?></div>
                            </div>
                        <?php endforeach; ?>

                        <div class="control-group">
                            <div class="control-label"><?php echo $this->form->getLabel('organism_id'); ?></div>
                            <div class="controls">
                                <select id="jform_organism_id" name="jform[organism_id]" class="inputbox" size="1" onchange="onChangeOrganism()">
                                    <option value="" ></option>
                                    <?php foreach ($this->user->getResourceManagerOrganisms() as $organism) : ?>
                                        <option value="<?php echo $organism->id; ?>" <?php
                                        if (isset($this->item->organism_id) && $this->item->organism_id == $organism->id) : echo 'selected="selected"';
                                        endif;
                                        ?>>
                                                    <?php echo $organism->name; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="accordion" id="rights">
                            <?php for ($index = 2; $index < 9; $index++) {
                                ?>
                                <div class="accordion-group">
                                    <div class="accordion-heading">
                                        <a class="accordion-toggle" data-toggle="collapse" data-parent="#rights" href="#collapse<?php echo $index; ?>">
                                            <?php echo JText::_('COM_EASYSDI_CORE_FORM_DESC_RESOURCE_' . $index); ?>
                                        </a>
                                    </div>
                                    <div id="collapse<?php echo $index; ?>" class="accordion-body collapse <?php if ($index == 2) echo 'in'; ?>">
                                        <div class="accordion-inner">
                                            <div class="controls">
                                                <select id="jform_<?php echo $index; ?>" name="jform[<?php echo $index; ?>][]" class="multiselect input-xxlarge" multiple="multiple">

                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php }
                            ?>

                        </div>
                    </div>

                    <div class="tab-pane" id="publishing">
                        <?php foreach ($this->form->getFieldset('publishing') as $field): ?>
                            <div class="control-group" id="<?php echo $field->fieldname; ?>">
                                <div class="control-label"><?php echo $field->label; ?></div>
                                <div class="controls"><?php echo $field->input; ?></div>
                            </div>
                        <?php endforeach; ?>
                        <?php if (!empty($this->item->modified)) : ?>
                            <?php foreach ($this->form->getFieldset('publishing_update') as $field): ?>
                                <div class="control-group" id="<?php echo $field->fieldname; ?>">
                                    <div class="control-label"><?php echo $field->label; ?></div>
                                    <div class="controls"><?php echo $field->input; ?></div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                </div>
            </div>
        </div>

        <?php foreach ($this->form->getFieldset('hidden') as $field): ?>
            <?php echo $field->input; ?>
        <?php endforeach; ?>  

        
        <input type = "hidden" name = "task" value = "" />
        <input type = "hidden" name = "option" value = "com_easysdi_core" />
        <?php echo JHtml::_('form.token'); ?>
    </form>

<?php echo $this->getToolbar(); ?>

</div>
