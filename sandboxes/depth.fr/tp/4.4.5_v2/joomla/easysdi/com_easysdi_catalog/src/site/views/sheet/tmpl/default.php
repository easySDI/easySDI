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

JHtml::_('behavior.keepalive');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');

//Load admin language file
$lang = JFactory::getLanguage();
$lang->load('com_easysdi_catalog', JPATH_ADMINISTRATOR);

$document = JFactory::getDocument();
$document->addScript('components/com_easysdi_core/libraries/easysdi/catalog/addToBasket.js?v=' . sdiFactory::getSdiFullVersion());

?>
<?php if ($this->item) : ?>
    <div class="metadata-sheet">
<?php
    printf($this->item);
  ?>
    </div>
<?php
else:
    echo JText::_('COM_EASYSDI_CATALOG_ITEM_NOT_LOADED');
endif;
?>