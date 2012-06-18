<?php
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
				d = new dTree('d', '<?php echo JURI::root(true).'/templates/easysdi/';  ?>');
				
				d.add(0,-1,'<?php echo addslashes($rootUser->name);  ?>');
				
				<?php 
				$query = "SELECT *, up.id as account_id FROM #__sdi_account up, #__users u where up.id != up.parent_id  AND up.parent_id = '$rootUser->account_id' AND up.user_id = u.id ORDER BY u.name";						
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
				$return = 'showAccount';
				if (JRequest::getVar('return') == 'listAffiliateAccount')
				{
					$return = 'listAffiliateAccount';
				}
				
			?>
				d.add(<?php echo $startId?>,<?php echo $parentNodeId?>,'<?php echo addslashes($childUser->name);  ?>','<?php JUri::base(true);?>index.php?search=<?php echo JRequest::getVar('search'); ?>&type=3&affiliate_id=<?php echo $childUser->user_id; ?>&return=<?php echo $return; ?>&option=com_easysdi_core&task=editAffiliateById');
			<?php
			}
			else
			{
				?>
				d.add(<?php echo $startId?>,<?php echo $parentNodeId?>,'<?php echo addslashes($childUser->name);  ?>','<?php JUri::base(true);?>index.php?cid[]=<?php echo $childUser->account_id; ?>&type=<?php echo $childUser->root_id; ?>&option=com_easysdi_core&task=editAffiliateAccount');
				<?php
			}
			$query = "SELECT * FROM #__sdi_account up, 
					  #__users u 
					  where up.id != up.parent_id  
					  AND up.parent_id = '$childUser->account_id' 
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