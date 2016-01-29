<?php
/**
 * @version		4.4.0
 * @package     com_easysdi_map
 * @copyright	
 * @license		
 * @author		
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
