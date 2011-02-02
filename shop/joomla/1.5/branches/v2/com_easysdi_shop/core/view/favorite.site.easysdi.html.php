<?php
/**
 * EasySDI, a solution to implement easily any spatial data infrastructure
 * Copyright (C) EasySDI Community
 * For more information : www.easysdi.org
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

class HTML_favorite{
	
	function manageFavoriteProduct ($option,$countMD,$rows,$orderableProductsMd,$notificationList,$total,$limitstart,$limit)
	{
		$db =& JFactory::getDBO();
		?>
		<div id="page">
		<h2 class="contentheading"><?php echo JText::_("SHOP_FAVORITE_TITLE"); ?></h2>
		<div class="contentin">
		<!--
			//Call here the include content item plugin, or a specific article.
			//Insert into the EasySDI config a key FAVORITE_ARTICLE_TOP with
			//the value like {include_content_item 148} refering to the plugin and
			//article you would like to call.
			//-->       
		
			<table width="100%" id="infoStep4">
				<?php
//				$row->text = config_easysdi::getValue("FAVORITE_ARTICLE_TOP");
//				$args = array( 1,&$row,&$params);
//				
//				JPluginHelper::importPlugin( 'content' );
//				$dispatcher =& JDispatcher::getInstance();
//				//$params = & new JParameter('');
//				$results = $dispatcher->trigger('onPrepareContent', 
//				array(&$row,&$params,0));
//				echo $row->text;
				?>
			</table>
			<script>
				function submitOrderForm()
		 		{
		 			document.getElementById('orderForm').submit();
		 		}
		 	</script>
			
			<form name="orderForm" id="orderForm" action='<?php echo JRoute::_(displayManager::buildUrl("index.php")) ?>' method='POST'>

				<?php $pageNav = new JPagination($total,$limitstart,$limit); ?>
				<br/>
				<table width="100%">
					<tr>
						<td align="left"><?php echo $pageNav->getPagesCounter(); ?></td>
						<td align="center"><?php echo JText::_("CORE_SHOP_DISPLAY"); ?>
						<?php echo $pageNav->getLimitBox(); ?>
						</td>
						<td align="right"> <?php echo $pageNav->getPagesLinks(); ?></td>
					</tr>
				</table>
			 	<h3><?php echo JText::_("SHOP_FAVORITE_RESOURCE_TITLE"); ?></h3>
						 			 
				<input type='hidden' name='option' value='<?php echo $option;?>'>
				<input type='hidden' id ="task" name='task' value='manageFavoriteProduct'>
				<input type='hidden'  name='limitstart' value="<?php echo  $limitstart; ?>">
				<input type="hidden" name="countMD" value="<?php echo $countMD;?>">		
				<input type="hidden" id="metadata_guid" name="metadata_guid" value="">
				<input type="hidden" id="favorite_id" name="favorite_id" value="">
				
				<!--<span class="searchCriteria">
					-->
					<table width="100%">
					   <tr>
					   	<td colspan="3" align="left"><?php echo JText::_("SHOP_FAVORITE_NUMBER_FOUND");?> : <?php echo $total; ?></td>
					   </tr>
					</table>
					
					<table id="favoriteManTable" class="box-table" width="100%">
					<thead>
						<tr>
							<th class="logo">&nbsp;</th>
							<th class="ptitle"><?php echo JText::_("SHOP_FAVORITE_ITEM_TITLE"); ?></th>
							<th class="logo">&nbsp;</th>
							<th class="logo">&nbsp;</th>
							<th class="logo">&nbsp;</th>
						</tr>
					</thead>
					<tbody>
					<?php
					$param = array('size'=>array('x'=>800,'y'=>800) );
					JHTML::_("behavior.modal","a.modal",$param);	
					$i=0;	
					//Display all the products 
					foreach ($rows  as $row)
					{
					
						$queryPartnerLogo = "select logo from #__sdi_account where id = ".$row->provider_id;
						$db->setQuery($queryPartnerLogo);
						$partner_logo = $db->loadResult();
						
						$query = "select count(*) FROM #__sdi_product p
												  INNER JOIN #__sdi_objectversion ov ON ov.id = p.objectversion_id
												  WHERE p.viewurlwms != '' AND ov.metadata_id = ".$row->metadata_id;
						$db->setQuery( $query);
						$hasPreview = $db->loadResult();
						
						$hasOrderableProduct = false;
						if (in_array($row->metadata_id, $orderableProductsMd))
							$hasOrderableProduct = true;
							
						?>
	
							<tr>		
								<td>
								   <img height="18px" width="18px" src="<?php echo $partner_logo;?>" title="<?php echo $row->provider_name;?>"></img>
								</td>
								<td width="100%">
									<span class="mdtitle" >
										<a class="modal" title="<?php echo JText::_("SHOP_FAVORITE_VIEW_MD"); ?>" 
										   href="./index.php?tmpl=component&option=com_easysdi_core&task=showMetadata&id=<?php echo $row->metadata_guid;  ?>" 
										   rel="{handler:'iframe',size:{x:650,y:600}}"> 
										   <?php echo $row->title; ?>
										</a>
									</span>
								</td>
								<td class="logo">
									<div title="<?php echo JText::_('SHOP_FAVORITE_REMOVE_FROM_FAVORITE'); ?>" 
										 class="pdFavorite" 
										 id="chooseFavorite" 
										 onClick="$('orderForm').metadata_guid.value='<?php echo $row->metadata_guid; ?>';$('orderForm').task.value='removeFavorite'; submitOrderForm();"></div>
								</td>
								<td class="logo">
									<div title="<?php if ( in_array($row->metadata_id,$notificationList)) echo JText::_('SHOP_FAVORITE_REMOVE_NOTIFICATION'); else echo JText::_('SHOP_FAVORITE_ADD_NOTIFICATION'); ?>" 
										 class="<?php if ( in_array($row->metadata_id,$notificationList)) echo "pdNotificated"; else echo "pdNotNotificated"; ?>" 
										 id="chooseNotification" 
										 onClick="$('orderForm').favorite_id.value='<?php echo $row->id; ?>';$('orderForm').task.value='<?php if ( in_array($row->metadata_id,$notificationList)) echo "remove"; else echo "add"; ?>MetadataNotification'; submitOrderForm();"></div>
								</td>
								
								<?php if($hasOrderableProduct){
								//TODO : correct the link?>
								<td class="logo">
									<div title="<?php echo JText::_('SHOP_FAVORITE_ADD_TO_CART'); ?>" 
									     class="savedOrderOrder" 
									     onClick="window.open('./index.php?option=com_easysdi_shop&task=shop&Itemid=<?php echo $shopitemId?>&firstload=1&fromStep=1&cid[]=<?php echo $row->metadata_id ?>', '_main');"></div>
								</td>
								<?php }else{ ?>
								<td class="nologo">&nbsp;</td>
								<?php } ?>
								
								<?php if($hasOrderableProduct && $hasPreview){?>
								<td align="center" class="logo">
									<div class="particular-view-product-link">
										<a  title="<?php echo JText::_('SHOP_FAVORITE_PREVIEW_PRODUCT');?>" 
											id="productLink<?php echo $i; ?>" 
											rel="{handler:'iframe',size:{x:565,y:450}}" 
											href="./index.php?tmpl=component&option=com_easysdi_shop&task=previewProduct&metadata_id=<?php echo $row->metadata_id;?>" 
											class="modal">&nbsp;</a>
									</div>
								</td>
								
								<?php }else{ ?>
								<td class="nologo">&nbsp;</td>
								<?php } ?>
							</tr>
					<?php
						$i=$i+1;
					}	
					?>
					</tbody>
					</table>
					<br/>
					<table width="100%">
						<tr>
							<td align="left"><?php echo $pageNav->getPagesCounter(); ?></td>
							<td align="center">&nbsp;</td>
							<td align="right"> <?php echo $pageNav->getPagesLinks(); ?></td>
						</tr>
					</table>
				</form>
		</div>
		</div>
		<?php
	}
}
?>