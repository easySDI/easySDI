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
jimport("joomla.html.pagination");
$limitstart = JRequest::getVar('limitstart',0);
$limit = JRequest::getVar('limit',15);
$search = JRequest::getVar('search','');
?>
<form action="<?php echo JRoute::_('index.php?option=com_easysdi_service&view=policies&'); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<div class="filter-search fltlft">
			<label class="filter-search-lbl" for="filter_search"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
			<input type="text" name="search" id="search" value="<?php echo $search;?>" class="inputbox" onchange="document.adminForm.submit();"  />	
			<button type="submit"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
			<button type="button" onclick="document.id('search').value='';this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
		</div>
	</fieldset>
	<div class="clr"> </div>

	<table class="adminlist">
		<thead>
			<tr>
				
				<th width="2%" class='title'></th>
				<th class='title'><b><?php echo JText::_( 'COM_EASYSDI_SERVICE_POLICY_ID'); ?></b></th>
				<th class='title'><b><?php echo JText::_( 'COM_EASYSDI_SERVICE_POLICY_USER'); ?></b></th>
				<th class='title'><b><?php echo JText::_( 'COM_EASYSDI_SERVICE_POLICY_ORDER'); ?></b></th>
			</tr>
		</thead>
	<tbody>
	<?php
	$isChecked = false;
	$it=0;
	foreach ($this->xml->config as $config) {
		if (strcmp($config['id'],$this->config )==0){
			$policyFile = $config->{'authorization'}->{'policy-file'};
			if (!file_exists($policyFile)){
					JError::raiseError(500, JText::_( 'COM_EASYSDI_SERVICE_POLICY_LOAD_ERROR'));
					return false;
			}
			
			if (file_exists($policyFile)) {
				$xmlConfigFile = simplexml_load_file($policyFile);
				foreach ($xmlConfigFile->Policy as $policy){
					if (strcmp($policy['ConfigId'],$this->config)==0){
						$it++;	
					}
				}
			}
		}
	}
	
	$pageNav = new JPagination($it,$limitstart,$limit);
	
	foreach ($this->xml->config as $config) {
		if (strcmp($config['id'],$this->config)==0){
			$servletClass=$config->{'servlet-class'};
			$policyFile = $config->{'authorization'}->{'policy-file'};
			$i=0;
			$count=0;
			if (!file_exists($policyFile)){
					JError::raiseError(500, JText::_( 'COM_EASYSDI_SERVICE_POLICY_LOAD_ERROR'));
					return false;
			}
			
			if (file_exists($policyFile)) {
				$xmlConfigFile = simplexml_load_file($policyFile);
			
				foreach ($xmlConfigFile->Policy as $policy){
					if (strcmp($policy['ConfigId'],$this->config)==0){
						if ( (!(stripos($policy['Id'],$search)===False)) || strlen($search)==0){
							if (($count>=$limitstart || $limit==0)&& ($count < $limitstart+$limit || $limit==0)){
								?>
								<tr class="row<?php echo $i%2;?>">
									<td class="center">
										<?php echo JHtml::_('grid.id', $i, $policy['Id']); ?>
									</td>
									<td>
										<a href="<?php echo JRoute::_('index.php?option=com_easysdi_service&task=policy.edit&config='.$this->config.'&cid[]='.$policy['Id'].'&connector='.$this->connector);?>"><?php echo $policy['Id']; ?></a>				
									</td>
									<td><?php  
										if (strcasecmp($policy->{'Subjects'}['All'],"true")==0){
											echo JText::_( 'COM_EASYSDI_SERVICE_POLICY_ALL_USERS');
										}else{
											if (count($policy->Subjects->Role)>0){
												foreach ($policy->Subjects->Role as $role){
													echo  JText::_( $role).",";
												}
											}
											if (count($policy->Subjects->User)>0){
												foreach ($policy->Subjects->User as $user){
													echo  JText::_( $user).",";
												}
											}
										}?>
									</td>
									<td class="order">
										<span><?php echo $pageNav->orderUpIcon($i,  true, 'policy.orderuppolicy', 'Move Up'); ?></span>
						            	<span><?php echo $pageNav->orderDownIcon($i,$it,  true, 'policy.orderdownpolicy', 'Move Down' ); ?></span>                       
						        	</td>
								</tr>
								<?php
								$i++;
							}
							$count++;
						}
					}
				}
			}
		}
	}
	?>
	</tbody>
	<tfoot>
		<td colspan="7"><?php echo $pageNav->getListFooter(); ?></td>
	</tfoot>
	</table>
	<div>
		<input type='hidden' name='config' id='config' value='<?php echo $this->config;?>'>
		<input type='hidden' name='connector' id='connector' value="<?php echo $this->connector;?>" >
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>