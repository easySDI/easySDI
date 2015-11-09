<?php
/**
 * @version     3.3.0
 * @package     com_easysdi_monitor
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */

// no direct access
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');

// Import CSS
$document = JFactory::getDocument();
$document->addStyleSheet('components/com_easysdi_monitor/assets/css/easysdi_monitor.css');

//Load css files
$document->addStyleSheet('components/com_easysdi_monitor/libraries/ext/resources/css/ext-all.css');
$document->addStyleSheet('components/com_easysdi_monitor/libraries/ext/resources/css/xtheme-gray.css');
//$document->addStyleSheet('components/com_easysdi_monitor/libraries/ext/examples/shared/examples.css');
$document->addStyleSheet('components/com_easysdi_monitor/libraries/ext/examples/ux/css/RowEditor.css');
$document->addStyleSheet('components/com_easysdi_monitor/assets/css/monitor.css');

//Common js lib
$document->addScript('components/com_easysdi_monitor/libraries/jquery/jquery.min.js');
$document->addScript('components/com_easysdi_monitor/libraries/ext/adapter/ext/ext-base.js');
$document->addScript('components/com_easysdi_monitor/libraries/ext/ext-all.js');
$document->addScript('components/com_easysdi_monitor/libraries/Highcharts-2.0.3/js/highcharts.js');
$document->addScript('components/com_easysdi_monitor/libraries/Highcharts-2.0.3/js/modules/exporting.js');
$document->addScript('components/com_easysdi_monitor/libraries/ext/examples/ux/RowEditor.js');
$document->addScript('components/com_easysdi_monitor/libraries/ext/examples/ux/RowExpander.js');
$document->addScript('components/com_easysdi_monitor/views/mains/js/ApplicationSettings.js');

//minified files -> use for prod
$document->addScript('components/com_easysdi_monitor/views/mains/js/Monitor.js');
$document->addScript('components/com_easysdi_monitor/views/mains/js/themes.js');

$user	= JFactory::getUser();
$userId	= $user->get('id');
$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');
$canOrder	= $user->authorise('core.edit.state', 'com_easysdi_monitor');
$saveOrder	= $listOrder == 'a.ordering';
if ($saveOrder)
{
	$saveOrderingUrl = 'index.php?option=com_easysdi_monitor&task=mains.saveOrderAjax&tmpl=component';
	JHtml::_('sortablelist.sortable', 'mainList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}


require_once( JPATH_COMPONENT_ADMINISTRATOR.'/'.'i18n'.'/'.'lang.php' );

?>
<script type="text/javascript">
	Joomla.orderTable = function() {
		table = document.getElementById("sortTable");
		direction = document.getElementById("directionTable");
		order = table.options[table.selectedIndex].value;
		if (order != '<?php echo $listOrder; ?>') {
			dirn = 'asc';
		} else {
			dirn = direction.options[direction.selectedIndex].value;
		}
		Joomla.tableOrdering(order, dirn, '');
	}
        
        var $ = document; // shortcut
       
            var head  = $.getElementsByTagName('head')[0];
            var link  = $.createElement('link');
            link.rel  = 'stylesheet';
            link.type = 'text/css';
            link.href = 'components/com_easysdi_monitor/libraries/ext/resources/css/ext-all.css';
            link.media = 'all';
           // head.appendChild(link);
            
          var link  = $.createElement('link');
            link.rel  = 'stylesheet';
            link.type = 'text/css';
            link.href = 'components/com_easysdi_monitor/libraries/ext/resources/css/xtheme-gray.css';
            link.media = 'all';
            //head.appendChild(link);
            
           //
</script>

<?php
//Joomla Component Creator code to allow adding non select list filters
if (!empty($this->extra_sidebar)) {
    $this->sidebar .= $this->extra_sidebar;
}
?>

<?php if(!empty($this->sidebar)): ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
<?php else : ?>
	<div id="j-main-container">
<?php endif;?>

<div id="tabsContainer"></div>
<table id="graphTable" style="width: 100%">
 <tr>                                   
  <td style="width: 50%">
   <div id="container1" style="width: 100%"></div>
  </td>
  <td style="width: 50%">
   <div id="container2" style="width: 100%"></div>
  </td>
 </tr>
 <tr>
  <td style="width: 50%">
   <div id="container3" style="width: 100%"></div>
  </td>
  <td style="width: 50%">
   &nbsp;
  </td>
 </tr>
</table>



        

		
