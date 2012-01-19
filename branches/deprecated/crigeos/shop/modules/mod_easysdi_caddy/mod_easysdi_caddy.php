<?php
global  $mainframe;
$db =& JFactory::getDBO();
//$language=&JFactory::getLanguage();
//$language->load('com_easysdi_shop', JPATH_ADMINISTRATOR);
//$language->load('com_easysdi_core', JPATH_ADMINISTRATOR);

//	$cid = JRequest::getVar ('cid', array(0) );
$cid = 	$mainframe->getUserState('productList');
$curstep = JRequest::getVar('step',0);
$currentPreview = JRequest::getVar('previewProductId');
/* todo here, load and merge all perims from each product to see if there is a common one.
otherwise do not let go to step 2.*/
//$arCommonsPerim = Array();
if (is_array(($cid)))
{
	// do something

	if (count($cid)>0)
	{		
		$query = "SELECT  p.*, u.name as user_name, v.metadata_id as metadata_id  , m.guid as metadata_guid FROM #__sdi_product p 
										INNER JOIN #__sdi_objectversion v ON v.id = p.objectversion_id 
										INNER JOIN #__sdi_object o ON o.id = v.object_id 
										INNER JOIN #__sdi_metadata m on m.id = v.metadata_id,
										#__sdi_account a, #__users u where p.id in (";
		foreach( $cid as $id )
		{
			$query = $query.$id."," ;
		}
		$query  = substr($query , 0, -1);
		$query = $query.") AND o.account_id = a.id AND a.user_id = u.id		";

		$db->setQuery( $query );
		$rows = $db->loadObjectList();
		if ($db->getErrorNum()) {
			echo "<div class='alert'>";
			echo "<b>".$db->getErrorMsg()."</b><br>";
			echo "</div>";
		}
		
		//load product common perimeters
		$arCommonsPerim;
		$i = 0;
		foreach( $cid as $id )
		{
			$db->setQuery("SELECT pp.perimeter_id FROM #__sdi_product_perimeter pp where product_id=".$id);
			if($i == 0){
				
				$arCommonsPerim = $db->loadResultArray();
			}
			else
			{
				$arCommonsPerim = array_intersect($arCommonsPerim, $db->loadResultArray());
			}
			
			if ($db->getErrorNum()) 
			{
				echo "<div class='alert'>";
				echo "<b>".$db->getErrorMsg()."</b><br>";
				echo "</div>";
			}
			
			$i++;
		}
		
		if(count($arCommonsPerim) < 1){
			echo "<table><tr><td valign=\"top\"><div class='shopWarnLogoActive'/></td>";
			echo "<td class=\"caddyError\">".JText::_("SHOP_CADDY_MESSAGE_NO_COMMON_PERIMETER")."</td>";
			echo"</tr></table>";
		}
		
		echo "<input type=\"hidden\" id=\"commonPerimCount\" value=\"".count($arCommonsPerim)."\">";
		
		$param = array('size'=>array('x'=>800,'y'=>800) );
		JHTML::_("behavior.modal","a.modal",$param);
		if (is_array($rows))
		{
			if ( count($rows)== 0)
			{
				?>
				<p><?php echo JText::_('SHOP_CADDY_EMPTY'); ?></p>
				<?php
			}
			?>
			<table>
			<?php
			$descriptionLength =  config_easysdi::getValue("SHOP_CONFIGURATION_CADDY_DESC_LENGTH");
			foreach($rows as $row )
			{
				?>
				<tr>
				<td>
				<a class="modal"
					title="<?php echo $row->name." (". $row->user_name.")";  ?>"
					href="./index.php?tmpl=component&option=com_easysdi_core&task=showMetadata&id=<?php echo $row->metadata_guid;  ?>"

					rel="{handler:'iframe',size:{x:650,y:600}}"> <?php echo mb_substr($row->name, 0, $descriptionLength, 'UTF-8');  ?>[...]</a>

				</td>
				<td>
				<?php
				if ($curstep == 2)
				{
				?>
				<div id ="preview_product" 
							<?php if ($row->previewWmsUrl)
								  { 
								  	
								  	if($currentPreview == $row->id)
								  	{
								  		?> class='previewActivateProductCaddy' 
										title="<?php echo JText::_('SHOP_CADDY_DEACTIVATE_PREVIEW');?>" 
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
											title="<?php echo JText::_('SHOP_CADDY_ACTIVATE_PREVIEW');?>" 
										  	onClick="document.forms['orderForm'].elements['previewProductId'].value='<?php echo $row->id ; ?>';submitOrderForm();"
										  	<?php
										}
									}
								  }
								  else
								  {
								  	echo "class='previewDisableProductCaddy' ";
									echo "title=\"".JText::_('SHOP_CADDY_PREVIEW_DISABLED')."\"";
								  } ;	 ?>>
				</div>
				</td>
				<?php
				}
				 ?>
				<td>
				<div id ="delete_product" title="<?php echo JText::_('SHOP_CADDY_REMOVE_FROM_CADDY');?>" onClick="document.forms['orderForm'].elements['task'].value='deleteProduct';
							<?php if ($currentPreview && $currentPreview == $row->id){ ?>document.forms['orderForm'].elements['previewProductId'].value='';<?php } ?>
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
		<p><?php echo JText::_('SHOP_CADDY_EMPTY'); ?></p>
		<?php
	}	
}
else
{
	?>
	<p><?php echo JText::_('SHOP_CADDY_EMPTY'); ?></p>
	<?php

}


	?>
