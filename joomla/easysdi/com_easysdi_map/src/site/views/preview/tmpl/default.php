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

$doc = JFactory::getDocument();
$doc->addScript(Juri::base(true) . '/components/com_easysdi_map/views/preview/tmpl/preview.js');

if (!empty($this->mapscript)) :
    ?>
    <?php echo $this->mapscript; ?>
    <script>
       <?php echo $this->addscript; ?>
    </script>

<?php else: 
     echo JText::_('COM_EASYSDI_MAP_PREVIEW_NOT_FOUND'); 
 endif; ?>
