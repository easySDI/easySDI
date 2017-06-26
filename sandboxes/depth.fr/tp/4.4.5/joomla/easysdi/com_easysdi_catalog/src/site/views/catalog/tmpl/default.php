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

JHTML::_('behavior.modal');
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');

$document = JFactory::getDocument();
$document->addScript('components/com_easysdi_core/libraries/easysdi/catalog/addToBasket.js?v=' . sdiFactory::getSdiFullVersion());
$document->addScript('components/com_easysdi_core/libraries/easysdi/catalog/searchMetadata.js?v=' . sdiFactory::getSdiFullVersion());
if (JDEBUG) {
    $document->addScript('components/com_easysdi_core/libraries/OpenLayers-2.13.1/OpenLayers.debug.js');
} else {
    $document->addScript('components/com_easysdi_core/libraries/OpenLayers-2.13.1/OpenLayers.js');
}
?>
<style>
    ul {
        list-style: none;
    }

    .pagination{
        text-align: center;
    }
</style>

<script type="text/javascript">
    js = jQuery.noConflict();

    js('document').ready(function () {
        if (!js('[name="advanced"]').length) {
            js("#searchtype").hide();
        }
<?php if ($this->isAdvanced()): ?>
            toogleAdvanced();
<?php endif; ?>

<?php if ($this->item->oninitrunsearch && JFactory::getApplication()->input->get('search', 'false', 'STRING') == 'false'): ?>
            //autosubmit search form
            submitForm();
<?php endif; ?>
<?php if ($this->item->scrolltoresults && JFactory::getApplication()->input->get('search', 'false', 'STRING') == 'true'): ?>
            //autoscroll to results
            var sdiSearchResults = jQuery('#sdi-search-results');
            if (sdiSearchResults.length)
                jQuery(window).scrollTop(sdiSearchResults.offset().top);
<?php endif; ?>
    });

</script>



<form class="form-horizontal form-validate sdi-catalog-fe-search" action="<?php echo JRoute::_('index.php?option=com_easysdi_catalog'); ?>#results" method="get" id="searchform" name="searchform" enctype="multipart/form-data">
    <?php
    $tmpl = JFactory::getApplication()->input->get('tmpl', null, 'string');
    if (isset($tmpl)):
        ?>
        <input type="hidden" name="tmpl" id="tmpl" value="<?php echo $tmpl; ?>"/>
    <?php endif; ?>
    <input type="hidden" name="option" id="option" value="com_easysdi_catalog"/>
    <input type="hidden" name="view" id="view" value="catalog"/>
    <input type="hidden" name="search" id="search" value="true"/>
    <input type="hidden" name="id" id="id" value="<?php echo $this->item->id; ?>"/>
    <input type="hidden" name="preview" id="preview" value="<?php echo $this->preview; ?>"/>
    <div class="catalog front-end-edit">
        <fieldset id="searchtype" class="radio btn-group pull-right">
            <input type="radio" id="jform_searchtype_simple"  name="jform[searchtype]" value="simple" <?php if (!$this->isAdvanced()) {
        echo 'checked="checked"';
    } ?>>
            <label id="lbl_simple" for="jform_searchtype_simple" class="btn searchtype"><?php echo JText::_('COM_EASYSDI_CATALOG_SIMPLE'); ?></label>
            <input type="radio" id="jform_searchtype_advanced" name="jform[searchtype]" value="advanced" <?php if ($this->isAdvanced()) {
        echo 'checked="checked"';
    } ?>>
            <label id="lbl-advanced" for="jform_searchtype_advanced" class="btn searchtype"><?php echo JText::_('COM_EASYSDI_CATALOG_ADVANCED'); ?></label>
        </fieldset>
        <h1><?php echo JText::_('COM_EASYSDI_CATALOG_TITLE'); ?></h1>

        <div class="well">
<?php echo $this->getSearchForm(); ?>
            <button id="btn-submit" class="btn btn-primary" type="submit" ><?php echo JText::_('COM_EASYSDI_CATALOG_SEARCH'); ?></button>
        </div>
    </div>
</form>

<?php
$results = $this->getResults();
if ($results):
    ?>
    <div class="catalog-searchresults" id="sdi-search-results">
        <h3><?php echo JText::plural("COM_EASYSDI_CATALOG_N_RESULTS",JFactory::getApplication('com_easysdi_catalog')->getUserState('global.list.total')); ?></h3>
        <?php
            // Build of extendend XML for each result entry
            foreach ($results as $result) :
                ?><div class="offset1 catalog-searchresult"> <?php
                echo $result;
                ?></div>
            <hr><?php endforeach; ?>

    </div>
    <div class="pagination">
        <p class="counter">
    <?php echo $this->pagination->getPagesCounter(); ?>
        </p>
    <?php echo $this->pagination->getPagesLinks(); ?>
    </div>

    <?php
else:
    //empty results string, or article if provided
    echo $this->getEmptyResultContent(EText::_($this->item->guid,2,''));
endif;
?>


