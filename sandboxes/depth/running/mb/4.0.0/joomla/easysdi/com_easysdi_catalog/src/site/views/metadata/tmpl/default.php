<?php
require_once JPATH_BASE . '/components/com_easysdi_catalog/libraries/luminous/luminous.php';
/**
 * @version     4.0.0
 * @package     com_easysdi_catalog
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org§> - http://www.easysdi.org
 */
// no direct access
defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');

//Load admin language file
$lang = JFactory::getLanguage();
$lang->load('com_easysdi_catalog', JPATH_ADMINISTRATOR);
$document = JFactory::getDocument();

$document->addStyleSheet('administrator/components/com_easysdi_core/libraries/ext/resources/css/ext-all.css');
$document->addScript('administrator/components/com_easysdi_core/libraries/easysdi/catalog/bootbox.min.js');
$document->addScript('administrator/components/com_easysdi_core/libraries/openlayers/OpenLayers.debug.js');
$document->addScript('administrator/components/com_easysdi_core/libraries/proj4js-1.4.1/dist/proj4.js');
$document->addScript('administrator/components/com_easysdi_core/libraries/ext/adapter/ext/ext-base-debug.js');
$document->addScript('administrator/components/com_easysdi_core/libraries/ext/ext-all-debug.js');
$document->addScript('administrator/components/com_easysdi_core/libraries/ext/ext-all-debug.js');
$document->addScript('administrator/components/com_easysdi_core/libraries/gemetclient-2.0.0/src/thesaur.js');
$document->addScript('administrator/components/com_easysdi_core/libraries/gemetclient-2.0.0/src/HS.js');
$document->addScript('administrator/components/com_easysdi_core/libraries/gemetclient-2.0.0/src/translations.js');

$document->addScript('http://maps.google.com/maps/api/js?v=3&amp;sensor=false');

$document->addScript('administrator/components/com_easysdi_core/libraries/syntaxhighlighter/scripts/shCore.js');
$document->addScript('administrator/components/com_easysdi_core/libraries/syntaxhighlighter/scripts/shBrushXml.js');

$document->addScript('administrator/components/com_easysdi_core/libraries/easysdi/catalog/editMetadata.js');

$document->addStyleSheet('administrator/components/com_easysdi_core/libraries/syntaxhighlighter/styles/shCore.css');
$document->addStyleSheet('administrator/components/com_easysdi_core/libraries/syntaxhighlighter/styles/shThemeDefault.css');
?>

<style>

    .action-1{
        font-size: 15px;
    }
    .legend-1{
        font-size: 16px;
    }

    .action-2, .action-3{
        font-size: 13px;
    }
    .legend-2, .legend-3{
        font-size: 14px;
    }

    .inner-fds{
        padding-left:15px;
        border-left: 1px solid #BDBDBD;
    }

    .collapse-btn, .neutral-btn{
        margin-right: 10px;
    }

    .add-btn, .empty-btn, .preview-btn{
        margin-left: 10px;
    }

    legend{
        font-size: 12px;
    }

    img.olTileImage{
        max-width: none;
    }

    svg {
        max-width :none !important;
    }


</style>

<script type="text/javascript">

    js = jQuery.noConflict();
    js('document').ready(function() {

<?php
foreach ($this->validators as $validator) {

    echo $validator;
}
?>
    });

</script>

<div class="metadata-edit front-end-edit">

    <button id="btn_toogle_all" action="open" class="btn btn-small"><?php echo JText::_('COM_EASYSDI_CATALOGE_TITLE_OPEN_ALL'); ?></button>

    <div>
        <?php echo $this->getActionToolbar(); ?>
    </div>
    <div>
        <h2><?php echo JText::_('COM_EASYSDI_CATALOGE_TITLE_EDIT_METADATA') . ' ' . $this->item->guid; ?></h2>
    </div>

    <form id="form-metadata" action="<?php echo JRoute::_('index.php?option=com_easysdi_catalog&task=metadata.save'); ?>" method="post" class="form-validate form-horizontal" enctype="multipart/form-data">


        <div class ="well">
            <?php echo $this->formHtml; ?>

            <?php foreach ($this->form->getFieldset('hidden') as $field): ?>
                <?php echo $field->input; ?>
            <?php endforeach; ?>
            <input type="hidden" name="option" value="com_easysdi_catalog" />
            <input type="hidden" name="task" value="" />

        </div>

        <div>
            <?php echo $this->getActionToolbar(); ?>

            <button type="submit" class="validate"><span><?php echo JText::_('JSUBMIT'); ?></span></button>
            <?php echo JText::_('or'); ?>
            <a href="<?php echo JRoute::_('index.php?option=com_easysdi_catalog&task=metadata.cancel'); ?>" title="<?php echo JText::_('JCANCEL'); ?>"><?php echo JText::_('JCANCEL'); ?></a>



            <?php echo JHtml::_('form.token'); ?>
        </div>
    </form>
    <button class="btn btn-primary btn-lg" data-toggle="modal" data-target="#myModal">
        Launch demo modal
    </button>

    <!-- Modal -->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel">Modal title</h4>
                </div>
                <div class="modal-body">
                    Hello
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save changes</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
</div>
