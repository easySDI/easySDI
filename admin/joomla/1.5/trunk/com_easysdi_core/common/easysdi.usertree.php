<?php
defined('_JEXEC') or die('Restricted access');

class userTree
{
	static function buildTreeView ($rootUser)
	{
		$database =& JFactory::getDBO();		
		?><!--  -->
		<script type="text/javascript" src="components/com_easysdi_core/common/dtree.js"></script>
		<div class="dtree">
		
			<a href="javascript: d.openAll();">open all</a> | <a href="javascript: d.closeAll();">close all</a>
			<script type="text/javascript">
				d = new dTree('d');
				
				d.add(0,-1,'<?php echo $rootUser->name;  ?>');
				
				<?php 
				$query = "SELECT * FROM #__easysdi_community_partner up, #__users u where up.partner_id != up.parent_id  AND up.parent_id = '$rootUser->partner_id' AND up.user_id = u.id ORDER BY partner_id";						
				$database->setQuery( $query );
				$src_list = $database->loadObjectList();
				if (count ($src_list)>0){
					userTree::addChildToTree(0,1,$src_list);				
				}							
				
				?>
			
				document.write(d);
	
			//
			</script>
		</div>

     <?php
	}
 	
	static function addChildToTree($parentNodeId,$startId, $childList)
	{
		$database =& JFactory::getDBO();
		$i = $startId;
		foreach ($childList as $childUser )
		{
			?>	
				d.add(<?php echo $i?>,<?php echo $parentNodeId?>,'<?php echo $childUser->name;  ?>','example01.html');
			<?php
			$query = "SELECT * FROM #__easysdi_community_partner up, #__users u where up.partner_id != up.parent_id  AND up.parent_id = '$childUser->partner_id' AND up.user_id = u.id ORDER BY partner_id";						
			$database->setQuery( $query );
			$src_list = $database->loadObjectList();
			if (count ($src_list)>0){
				userTree::addChildToTree($i,$i + 1,$src_list);
				$i = $i + count($src_list);
			}
			$i++;
		}
	}
}
?>