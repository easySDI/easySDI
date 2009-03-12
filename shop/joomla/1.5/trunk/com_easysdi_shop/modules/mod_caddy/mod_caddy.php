<?php		
global  $mainframe;
$db =& JFactory::getDBO(); 
		
//	$cid = JRequest::getVar ('cid', array(0) );
	$cid = 		$mainframe->getUserState('productList');
	
	if (is_array(($cid)))
		{
  // do something

			
if (count($cid)>0){
	$query = "select * from #__easysdi_product where id in (";
	foreach( $cid as $id )
		{
			$query = $query.$id."," ;
		}
		$query  = substr($query , 0, -1);
		$query = $query.")";
		
	$db->setQuery( $query );
	$rows = $db->loadObjectList();
	if ($db->getErrorNum()) {						
			echo "<div class='alert'>";									
				echo "<b>".$db->getErrorMsg()."</b><br>";
				echo "</div>";
	}

	$param = array('size'=>array('x'=>800,'y'=>800) );
	JHTML::_("behavior.modal","a.modal",$param);	
	if (is_array($rows)){
		foreach($rows as $row )
		{
			?>
	
			<a title="<?php echo $row->data_title; ?>" class="modal" href="./index.php?tmpl=component&option=com_easysdi_core&task=showMetadata&id=<?php echo $row->metadata_id;  ?>" rel="{handler:'iframe',size:{x:500,y:500}}"> <?php echo substr($row->data_title,0,20)."..."; ?></a>												
			<button type="button" onClick="document.getElementById('task').value='deleteProduct';locOrderForm = document.getElementById('orderForm');newInput = document.createElement('input');newInput.type='hidden';newInput.name='prodId';newInput.value='<?php echo $row->id;?>';locOrderForm.appendChild(newInput);document.getElementById('orderForm').submit();"> <?php JText::_('EASYSDI_REMOVE_PRODUCT'); ?></button>
			<hr>
			
			<?php 
		}	
	}
		}}
?>
