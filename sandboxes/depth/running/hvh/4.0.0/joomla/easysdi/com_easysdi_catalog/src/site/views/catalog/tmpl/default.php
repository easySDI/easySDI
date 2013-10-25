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

//Load admin language file
$lang = JFactory::getLanguage();
$lang->load('com_easysdi_catalog', JPATH_ADMINISTRATOR);
?>
<form class="form-inline form-validate" action="<?php echo JRoute::_('index.php?option=com_easysdi_catalog&view=catalog&search=true&id='.$this->item->id); ?>" method="post" id="adminForm" name="adminForm" enctype="multipart/form-data">
    <div class="catalog front-end-edit">
        <h1><?php echo JText::_('COM_EASYSDI_CATALOG_TITLE'); ?></h1>
        <div class="well">
            <button class="btn btn-primary" type="submit" >Search</button>
        </div>
    </div>
</form>

<?php
if (!empty($this->item->dom)):
    $xpath = new DomXPath($this->item->dom);
    $xpath->registerNamespace('csw', 'http://www.opengis.net/cat/csw/2.0.2');
    $xpath->registerNamespace('gmd', 'http://www.isotc211.org/2005/gmd');
    $nodes = $xpath->query('//csw:SearchResults/gmd:MD_Metadata');
    ?>
    <div class="catalog-searchresults">
        <h3><?php echo JText::_("COM_EASYSDI_CATALOG_RESULTS_TITLE"); ?></h3>        
        <?php
        // Build of extendend XML for each result entry
        foreach ($nodes as $node) :
            $metadata = new cswmetadata();
            $metadata->init($node);
            $metadata->extend($this->item->alias, 'result', 'true', $lang->getTag());
            $result = $metadata->applyXSL($this->item->alias, 'result');
            ?><div class="catalog-searchresult"> <?php
            echo $result;
            ?><hr></div><?php
        endforeach;
        ?>
            
    </div>
    <?php
endif;
?>

