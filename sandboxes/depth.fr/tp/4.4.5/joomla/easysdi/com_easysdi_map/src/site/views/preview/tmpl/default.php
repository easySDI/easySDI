<?php
/**
 * @version     4.4.5
 * @package     com_easysdi_map
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
// no direct access
defined('_JEXEC') or die;

JHTML::_('behavior.modal');

$doc = JFactory::getDocument();
$doc->addScript(Juri::base(true) . '/components/com_easysdi_map/views/preview/tmpl/preview.js?v=' . sdiFactory::getSdiFullVersion());

if (!empty($this->mapscript)) :
    ?>
    <?php echo $this->mapscript; ?>
    <script>
       <?php echo $this->addscript; ?>
    </script>

<?php else: 
     echo JText::_('COM_EASYSDI_MAP_PREVIEW_NOT_FOUND'); 
 endif; ?>
