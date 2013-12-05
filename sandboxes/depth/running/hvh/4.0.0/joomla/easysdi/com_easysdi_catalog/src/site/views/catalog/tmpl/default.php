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
    <div class="catalog front-end-edit">
        <fieldset id="searchtype" class="radio btn-group pull-right" style="display: none">
            <input type="radio" id="jform_searchtype_simple" name="jform[searchtype]" value="simple" checked="checked">
            <label for="jform_searchtype_simple" class="btn searchtype active">Simple</label>
            <input type="radio" id="jform_searchtype_advanced" name="jform[searchtype]" value="advanced" >
            <label for="jform_searchtype_advanced" class="btn btn-danger searchtype ">Avancée</label>
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


