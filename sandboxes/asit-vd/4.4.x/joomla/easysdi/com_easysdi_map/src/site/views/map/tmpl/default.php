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

if ($this->item) :
    ?>
    
    <?php echo $this->mapscript; ?>

<?php else: ?>
    Could not load the item
<?php endif; ?>
