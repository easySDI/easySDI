<?php
/**
* @version     4.4.5
* @package     com_easysdi_processing
* @copyright   Copyright (C) 2013-2017. All rights reserved.
* @license     GNU General Public License version 3 or later; see LICENSE.txt
* @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
*/
// no direct access
defined('_JEXEC') or die;

$user=sdiFactory::getSdiUser();
if(!$user->isEasySDI) {
    return JError::raiseWarning(403, JText::_('JERROR_ALERTNOAUTHOR'));
}

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('behavior.keepalive');

// Import JS
$document = JFactory::getDocument();
$document->addScript('components/com_easysdi_core/libraries/easysdi/view/view.js?v=' . sdiFactory::getSdiFullVersion());
$document->addScript('components/com_easysdi_processing/assets/js/params_editor.js?v=' . sdiFactory::getSdiFullVersion());
$app = JFactory::getApplication();
$processing=Easysdi_processingHelper::getProcessById($app->input->get('processing', '', 'INT'));
$processing_parameters=json_decode($processing->parameters);


?>
<script type="text/javascript">
    js = jQuery.noConflict();

    Joomla.submitbutton = function(task)
    {
        if(task == 'processing.cancel'){
            Joomla.submitform(task, document.getElementById('processing-form'));
        }
        else{

            if (task != 'processing.cancel' && document.formvalidator.isValid(document.id('processing-form'))) {

                Joomla.submitform(task, document.getElementById('processing-form'));
            }
            else {
                alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
            }
        }
    }
</script>

<script type="text/javascript">

    js = jQuery.noConflict();
    js(document).ready(function() {

        onProductStorageChange();

        js('#adminForm').submit(function(event) {
            if (js('#jform_deposit').val() != '') {
                js('#jform_deposit_hidden').val(js('#jform_deposit').val());
            }
            if (js('#jform_file').val() != '') {
                js('#jform_file_hidden').val(js('#jform_file').val());
            }
        });


        js('#jform_testurlauthentication').click(onTestUrlAuthenticationClick);
        js('#jform_testurlauthentication').parent().append('<span id="result_testurlauthentication"></span>');


    });
    js = jQuery.noConflict();
     js(document).ready(function(){
        enableAccessScope();
    });
    Joomla.submitbutton = function(task)
    {
        if(task == 'processing.cancel'){
            Joomla.submitform(task, document.getElementById('processing-form'));
        }
        else{

            if (task != 'processing.cancel' && document.formvalidator.isValid(document.id('processing-form'))) {

                Joomla.submitform(task, document.getElementById('processing-form'));
            }
            else {
                alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
            }
        }
    }
    function onProductStorageChange() {
        var storage = js("#jform_filestorage :selected").val();
        switch (storage) {
            case "upload":
                js('#file').show();
                js('#fileurl,#userurl, #passurl, #testurlauthentication').hide();
                js('#packageurl').removeAttr('required');
                break;
            case "url":
                js('#file').hide();
                js('#fileurl, #userurl, #passurl, #testurlauthentication').show();
                break;
        }


    }
var globdata;


    function cleanDownload() {
        js('#jform_filestorage').find("option").attr("selected", false);
        js('#jform_fileurl').val('');
        js('#jform_packageurl').val('');
        cleanFile();
    }

     function cleanFile() {
        js('#jform_file').val('');
        js('#jform_file_hidden').val('');
    }
    function cleanDeposit() {
        js('#jform_deposit').val('');
        js('#jform_deposit_hidden').val('');
    }

     function onTestUrlAuthenticationClick(){
        js.ajax({
            url: "<?php echo JRoute::_('index.php?option=com_easysdi_shop&task=diffusion.testURLAccessibility') ?>",
            type: "POST",
            data: {
                url: js('#jform_fileurl').val(),
                user: js('#jform_userurl').val(),
                password: js('#jform_passurl').val()
            }
        }).fail(function(){
            console.log('todo');
        }).done(function(data){
            js('#result_testurlauthentication').removeClass('success error');
            if(data && data.success)
                js('#result_testurlauthentication').html('<?php echo JText::_('COM_EASYSDI_PROCESSING_TEST_URL_AUTHENTICATION_OK',true); ?>').addClass('success');
            else{
                js('#result_testurlauthentication').html('<?php echo JText::_('COM_EASYSDI_PROCESSING_TEST_URL_AUTHENTICATION_FAILURE',true); ?>').addClass('error');                console.log(data);
            }
        }).always(function(){
            js('#jform_testurlauthentication').blur();
        })
        ;

        return false;
    };

    jQuery(function(){

        jQuery("select[data-toggleif]").each(function(){
            var obj=jQuery(this);

            var change=function(){
                var target=obj.data('toggleif');
                target=jQuery(target);
                var selected=obj.find(':selected').val();
                target.each(function(){
                    if (jQuery(this).hasClass('if_'+selected)) {
                        jQuery(this).show();
                    } else {
                        jQuery(this).hide();
                    }
                })
            };
            change();
            obj.change(change);
        });

    })


</script>

<style type="text/css">
    #result_testurlauthentication{
        padding: 5px 0 0 15px;
        display: inline-block;
    }

    #result_testurlauthentication.success{
        color: green;
    }

    #result_testurlauthentication.error{
        color: red;
    }
</style>

<form action="<?php echo JRoute::_('index.php?option=com_easysdi_processing&task=myorder.save') ?>" method="post" enctype="multipart/form-data" name="adminForm" id="processing-form" class="form-validate">
   <div class="row-fluid">
        <div class="span10 form-horizontal">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#details" data-toggle="tab"><?php echo empty($this->item->id) ? JText::_('COM_EASYSDI_PROCESSING_TAB_NEW') : JText::sprintf('COM_EASYSDI_PROCESSING_TAB_EDIT', $this->item->id); ?> : <?php echo $processing->name; ?></a></li>
             <!--   <li><a href="#params-tab" data-toggle="tab"><?php echo JText::_('COM_EASYSDI_PROCESSING_TAB_PARAMS'); ?></a></li>
                <li><a href="#publishing" data-toggle="tab"><?php echo JText::_('COM_EASYSDI_PROCESSING_TAB_PUBLISHING'); ?></a></li>
                <?php if (JFactory::getUser()->authorise('core.admin', 'easysdi_processing')): ?>
                    <li><a href="#permissions" data-toggle="tab"><?php echo JText::_('COM_EASYSDI_PROCESSING_TAB_RULES'); ?></a></li>
                <?php endif ?>-->
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

                <?php if (JFactory::getUser()->authorise('core.admin', 'easysdi_processing')): ?>
                    <div class="tab-pane" id="permissions">
                        <fieldset>
                            <?php echo $this->form->getInput('rules'); ?>
                        </fieldset>
                    </div>
                <?php endif; ?>
            </div>
        </div>

       <?php

        if (null !== $processing_parameters) {
            ?>
            <fieldset id ="fieldset_params">
                <legend>Paramètres du traitement</legend>
                <?php
                foreach ($processing_parameters as $param) {
                    ?>
                    <div class="form-group">
                        <?php echo Easysdi_processingParamsHelper::label($param); ?>
                        <?php echo Easysdi_processingParamsHelper::input($param); ?>
                    </div>
                    <?php
                }
                ?>
            </fieldset>
            <?php
        }

        $dispatcher = JDispatcher::getInstance();
        $plugin_results = $dispatcher->trigger( 'onRenderProcessingOrderForm' ,array($processing, sdiFactory::getSdiUser()));

        if (isset($plugin_results[0])&&isset($plugin_results[0]['html'])) echo $plugin_results[0]['html'];

        ?>
           <br>

        <div class="clr"></div>

        <?php foreach ($this->form->getFieldset('hidden') as $field): ?>
            <?php echo $field->input; ?>
        <?php endforeach; ?>

        <input type="hidden" name="task" value="" />
        <?php echo JHtml::_('form.token'); ?>

        <!-- Begin Sidebar -->
        <!--<div class="span2">
            <h4><?php echo JText::_('JDETAILS'); ?></h4>
            <hr />
            <fieldset class="form-vertical">
                <div class="control-group">
                    <?php if (JFactory::getUser()->authorise('core.edit.state', 'easysdi_processing')): ?>
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
        </div>-->
        <!-- End Sidebar -->
    </div>
</form>
<?php echo $this->getToolbar(); ?>