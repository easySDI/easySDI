<?php
global  $mainframe;
$db =& JFactory::getDBO();

//	$cid = JRequest::getVar ('cid', array(0) );
$cid = 	$mainframe->getUserState('productList');
$curstep = JRequest::getVar('step',0);

if (is_array(($cid)))
{
	// do something

	if (count($cid)>0)
	{		
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
		if (is_array($rows))
		{
			if ( count($rows)== 0)
			{
				?>
				<p><?php echo JText::_('EASYSDI_EMPTY_CADDY'); ?></p>
				<?php
			}
			?>
			<table>
			<?php
			foreach($rows as $row )
			{
				
				?>
				<tr>
				<td>
				<a title="<?php echo $row->data_title; ?>" class="modal"
					href="./index.php?tmpl=component&option=com_easysdi_core&task=showMetadata&id=<?php echo $row->metadata_id;  ?>"
					rel="{handler:'iframe',size:{x:500,y:500}}"> <?php echo substr($row->data_title,0,20); ?></a>
				</td>
				<td>
				<?php
				if ($curstep == 2)
				{
				?>
				<div id ="preview_product" 
							<?php if ($row->previewWmsUrl)
								  { 
								  	$currentPreview = JRequest::getVar('previewProductId');
								  	if($currentPreview == $row->id)
								  	{
								  		?> class='previewActivateProductCaddy' 
									  	onClick="document.forms['orderForm'].elements['previewProductId'].value='';submitOrderForm();"
									  	<?php
								  	}
								  	else
								  	{
								  		if($currentPreview)
								  		{
									  		echo "class='previewDisableProductCaddy'";
										}
										else
										{
											?> 
										  	class='previewActivableProductCaddy' 
										  	onClick="document.forms['orderForm'].elements['previewProductId'].value='<?php echo $row->id ; ?>';submitOrderForm();"
										  	<?php
										}
									}
								  }
								  else
								  {
								  	echo "class='previewDisableProductCaddy'";
								  } ;	 ?>>
				</div>
				</td>
				<?php
				}
				 ?>
				<td>
				<div id ="delete_product" onClick="document.forms['orderForm'].elements['task'].value='deleteProduct';
							locOrderForm = document.forms['orderForm'];
							newInput = document.createElement('input');
							newInput.type='hidden';newInput.name='prodId';
							newInput.value='<?php echo $row->id;?>';
							locOrderForm.appendChild(newInput);
							submitOrderForm();"
							class="deleteProductCaddy">
				</div>
				</td>
				</tr>
				
				<?php
			}
			?>
			</table>
			<?php
		}
		
	}
	else
	{
		?>
		<p><?php echo JText::_('EASYSDI_EMPTY_CADDY'); ?></p>
		<?php
	}	
}
else
{
	?>
	<p><?php echo JText::_('EASYSDI_EMPTY_CADDY'); ?></p>
	<?php

}


	?>
