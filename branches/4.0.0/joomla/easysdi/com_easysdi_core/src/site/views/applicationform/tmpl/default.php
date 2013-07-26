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
    getScript('//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js', function() {
        js = jQuery.noConflict();
        js(document).ready(function() {
            js('#form-application').submit(function(event) {

            });


        });
    });

</script>

<div class="application-edit front-end-edit">
    <?php if (!empty($this->item->id)): ?>
        <h1><?php echo JText::_('COM_EASYSDI_CORE_TITLE_EDIT_APPLICATION') . ' ' . $this->item->name; ?></h1>
    <?php else: ?>
        <h1><?php echo JText::_('COM_EASYSDI_CORE_TITLE_NEW_APPLICATION'); ?></h1>
    <?php endif; ?>
    
    <form class="form-inline form-validate" action="<?php echo JRoute::_('index.php?option=com_easysdi_core&task=application.save'); ?>" method="post" id="adminForm" name="adminForm" enctype="multipart/form-data">

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
                    </div>

                    <div class="tab-pane" id="publishing">
                        <?php foreach ($this->form->getFieldset('publishing') as $field): ?>
                            <div class="control-group" id="<?php echo $field->fieldname; ?>">
                                <div class="control-label"><?php echo $field->label; ?></div>
                                <div class="controls"><?php echo $field->input; ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <?php foreach ($this->form->getFieldset('hidden') as $field): ?>
            <?php echo $field->input; ?>
        <?php endforeach; ?>  

        <?php echo $this->getToolbar(); ?>
        <input type = "hidden" name = "task" value = "" />
        <input type = "hidden" name = "option" value = "com_easysdi_core" />
        <?php echo JHtml::_('form.token'); ?>
    </form>
</div>