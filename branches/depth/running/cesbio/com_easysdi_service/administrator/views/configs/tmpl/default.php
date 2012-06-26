<?php
/**
 * @version     3.0.0
 * @package     com_easysdi_service
 * @copyright   Copyright (C) 2012. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */


// no direct access
defined('_JEXEC') or die;

JHtml::_('behavior.tooltip');
JHTML::_('script','system/multiselect.js',false,true);
// Import CSS
$document = &JFactory::getDocument();
$document->addStyleSheet('components/com_easysdi_service/assets/css/easysdi_service.css');

global $mainframe;

$limitstart = JRequest::getVar('limitstart',0);
$limit = JRequest::getVar('limit',15);
$search = JRequest::getVar('search','');



?>

<form action="<?php echo JRoute::_('index.php?option=com_easysdi_service&view=configs'); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		
	</fieldset>
	<div class="clr"> </div>

	<table class="adminlist">
		<thead>
			<tr>
				
				<th width="2%" class='title'></th>
				<th class='title'><b><?php echo JText::_( 'EASYSDI_CONFIGURATION ID'); ?></b></th>
				<th class='title'><b><?php echo JText::_( 'EASYSDI_CONFIGURATION POLICIES_LIST'); ?></b></th>
				<th class='title'><b><?php echo JText::_( 'EASYSDI_CONFIGURATION_TYPE'); ?></b></th>
				<th class='title'><b><?php echo JText::_( 'EASYSDI_CONFIGURATION_URL'); ?></b></th>
	
			</tr>
		</thead>
	<tbody>
	<?php
	$id = JRequest::getVar("configId","");

	$i=0;
	foreach ($this->xml->config as $config) {
		if (!(stripos($config['id'],$search)===False) || !(stripos($config->{'servlet-class'},$search)===False) || strlen($search)==0){
			if (($i>=$limitstart || $limit==0)&& ($i < $limitstart+$limit || $limit==0)){
				$policyFile = $config->{'authorization'}->{'policy-file'};
				
				
				if($config->{'servlet-class'} == "org.easysdi.proxy.wms.WMSProxyServlet")
				{
					$layout = "WMS";
				}
				else if($config->{'servlet-class'} == "org.easysdi.proxy.wmts.WMTSProxyServlet")
				{
					$layout = "WMTS";
				}
				else if($config->{'servlet-class'} == "org.easysdi.proxy.csw.CSWProxyServlet")
				{
					$layout = "CSW";
				}
				else if($config->{'servlet-class'} == "org.easysdi.proxy.wfs.WFSProxyServlet")
				{
					$layout = "WFS";
				}
				
				?>
				
		<tr class="row<?php echo $i%2; ?>">
			<td class="center">
				<?php echo JHtml::_('grid.id', $i, $config['id']); ?>
			</td>
			<td class="center">
				<a href="<?php echo JRoute::_('index.php?option=com_easysdi_service&task=config.edit&cid[]='.$config['id']).'&layout='.$layout;?>"><?php echo $config['id'];?></a>
			</td>
			<td class="center">
				<a href="<?php echo JRoute::_('index.php?option=com_easysdi_service&task=policies.edit&cid[]='.$config['id']);?>">
					<img src="<?php echo JURI::root(true); ?>/includes/js/ThemeOffice/license.png" border="0" />
				</a>
			</td>
			
			<td>
			<?php echo '<b>'.$layout.'</b>'?>
			</td>
			<td><?php	echo $config->{'host-translator'};?></td>
		</tr>
		

		<?php
			}
			$i++;

		}

	}?>
	</tbody>


	</table>

	<div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>