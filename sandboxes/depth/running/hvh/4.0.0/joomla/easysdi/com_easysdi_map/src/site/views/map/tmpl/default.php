<?php
/**
 * @version     4.0.0
 * @package     com_easysdi_map
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
// no direct access
defined('_JEXEC') or die;

//Load admin language file
$lang = JFactory::getLanguage();
$lang->load('com_easysdi_map', JPATH_ADMINISTRATOR);

if ($this->item) :
    ?>
    <div id="sdimapcontainer" class="cls-sdimapcontainer">
    </div>
    <?php echo $this->item->text; ?>

<?php else: ?>
    Could not load the item
<?php endif; ?>
