<?php
/**
 * @version		4.4.0
 * @package     com_easysdi_monitor
 * @copyright	
 * @license		
 * @author		
 */

// no direct access
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('behavior.keepalive');

// Import CSS
$document = JFactory::getDocument();
$document->addStyleSheet('components/com_easysdi_monitor/assets/css/easysdi_monitor.css');
?>
<script type="text/javascript">
    
    
	Joomla.submitbutton = function(task)
	{
        
		if (task == 'main.cancel' || document.formvalidator.isValid(document.id('main-form'))) {
			Joomla.submitform(task, document.getElementById('main-form'));
		}
		else {
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
	}
</script>

