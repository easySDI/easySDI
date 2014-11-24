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

JHTML::_('behavior.modal');
JHtml::_('formbehavior.chosen', 'select');

$document = JFactory::getDocument();
$document->addScript('administrator/components/com_easysdi_core/libraries/easysdi/catalog/addToBasket.js');
$document->addScript('administrator/components/com_easysdi_core/libraries/easysdi/catalog/searchMetadata.js');
if (JDEBUG) {
$document->addScript('administrator/components/com_easysdi_core/libraries/openlayers/OpenLayers.debug.js');
}
else{
$document->addScript('administrator/components/com_easysdi_core/libraries/openlayers/OpenLayers.js');
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

    js('document').ready(function() {
        <?php if ($this->isAdvanced()): ?>
            showAdvanced();
        <?php endif; ?>
        <?php if ($this->item->oninitrunsearch && JFactory::getApplication()->input->get('search', 'false', 'STRING') == 'false' ): ?>
            //autosubmit search form
            submitForm();
        <?php endif; ?>
        <?php if ($this->item->scrolltoresults && JFactory::getApplication()->input->get('search', 'false', 'STRING') == 'true' ): ?>
            //autoscroll to results
            jQuery(window).scrollTop(jQuery('#sdi-search-results').offset().top);
        <?php endif; ?>
    });

</script>



<form class="form-horizontal form-validate sdi-catalog-fe-search" action="<?php echo JRoute::_('index.php?option=com_easysdi_catalog' ); ?>" method="get" id="searchform" name="searchform" enctype="multipart/form-data">
    <?php 
    $tmpl = JFactory::getApplication()->input->get('tmpl', null, 'string');
    if(isset($tmpl)):?>
    <input type="hidden" name="tmpl" id="tmpl" value="<?php echo $tmpl ; ?>"/>
    <?php endif; ?>
    <input type="hidden" name="view" id="view" value="catalog"/>
    <input type="hidden" name="search" id="search" value="true"/>
    <input type="hidden" name="id" id="id" value="<?php echo $this->item->id; ?>"/>
    <input type="hidden" name="preview" id="preview" value="<?php echo $this->preview ; ?>"/>
    <div class="catalog front-end-edit">
        <fieldset id="searchtype" class="radio btn-group pull-right" style="display: none">
            <input type="radio" id="jform_searchtype_simple" class="input-searchtype"  name="jform[searchtype]" value="simple" <?php if(!$this->isAdvanced()){ echo 'checked="checked"'; }?>>
            <label for="jform_searchtype_simple" class="btn searchtype active"><?php echo JText::_('COM_EASYSDI_CATALOG_SIMPLE') ; ?></label>
            <input type="radio" id="jform_searchtype_advanced" class="input-searchtype" name="jform[searchtype]" value="advanced" <?php if($this->isAdvanced()){ echo 'checked="checked"'; }?>>
            <label for="jform_searchtype_advanced" class="btn btn-danger searchtype "><?php echo JText::_('COM_EASYSDI_CATALOG_ADVANCED'); ?></label>
        </fieldset>
        <h1><?php echo JText::_('COM_EASYSDI_CATALOG_TITLE'); ?></h1>

        <div class="well">
            <?php echo $this->getSearchForm(); ?>
            <button id="btn-submit" class="btn btn-primary" type="submit" ><?php echo JText::_('COM_EASYSDI_CATALOG_SEARCH') ; ?></button>
        </div>
    </div>
</form>

<?php
$results = $this->getResults();
if ($results):
    ?>
    <div class="catalog-searchresults" id="sdi-search-results">
        <h3><?php echo JFactory::getApplication('com_easysdi_catalog')->getUserState('global.list.total') . ' ' . JText::_("COM_EASYSDI_CATALOG_RESULTS_TITLE"); ?></h3>        
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
endif;
?>


