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
$document->addScript('administrator/components/com_easysdi_core/libraries/openlayers/OpenLayers.debug.js');
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
            submitForm();
        <?php endif; ?>
    });

</script>



<form class="form-horizontal form-validate " action="<?php echo JRoute::_('index.php?option=com_easysdi_catalog&view=catalog&search=true&id=' . $this->item->id . '&preview=' . $this->preview); ?>#results" method="post" id="searchform" name="searchform" enctype="multipart/form-data">
    <?php 
    $tmpl = JFactory::getApplication()->input->get('tmpl', null, 'string');
    if(isset($tmpl)):?>
    <input type="hidden" name="tmpl" id="tmpl" value="<?php echo $tmpl ; ?>"/>
    <?php endif; ?>
    <div class="catalog front-end-edit">
        <fieldset id="searchtype" class="radio btn-group pull-right" style="display: none">
            <input type="radio" id="jform_searchtype_simple" class="input-searchtype"  name="jform[searchtype]" value="simple" <?php if(!$this->isAdvanced()){ echo 'checked="checked"'; }?>>
            <label for="jform_searchtype_simple" class="btn searchtype active"><?php echo JText::_('COM_EASYSDI_CATALOGE_SIMPLE') ; ?></label>
            <input type="radio" id="jform_searchtype_advanced" class="input-searchtype" name="jform[searchtype]" value="advanced" <?php if($this->isAdvanced()){ echo 'checked="checked"'; }?>>
            <label for="jform_searchtype_advanced" class="btn btn-danger searchtype "><?php echo JText::_('COM_EASYSDI_CATALOGE_ADVANCED'); ?></label>
        </fieldset>
        <h1><?php echo JText::_('COM_EASYSDI_CATALOG_TITLE'); ?></h1>

        <div class="well">
            <?php echo $this->getSearchForm(); ?>
            <button id="btn-submit" class="btn btn-primary" type="submit" ><?php echo JText::_('COM_EASYSDI_CATALOGE_SEARCH') ; ?></button>
        </div>
    </div>
</form>

<?php
$results = $this->getResults();
if ($results):
    ?>
    <a name="results"></a>
    <div class="catalog-searchresults">
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


