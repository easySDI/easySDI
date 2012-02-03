<?php
/**
 * EasySDI, a solution to implement easily any spatial data infrastructure
 * Copyright (C) EasySDI Community
 * For more information : www.easysdi.org*
 * 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or 
 * any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see http://www.gnu.org/licenses/gpl.html. 
 */

defined('_JEXEC') or die('Restricted access');

class userTree
{
	static function buildTreeView ($rootUser, $is_frontEnd)
	{
		$database =& JFactory::getDBO();		
		
		if ($is_frontEnd == true)
		{
			?>
			<script type="text/javascript" src="./administrator/components/com_easysdi_core/common/dtree.js"></script>
			<?php
		}
		else
		{
			?>
			<script type="text/javascript" src="components/com_easysdi_core/common/dtree.js"></script>
			<?php
		}
		?><!--  -->
		
		
		<?php 
		$app =& JFactory::getApplication();
		?>

		<div class="dtree">
			<table>
			<tr>
			<td>
			<a href="javascript: d.openAll();"><?php echo JText::_("EASYSDI_USER_TREE_OPEN_ALL"); ?></a> | <a href="javascript: d.closeAll();"><?php echo JText::_("EASYSDI_USER_TREE_CLOSE_ALL"); ?></a>
			</td>
			</tr>
			<tr>
			
			<td>
			<script type="text/javascript">
				d = new dTree('d', '<?php echo JURI::root().'templates/'.$app->getTemplate().'/';  ?>');
				
				d.add(0,-1,'<?php echo addslashes($rootUser->name);  ?>');
				
				<?php 
				$query = "SELECT * FROM #__easysdi_community_partner up, #__users u where up.partner_id != up.parent_id  AND up.parent_id = '$rootUser->partner_id' AND up.user_id = u.id ORDER BY u.name";						
				$database->setQuery( $query );
				$src_list = $database->loadObjectList();
				if (count ($src_list)>0){
					$i = 1;
					userTree::addChildToTree(0,$i,$src_list, $is_frontEnd);				
				}							
				
				?>
			
				document.write(d);
	
			//
			</script>
			</td>
			</tr>
			</table>
		</div>

     <?php
	}
 	
	static function addChildToTree($parentNodeId,&$startId, $childList, $is_frontEnd)
	{
		$database =& JFactory::getDBO();
		//$i = $startId;
		foreach ($childList as $childUser )
		{
			if($is_frontEnd == true)
			{
				$return = 'showPartner';
				if (JRequest::getVar('return') == 'listAffiliatePartner')
				{
					$return = 'listAffiliatePartner';
				}
				
			?>
				d.add(<?php echo $startId?>,<?php echo $parentNodeId?>,'<?php echo addslashes($childUser->name);  ?>','<?php JUri::base(true);?>index.php?search=<?php echo JRequest::getVar('search'); ?>&type=3&affiliate_id=<?php echo $childUser->user_id; ?>&return=<?php echo $return; ?>&option=com_easysdi_core&task=editAffiliateById');
			<?php
			}
			else
			{
				?>
				d.add(<?php echo $startId?>,<?php echo $parentNodeId?>,'<?php echo addslashes($childUser->name);  ?>','<?php JUri::base(true);?>index.php?cid[]=<?php echo $childUser->partner_id; ?>&option=com_easysdi_core&task=editAffiliatePartner');
				<?php
			}
			$query = "SELECT * FROM #__easysdi_community_partner up, 
									#__users u 
					  where up.partner_id != up.parent_id  
					  AND up.parent_id = '$childUser->partner_id' 
					  AND up.user_id = u.id 
					  ORDER BY u.name";						
			$database->setQuery( $query );
			$src_list = $database->loadObjectList();
			if (count ($src_list)>0){
				$i = $startId;
				$startId = $startId + 1;
				userTree::addChildToTree($i,$startId,$src_list, $is_frontEnd);
				//$i = $i + count($src_list);
			}
			$startId = $startId + 1;
		}
	}
}
?>