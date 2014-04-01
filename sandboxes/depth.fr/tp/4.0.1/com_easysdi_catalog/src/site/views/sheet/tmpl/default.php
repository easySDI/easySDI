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

JHtml::_('behavior.keepalive');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');

//Load admin language file
$lang = JFactory::getLanguage();
$lang->load('com_easysdi_catalog', JPATH_ADMINISTRATOR);

$document = JFactory::getDocument();
$document->addScript('administrator/components/com_easysdi_core/libraries/easysdi/catalog/addToBasket.js');
?>
<style>
       div.modal {
	position: fixed;
	top: 50%;
	left: 50%;
	z-index: 1050;
	overflow: auto;
	width: 560px;
	margin: -250px 0 0 -280px;
	background-color: #fff;
	border: 1px solid #999;
	border: 1px solid rgba(0,0,0,0.3);
	*border: 1px solid #999;
	-webkit-border-radius: 6px;
	-moz-border-radius: 6px;
	border-radius: 6px;
	-webkit-box-shadow: 0 3px 7px rgba(0,0,0,0.3);
	-moz-box-shadow: 0 3px 7px rgba(0,0,0,0.3);
	box-shadow: 0 3px 7px rgba(0,0,0,0.3);
	-webkit-background-clip: padding-box;
	-moz-background-clip: padding-box;
	background-clip: padding-box;
}
div.modal.fade {
	-webkit-transition: opacity .3s linear;
	-moz-transition: opacity .3s linear;
	-o-transition: opacity .3s linear;
	transition: opacity .3s linear;
	top: -25%;
}
div.modal.fade.in {
	top: 50%;
}
    
</style>
<?php if ($this->item) : ?>
<form class="form-horizontal form-inline form-validate" action="<?php echo JRoute::_('index.php?option=com_easysdi_catalog&'); ?>" method="post" id="adminForm" name="adminForm" enctype="multipart/form-data">
    <div class="metadata-sheet">
<?php
    printf($this->item);
  ?>
    </div>
</form>
<?php
else:
    echo JText::_('COM_EASYSDI_CATALOG_ITEM_NOT_LOADED');
endif;
?>
