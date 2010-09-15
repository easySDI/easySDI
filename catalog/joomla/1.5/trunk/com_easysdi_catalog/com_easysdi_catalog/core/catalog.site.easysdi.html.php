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

class HTML_catalog{


	function listCatalogContentWithPan ($pageNav,$cswResults,$option, $total,$searchCriteria,$maxDescr)
	{
		global  $mainframe;
		$db =& JFactory::getDBO();
		$simulatedTabIndex = JRequest::getVar('simulatedTabIndex');
		$advancedSrch = JRequest::getVar('advancedSrch',0);
		$partners = array();
		$partners[0]='';
		//$query = "SELECT  #__easysdi_community_partner.partner_id as value, partner_acronym as text FROM `#__easysdi_community_partner` INNER JOIN `#__easysdi_product` ON #__easysdi_community_partner.partner_id = #__easysdi_product.partner_id GROUP BY #__easysdi_community_partner.partner_id";
		//Do not display a furnisher without product
		
		$query = "SELECT  #__easysdi_community_partner.partner_id as value, #__users.name as text 
		          FROM #__users, `#__easysdi_community_partner` 
			  INNER JOIN `#__easysdi_product` ON #__easysdi_community_partner.partner_id = #__easysdi_product.partner_id 
			  WHERE #__users.id = #__easysdi_community_partner.user_id AND 
			     #__easysdi_community_partner.partner_id IN (Select #__easysdi_product.partner_id from #__easysdi_product where #__easysdi_product.published=1) 
			  GROUP BY #__easysdi_community_partner.partner_id 
			  ORDER BY #__users.name";
		
		/*
		$query = "SELECT  #__easysdi_community_partner.partner_id as value, #__users.name as text 
		          FROM #__users, `#__easysdi_community_partner` 
			  INNER JOIN `#__easysdi_product` ON #__easysdi_community_partner.partner_id = #__easysdi_product.partner_id 
			  WHERE #__users.id = #__easysdi_community_partner.user_id 
			  GROUP BY #__easysdi_community_partner.partner_id 
			  ORDER BY #__users.name";
	        */
		$db->setQuery( $query);
		$partners = array_merge( $partners, $db->loadObjectList() );
		if ($db->getErrorNum()) 
		{
			echo "<div class='alert'>";
			echo 	$db->getErrorMsg();
			echo "</div>";
		}
		
		?>
		<div id="page">
		<h2 class="contentheading"><?php echo JText::_("EASYSDI_CATALOG_TITLE"); ?></h2>
		<div class="contentin">
		 
		<form name="catalog_search_form" id="catalog_search_form"  method="GET">
			<input type="hidden" name="option" id="option" value="<?php echo JRequest::getVar('option' );?>" />
			<input type="hidden" name="view" id="view" value="<?php echo JRequest::getVar('view' );?>" />
			<input type="hidden" name="bboxMinX" id="bboxMinX" value="<?php echo JRequest::getVar('bboxMinX', "-180" );?>" /> 
			<input type="hidden" name="bboxMinY" id="bboxMinY" value="<?php echo JRequest::getVar('bboxMinY', "-90" );?>" /> 
			<input type="hidden" name="bboxMaxX" id="bboxMaxX" value="<?php echo JRequest::getVar('bboxMaxX', "180" ); ?>" />
			<input type="hidden" name="bboxMaxY" id="bboxMaxY" value="<?php echo JRequest::getVar('bboxMaxY', "90" );?>" />
			<input type="hidden" name="Itemid" id="Itemid" value="<?php echo JRequest::getVar('Itemid');?>" />
			<input type="hidden" name="lang" id="lang" value="<?php echo JRequest::getVar('lang');?>" />
			<input type="hidden" name="tabIndex" id="tabIndex" value="" />
			<input type="hidden" name="simulatedTabIndex" id ="simulatedTabIndex" value ="<?php echo JRequest::getVar('simulatedTabIndex');?>" />
			<input type="hidden" name="advancedSrch" id ="advancedSrch" value ="<?php echo JRequest::getVar('advancedSrch', 0);?>" />
			<input type="hidden" name="firstload" id="limitstart" value="1" />
			<input type="hidden" name="fromStep" id="fromStep" value="1" />
			
			<script  type="text/javascript">
				window.addEvent('domready', function() {
				/*
				* Register event handlers
				*/
					//initialize the page
					init();
					
					//Toggle the state of the advanced search
					$('advSearchRadio').addEvent('click', function() {
						toggleAdvancedSearch($('advSearchRadio').checked);
					});
					
					//Handler for the clear button
					$('easysdi_clear_button').addEvent('click', function() {
						easysdiClearButton_click();
					});
					
				});
				
				function init(){
					//hide advanced search
					toggleAdvancedSearch($('advancedSrch').value);
				}
				
				function easysdiClearButton_click(){
					clearBasicSearch();
					clearAdvancedSearch();
					document.getElementById('tabIndex').value = '0';
					document.getElementById('catalog_search_form').submit();
				}
				
				function toggleAdvancedSearch(isVisible){
					if(isVisible == true){
						$('divAdvancedSearch').style.visibility = 'visible';
						$('divAdvancedSearch').style.display = 'block';
						$('advSearchRadio').checked = true;
						$('advancedSrch').value=1;
					}else{
						$('divAdvancedSearch').style.visibility = 'hidden';
						$('divAdvancedSearch').style.display = 'none';
						$('advSearchRadio').checked = false;
						$('advancedSrch').value=0;
						//Do not keep data in a hidden table
						clearAdvancedSearch();
					}
				}
				
				function clearBasicSearch ()
				{
					 document.getElementById('simple_filterfreetextcriteria').value = '';
					 document.getElementById('partner_id').value = '';
				}
				
				function clearAdvancedSearch ()
				{
					 document.getElementById('filter_visible').value = '';
					 document.getElementById('filter_orderable').value = '';
					 document.getElementById('filter_theme').value = '';
					 document.getElementById('update_select').value = 'equal';
					 document.getElementById('update_cal').value = '';
					 document.getElementById("bboxMinX").value = "-180"; 	
					 document.getElementById("bboxMinY").value ="-90";
					 document.getElementById("bboxMaxX").value ="180"; 	
					 document.getElementById("bboxMaxY").value ="90";
				}
				 
			</script>
			
			<h3><?php echo JText::_("EASYSDI_CATALOG_SEARCH_CRITERIA_TITLE"); ?></h3>
			
			<!--
				This is the simple search
			-->
			<div>
			<table width="100%" class="mdCatContent">
				<tr>
					<td align="left"><b><?php echo JText::_("EASYSDI_CATALOG_FILTER_TITLE");?></b>&nbsp;
					<!-- this was the old advanced critera: filterfreetextcriteria -->
					<td align="left"><input type="text" id="simple_filterfreetextcriteria"  name="simple_filterfreetextcriteria" value="<?php echo JRequest::getVar('simple_filterfreetextcriteria');?>" class="inputbox" /></td>
					<td class="catalog_controls">
						<table>
							<tr>
								<td>
								<button id="simple_search_button" onclick="document.getElementById('tabIndex').value = '0';" name="simple_search_button" type="submit" class="easysdi_search_button">
								<?php echo JText::_("EASYSDI_CATALOG_SEARCH_BUTTON"); ?></button>
								</td>
								<td>
								<button type="submit" id="easysdi_clear_button" class="easysdi_clear_button">
								<?php echo JText::_("EASYSDI_CATALOG_CLEAR_BUTTON"); ?></button>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td><?php echo JText::_("EASYSDI_CATALOG_FILTER_PARTNER");?></td>
					<td><?php echo JHTML::_("select.genericlist", $partners, 'partner_id', 'size="1" class="inputbox" ', 'value', 'text', JRequest::getVar('partner_id')); ?></td>
					<td><input id="advSearchRadio" name="advSearchRadio" type="checkBox" value=""/>
						<span><?php echo JText::_("EASYSDI_TEXT_ADVANCED_CRITERIA"); ?></span></td>		
				</tr>
			</table>
			</div>
			
			<!--
				This is the advanced search
			-->
<?php
			global  $mainframe;
			$option= JRequest::getVar('option');
			$db =& JFactory::getDBO();
			
			$themes = array();
			$themes[] = JHTML::_('select.option', '', '');
			$query = "SELECT #__easysdi_metadata_topic_category.code as value, #__easysdi_metadata_topic_category.value as text FROM `#__easysdi_metadata_topic_category`";
			$db->setQuery( $query);
			$themes = array_merge( $themes, $db->loadObjectList() );		
			HTML_catalog::alter_array_value_with_Jtext($themes);
		
?>
			<div id="divAdvancedSearch">
				<table width="100%" class="mdCatContent">
					<tr>
						<td ><?php echo JText::_("EASYSDI_CATALOG_FILTER_THEME");?></td>
						<td><?php echo JHTML::_("select.genericlist", $themes, 'filter_theme', 'size="1" class="inputbox" ', 'value', 'text', JRequest::getVar('filter_theme')); ?></td>
					</tr>
					
					<tr>
						<td><?php echo JText::_("EASYSDI_CATALOG_FILTER_VISIBLE");?></td>
						<td><input type="checkbox" id="filter_visible" name="filter_visible" <?php if (JRequest::getVar('filter_visible')) echo " checked"; ?> class="inputbox" /></td>
					</tr>
					<tr>
						<td><?php echo JText::_("EASYSDI_CATALOG_FILTER_ORDERABLE");?></td>
						<td><input type="checkbox" id="filter_orderable" name="filter_orderable" <?php if (JRequest::getVar('filter_orderable')) echo " checked"; ?> class="inputbox" /></td>
					</tr>
					<tr>
						<td><?php echo JText::_("EASYSDI_CATALOG_UPDATE");?></td>
						<td>
							<select id="update_select" size="1" name="update_select">
								<option value="equal" <?php if(JRequest::getVar('update_select')=="equal") echo "SELECTED"; ?>><?php echo JText::_("EASYSDI_CATALOG_DATE_EQUAL");?></option>
								<option value="smallerorequal" <?php if(JRequest::getVar('update_select')=="smallerorequal") echo "SELECTED"; ?>><?php echo JText::_("EASYSDI_CATALOG_DATE_BEFORE");?></option>
								<option value="greaterorequal" <?php if(JRequest::getVar('update_select')=="greaterorequal") echo "SELECTED"; ?>><?php echo JText::_("EASYSDI_CATALOG_DATE_AFTER");?></option>
								<option value="different" <?php if(JRequest::getVar('update_select')=="different") echo "SELECTED"; ?>><?php echo JText::_("EASYSDI_CATALOG_DATE_NOTEQUAL");?></option>
							</select>
							<?php echo JHTML::_('calendar',JRequest::getVar('update_cal'), "update_cal","update_cal","%d.%m.%Y"); ?>
						</td>
					</tr>
				</table>
				
				<?php 
				//Feature deactivated for now...
				//HTML_catalog::generateMap(); 
				
				?>
			</div>
			<!--
			<table>
				<tr>
					<td>
					<button id="advanced_search_button" type="submit" class="easysdi_search_button"
						onclick="document.getElementById('tabIndex').value = '1';
								 document.getElementById('catalog_search_form').submit();">
								 <?php echo JText::_("EASYSDI_CATALOG_SEARCH_BUTTON"); ?></button>
					</td>
					<td>
					<button type="submit" class="easysdi_clear_button"
						onclick="clearDetailsForm();
								  document.getElementById('tabIndex').value = '1';
								 document.getElementById('catalog_search_form').submit();">
						<?php echo JText::_("EASYSDI_CATALOG_CLEAR_BUTTON"); ?></button>
					</td>
				</tr>
			</table>
			-->
		</form>
		
		

		 <?php if($cswResults){
			 
			 //
			 //
			 //
			 // Nothing to do out there...
			 //
			 //
			 //
			 
			 
			 
			 
			 
			 
			 
			 ?> <br/>
<table width="100%">
	<tr>
		<td align="left"><?php echo $pageNav->getPagesCounter(); ?></td>
		<td align="right"><?php echo $pageNav->getPagesLinks(); ?></td>
	</tr>
</table>
<h3><?php echo JText::_("EASYSDI_SEARCH_RESULTS_TITLE"); ?></h3>

<span class="easysdi_number_of_metadata_found"><?php echo JText::_("EASYSDI_CATALOG_NUMBER_OF_METADATA_FOUND");?>
		<?php echo $total ?> </span>
<table class="mdsearchresult">
<!--
	<thead>
		<tr>

	 		<th><?php echo JText::_('EASYSDI_CATALOG_PRODUCT_SHARP'); ?></th>
			<th><?php echo JText::_('EASYSDI_CATALOG_ORDERABLE'); ?></th>

			<th><?php echo JText::_('EASYSDI_CATALOG_ROOT_LOGO'); ?></th>
			<th><?php echo JText::_('EASYSDI_CATALOG_PRODUCT_NAME'); ?></th>
		</tr>
	</thead>
-->
	<?php
	$i=0;
	$param = array('size'=>array('x'=>800,'y'=>800) );
	JHTML::_("behavior.modal","a.modal",$param);
	$xpath = new DomXPath($cswResults);
	$xpath->registerNamespace('gmd','http://www.isotc211.org/2005/gmd');
	$nodes = $xpath->query('//gmd:MD_Metadata');
	
	foreach($nodes  as $metadata){
			
		$i++;
			
		$md = new geoMetadata($metadata);
		?>
		<tr>
			<!-- <td><?php echo $i; ?></td>  -->
			<?php
			$md_orderable=0;
			$pOrderableExt = 0;
			$pOrderableInt = 0;
			$rOrderableExt = 0;
			$rOrderableInt = 0;
			
			$query = "select external from #__easysdi_product where metadata_id = '".$md->getFileIdentifier()."'";
			$db->setQuery( $query);
			$pOrderableExt = $db->loadResult();
			
			$query = "select internal from #__easysdi_product where metadata_id = '".$md->getFileIdentifier()."'";
			$db->setQuery( $query);
			$pOrderableInt = $db->loadResult();
			
			//check user permissions
			$user = JFactory::getUser();
			$partner = new partnerByUserId($db);
			if (!$user->guest){
				$partner->load($user->id);
			}else{
				$partner->partner_id = 0;
			}
                	
			if($partner->partner_id == 0)
			{
				//No user logged, display only external products
				$rOrderableExt = 1;
				$rOrderableInt = 0;
			}
			else
			{
				//User logged, display products according to users's rights
				if(userManager::hasRight($partner->partner_id,"REQUEST_EXTERNAL"))
				{
					if(userManager::hasRight($partner->partner_id,"REQUEST_INTERNAL"))
					{
						$query  = "SELECT COUNT(*) FROM #__easysdi_product p where published=1 and orderable = 1";
						$query .= " AND p.metadata_id=".$md->getFileIdentifier();
						$query .= " AND (p.EXTERNAL=1
						OR
						(p.INTERNAL =1 AND
						(p.partner_id =  $partner->partner_id
						OR
						p.partner_id = (SELECT root_id FROM #__easysdi_community_partner WHERE partner_id = $partner->partner_id )
						OR 
						p.partner_id IN (SELECT partner_id FROM #__easysdi_community_partner WHERE root_id = (SELECT root_id FROM #__easysdi_community_partner WHERE partner_id = $partner->partner_id ))
						OR
						p.partner_id  IN (SELECT partner_id FROM #__easysdi_community_partner WHERE root_id = $partner->partner_id ) 
						
						))) ";
						$db->setQuery( $query);
		                                $tot = $db->loadResult();
						if($tot > 0){
						   $rOrderableInt = 1;
						}
							
					}
					else
					{
						$rOrderableExt = 1;
					}
				}
				else
				{
					if(userManager::hasRight($partner->partner_id,"REQUEST_INTERNAL"))
					{
						$query  = "SELECT COUNT(*) FROM #__easysdi_product p where published=1 and orderable = 1";
						$query .= " AND p.metadata_id=".$md->getFileIdentifier();
						$query .= " AND (p.INTERNAL =1 AND
						(p.partner_id =  $partner->partner_id
						OR
						p.partner_id = (SELECT root_id FROM #__easysdi_community_partner WHERE partner_id = $partner->partner_id )
						OR 
						p.partner_id IN (SELECT partner_id FROM #__easysdi_community_partner WHERE root_id = (SELECT root_id FROM #__easysdi_community_partner WHERE partner_id = $partner->partner_id ))
						OR
						p.partner_id  IN (SELECT partner_id FROM #__easysdi_community_partner WHERE root_id = $partner->partner_id ) 
						)) ";
						$db->setQuery( $query);
		                                $tot = $db->loadResult();
						if($tot > 0){
						   $rOrderableInt = 1;
						}
										
					}
					else
					{
						//no command right
						$rOrderableExt = 0;
				                $rOrderableInt = 0;
					}
				}
			}
			//echo "<br>".$pOrderableExt."-".$rOrderableExt."-".$pOrderableInt."-".$rOrderableInt;
			if(($pOrderableExt == 1 && $rOrderableExt == 1) || ($pOrderableInt == 1 && $rOrderableInt == 1))
			{
				$md_orderable=1;
				//echo "->orderable";
			}

			//echo $md->getFileIdentifier()." Ext:".$pOrderableExt." Int:".$pOrderableInt."__".$query ."<br>";
			$query = "select count(*) from #__easysdi_product where previewWmsUrl != '' AND metadata_id = '".$md->getFileIdentifier()."'";
			
			$db->setQuery( $query);

			$hasPreview = $db->loadResult();
			if ($db->getErrorNum()) {
				$hasPreview = 0;
			}

			$queryPartnerID = "select partner_id from #__easysdi_product where metadata_id = '".$md->getFileIdentifier()."'";
			$db->setQuery($queryPartnerID);
			$partner_id = $db->loadResult();
			
			$queryPartnerLogo = "select partner_logo from #__easysdi_community_partner where partner_id = ".$partner_id;
			$db->setQuery($queryPartnerLogo);
			$partner_logo = $db->loadResult();
			
			$query="select CONCAT( CONCAT( a.address_agent_firstname, ' ' ) , a.address_agent_lastname ) AS name from #__easysdi_community_partner p inner join #__easysdi_community_address a on p.partner_id = a.partner_id WHERE p.partner_id = ".$partner_id ." and a.type_id=1" ;
			$db->setQuery($query);
			$supplier= $db->loadResult();
			
			$user =& JFactory::getUser();
			$language = $user->getParam('language', '');
			
			$logoWidth = config_easysdi::getValue("logo_width");
			$logoHeight = config_easysdi::getValue("logo_height");
		
			$isMdPublic = false;
			$isMdFree = true;

			//Define if the md is free or not
			$queryPartnerID = "select is_free from #__easysdi_product where metadata_id = '".$md->getFileIdentifier()."'";
			$db->setQuery($queryPartnerID);
			$is_free = $db->loadResult();
			if($is_free == 0)
			{
				$isMdFree = false;
			}
				
			//Define if the md is public or not
			$queryPartnerID = "select metadata_external from #__easysdi_product where metadata_id = '".$md->getFileIdentifier()."'";
			$db->setQuery($queryPartnerID);
			$external = $db->loadResult();
			if($external == 1)
			{
				$isMdPublic = true;
			}
			//}
			
			?>
			 
	  <td class="imgHolder" rowspan="3">
	 <img <?php if($logoWidth != "") echo "width=\"$logoWidth px\"";?> <?php if($logoHeight != "") echo "width=\"$logoHeight px\"";?> src="<?php echo $partner_logo;?>" title="<?php echo $row->supplier_name;?>"></img>   
	  </td>
	  <td colspan="3"><span class="mdtitle"><?php echo $md->getDataIdentificationTitle();?></span>
	  </td>
	  <td valign="top" rowspan=2>
	    <table id="info_md">
		  <tr>
		     <td><div <?php if($isMdPublic) echo 'class="publicMd"'; else echo 'title="'.JText::_("EASYSDI_CATALOG_INFOLOGO_PRIVATEMD").'" class="privateMd"';?>></div></td>
		  </tr>
		  <tr>
		     <td><div <?php if($md_orderable == 1) echo 'title="'.JText::_("EASYSDI_CATALOG_INFOLOGO_ORDERABLE").'" class="easysdi_product_exists"'; else echo 'title="'.JText::_("EASYSDI_CATALOG_INFOLOGO_NOTORDERABLE").'" class="easysdi_product_does_not_exist"';?>></div></td>
		  </tr>
		  <tr>
		     <td><div <?php if($isMdFree) echo 'title="'.JText::_("EASYSDI_CATALOG_INFOLOGO_FREEMD").'" class="freeMd"'; else echo 'class="notFreeMd"';?>></div></td>
		  </tr>
		</table>
	  </td>
	 </tr>
	 <tr>
	  <td colspan="3"><span class="mddescr"><?php echo mb_substr($md->getDescription($language), 0, $maxDescr, 'UTF-8'); if(strlen($md->getDescription($language))>$maxDescr)echo" [...]";?></span></td>
	 </tr>
	 <tr> 
	 <!--
	 <a	class="<?php if ($md_orderable>0) {echo "easysdi_orderable";} else {echo "easysdi_not_orderable";} ?>" 
		    href="./index.php?option=com_easysdi_shop&view=shop" target="_self"><?php echo JText::_("EASYSDI_VIEW_MD_FILE"); ?>
		 </a>
	 --> 
	  <td class="mdActionViewFile"><span class="mdviewfile">
	  	<a class="modal"
				title="<?php echo JText::_("EASYSDI_VIEW_MD_FILE"); ?>"
				href="./index.php?tmpl=component&option=com_easysdi_core&task=showMetadata&id=<?php echo $md->getFileIdentifier();  ?>"
				rel="{handler:'iframe',size:{x:650,y:600}}"><?php echo JText::_("EASYSDI_VIEW_MD_FILE"); ?>
			</a></span>
	  </td>
	  <td class="mdActionViewProduct">
	  <?php if ($hasPreview > 0){ ?>
	  <span class="mdviewproduct">
	    <a class="modal" href="./index.php?tmpl=component&option=com_easysdi_catalog&task=previewProduct&metadata_id=<?php echo $md->getFileIdentifier();?>"
			rel="{handler:'iframe',size:{x:558,y:415}}"><?php echo JText::_("EASYSDI_PREVIEW_PRODUCT"); ?></a></span>
      	  <?php } ?>
	  </td>
	  <td class="mdNoAction"></td>
	  </tr>
	 <tr>
	   <td colspan="5" halign="middle"><div class="separator" /></td>
	 </tr>
	 <?php
	}
	?>
	</table>
	
	<!-- pageNav at footer -->
	<table width="100%">
	   <tr>
		<td colspan="3">&nbsp;</td>
	   </tr>
	   <tr>
		<td align="left"><?php echo $pageNav->getPagesCounter(); ?></td>
		<td align="center">&nbsp;</td>
		<td align="right"><?php echo $pageNav->getPagesLinks(); ?></td>
	   </tr>
	</table>
	
	<?php } ?></div>
		

	</div>


	<?php
			
		
	}
	/*
	function listCatalogContent($pageNav,$cswResults,$option, $total,$searchCriteria,$maxDescr){
		global  $mainframe;
		$db =& JFactory::getDBO();
		?>
		<div id="page">
		<h2 class="contentheading"><?php echo JText::_("EASYSDI_CATALOG_TITLE"); ?></h2>
		<div class="contentin">
		
		

		<form name="catalog_search_form" id="catalog_search_form"  method="GET">
			<input type="hidden" name="option" id="option" value="<?php echo JRequest::getVar('option' );?>" />
			<input type="hidden" name="view" id="view" value="<?php echo JRequest::getVar('view' );?>" />
			<input type="hidden" name="bboxMinX" id="bboxMinX" value="<?php echo JRequest::getVar('bboxMinX', "-180" );?>" /> 
			<input type="hidden" name="bboxMinY" id="bboxMinY" value="<?php echo JRequest::getVar('bboxMinY', "-90" );?>" /> 
			<input type="hidden" name="bboxMaxX" id="bboxMaxX" value="<?php echo JRequest::getVar('bboxMaxX', "180" ); ?>" />
			<input type="hidden" name="bboxMaxY" id="bboxMaxY" value="<?php echo JRequest::getVar('bboxMaxY', "90" );?>" />
			<input type="hidden" name="Itemid" id="Itemid" value="<?php echo JRequest::getVar('Itemid');?>" />
			<input type="hidden" name="lang" id="lang" value="<?php echo JRequest::getVar('lang');?>" />
			<input type="hidden" name="tabIndex" id="tabIndex" value="" />
			<h3><?php echo JText::_("EASYSDI_CATALOG_SEARCH_CRITERIA_TITLE"); ?></h3>
		
				<?php
				$index = JRequest::getVar('tabIndex', 0);
				$tabs =& JPANE::getInstance('Tabs', array('startOffset'=>$index));
			//	echo $tabs->startPane("catalogPane");
			//	echo $tabs->startPanel(JText::_("EASYSDI_TEXT_SIMPLE_CRITERIA"),"catalogPanel1");
				?> <br/>

			<table width="100%">
				<tr>
					<td>
						<table width="100%">
							<tr>
								<td align="left"><b><?php echo JText::_("EASYSDI_CATALOG_FILTER_TITLE");?></b>&nbsp;
								<input type="text" id="simple_filterfreetextcriteria"  name="simple_filterfreetextcriteria" value="<?php echo JRequest::getVar('simple_filterfreetextcriteria');?>" class="inputbox" /></td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
			<table>
				<tr>
					<td>
					<button type="submit" class="easysdi_search_button"
						onclick="clearDetailsForm();
								 document.getElementById('tabIndex').value = '0';
								 document.getElementById('catalog_search_form').submit();">
								 <?php echo JText::_("EASYSDI_CATALOG_SEARCH_BUTTON"); ?></button>
					</td>
					<td>
					<button type="submit" class="easysdi_clear_button"
						onclick="clearForm();
								 document.getElementById('tabIndex').value = '0';
								document.getElementById('catalog_search_form').submit();">
								<?php echo JText::_("EASYSDI_CATALOG_CLEAR_BUTTON"); ?></button>
					</td>
				</tr>
			</table>
				<?php
		//		echo $tabs->endPanel();
		//		echo $tabs->startPanel(JText::_("EASYSDI_TEXT_ADVANCED_CRITERIA"),"catalogPanel2");
				?><br/>
			<table width="100%" >
				<tr>
					<td><?php
					HTML_catalog::generateMap();
					?></td>
					
				</tr>
			</table>
			<table>
				<tr>
					<td>
					<button type="submit" class="easysdi_search_button"
						onclick="clearForm();
								 document.getElementById('tabIndex').value = '1';
								 document.getElementById('catalog_search_form').submit();">
								 <?php echo JText::_("EASYSDI_CATALOG_SEARCH_BUTTON"); ?></button>
					</td>
					<td>
					<button type="submit" class="easysdi_clear_button"
						onclick="clearDetailsForm();
								  document.getElementById('tabIndex').value = '1';
								 document.getElementById('catalog_search_form').submit();">
						<?php echo JText::_("EASYSDI_CATALOG_CLEAR_BUTTON"); ?></button>
					</td>
				</tr>
			</table>
			<script  type="text/javascript">
				function clearDetailsForm ()
				{
					document.getElementById('filterfreetextcriteria').value = '';
					 document.getElementById('filter_visible').value = '';
					 document.getElementById('partner_id').value = '';
					 document.getElementById('filter_orderable').value = '';
					 document.getElementById('filter_theme').value = '';	
					 document.getElementById("bboxMinX").value = "-180"; 	
					 document.getElementById("bboxMinY").value ="-90";
					 document.getElementById("bboxMaxX").value ="180"; 	
					 document.getElementById("bboxMaxY").value ="90";
				}
				function clearForm()
				{
					document.getElementById('simple_filterfreetextcriteria').value = '';
				}
			</script>
			<?php
	//		echo $tabs->endPanel();
	//		echo $tabs->endPane();
			?>
		</form>
		
		
		 <?php if($cswResults){ ?> <br/>
<table width="100%">
	<tr>
		<td align="left"><?php echo $pageNav->getPagesCounter(); ?></td>
		<td align="right"><?php echo $pageNav->getPagesLinks(); ?></td>
	</tr>
</table>
<h3><?php echo JText::_("EASYSDI_SEARCH_RESULTS_TITLE"); ?></h3>

<span class="easysdi_number_of_metadata_found"><?php echo JText::_("EASYSDI_CATALOG_NUMBER_OF_METADATA_FOUND");?>
		<?php echo $total ?> </span>
<table class="mdsearchresult">
<!--
	<thead>
		<tr>

	 		<th><?php echo JText::_('EASYSDI_CATALOG_PRODUCT_SHARP'); ?></th>
			<th><?php echo JText::_('EASYSDI_CATALOG_ORDERABLE'); ?></th>

			<th><?php echo JText::_('EASYSDI_CATALOG_ROOT_LOGO'); ?></th>
			<th><?php echo JText::_('EASYSDI_CATALOG_PRODUCT_NAME'); ?></th>
		</tr>
	</thead>
-->
	<?php
	$i=0;
	$param = array('size'=>array('x'=>800,'y'=>800) );
	JHTML::_("behavior.modal","a.modal",$param);



	$xpath = new DomXPath($cswResults);
	$xpath->registerNamespace('gmd','http://www.isotc211.org/2005/gmd');
	$nodes = $xpath->query('//gmd:MD_Metadata');

	foreach($nodes  as $metadata){
			
		$i++;
			
		$md = new geoMetadata($metadata);
		?>
		<tr>
			<!-- <td><?php echo $i; ?></td>  -->
			<?php
			$query = "select count(*) from #__easysdi_product where metadata_id = '".$md->getFileIdentifier()."'";
			$db->setQuery( $query);

			$md_orderable = $db->loadResult();

			if ($db->getErrorNum()) {
				$md_orderable = '0';
			}


			$query = "select count(*) from #__easysdi_product where previewBaseMapId is not null AND previewBaseMapId>0 AND metadata_id = '".$md->getFileIdentifier()."'";

			$db->setQuery( $query);

			$hasPreview = $db->loadResult();
			if ($db->getErrorNum()) {
				$hasPreview = 0;

			}

			$queryPartnerID = "select partner_id from #__easysdi_product where metadata_id = '".$md->getFileIdentifier()."'";
			$db->setQuery($queryPartnerID);
			$partner_id = $db->loadResult();
			
			$queryPartnerLogo = "select partner_logo from #__easysdi_community_partner where partner_id = ".$partner_id;
			$db->setQuery($queryPartnerLogo);
			$partner_logo = $db->loadResult();
			
			$query="select CONCAT( CONCAT( a.address_agent_firstname, ' ' ) , a.address_agent_lastname ) AS name from #__easysdi_community_partner p inner join #__easysdi_community_address a on p.partner_id = a.partner_id WHERE p.partner_id = ".$partner_id ." and a.type_id=1" ;
			$db->setQuery($query);
			$supplier= $db->loadResult();
			
			$user =& JFactory::getUser();
			$language = $user->getParam('language', '');
			
			$logoWidth = config_easysdi::getValue("logo_width");
			$logoHeight = config_easysdi::getValue("logo_height");
		
			$isMdPublic = false;
			$isMdFree = true;
			if( $md_orderable != 0)
			{
				//Define if the md is free or not
				$queryPartnerID = "select is_free from #__easysdi_product where metadata_id = '".$md->getFileIdentifier()."'";
				$db->setQuery($queryPartnerID);
				$is_free = $db->loadResult();
				if($is_free == 0)
				{
					$isMdFree = false;
				}
				
				//Define if the md is public or not
				$queryPartnerID = "select external from #__easysdi_product where metadata_id = '".$md->getFileIdentifier()."'";
				$db->setQuery($queryPartnerID);
				$external = $db->loadResult();
				if($external == 1)
				{
					$isMdPublic = true;
				}
			}
			
			
			?>
			 
	  <td valign="top" rowspan=3>
	    <img width="<?php echo $logoWidth ?>px" height="<?php echo $logoHeight ?>px" src="<?php echo $partner_logo;?>" alt="<?php echo JText::_('EASYSDI_CATALOG_ROOT_LOGO');?>"></img>
	  </td>
	  <td colspan="3"><span class="mdtitle"><a><?php echo $md->getDataIdentificationTitle();?></a></span>
	  </td>
	  <td valign="top" rowspan=2>
	    <table id="info_md">
		  <tr>
		     <td><div <?php if($isMdPublic) echo 'class="publicMd"'; else echo 'title="'.JText::_("EASYSDI_CATALOG_INFOLOGO_PRIVATEMD").'" class="privateMd"';?>></div></td>
		  </tr>
		  <tr>
		     <td><div <?php if($md_orderable>0) echo 'title="'.JText::_("EASYSDI_CATALOG_INFOLOGO_ORDERABLE").'" class="easysdi_product_exists"'; else echo 'title="'.JText::_("EASYSDI_CATALOG_INFOLOGO_NOTORDERABLE").'" class="easysdi_product_does_not_exist"';?>></div></td>
		  </tr>
		  <tr>
		     <td><div <?php if($isMdFree) echo 'title="'.JText::_("EASYSDI_CATALOG_INFOLOGO_FREEMD").'" class="freeMd"'; else echo 'class="notFreeMd"';?>></div></td>
		  </tr>
		</table>
	  </td>
	 </tr>
	 <tr>
	  <td colspan="3"><span class="mddescr"><?php echo substr($md->getDescription($language), 0, $maxDescr); if(strlen($md->getDescription($language))>$maxDescr)echo" [...]";?></span></td>
	 </tr>
	 <tr> 
	 <!--
	 <a	class="<?php if ($md_orderable>0) {echo "easysdi_orderable";} else {echo "easysdi_not_orderable";} ?>" 
		    href="./index.php?option=com_easysdi_shop&view=shop" target="_self"><?php echo JText::_("EASYSDI_VIEW_MD_FILE"); ?>
		 </a>
	 --> 
	  <td><span class="mdviewfile">
	  	<a class="modal"
				title="<?php echo JText::_("EASYSDI_VIEW_MD_FILE"); ?>"
				href="./index.php?tmpl=component&option=com_easysdi_core&task=showMetadata&id=<?php echo $md->getFileIdentifier();  ?>"
				rel="{handler:'iframe',size:{x:650,y:550}}"><?php echo JText::_("EASYSDI_VIEW_MD_FILE"); ?>
			</a></span>
	  </td>
	  	<?php if ($hasPreview > 0){ ?>
	  <td><span class="mdviewproduct">
	    <a class="modal" href="./index.php?tmpl=component&option=com_easysdi_catalog&task=previewProduct&metadata_id=<?php echo $md->getFileIdentifier();?>"
			rel="{handler:'iframe',size:{x:650,y:550}}"><?php echo JText::_("EASYSDI_PREVIEW_PRODUCT"); ?></a></span>
      </td>
		<?php } ?>
	  <td>&nbsp;</td>
	 </tr>
	 <tr>
	   <td colspan="4">&nbsp;</td>
	 </tr>
	 

	 <?php
	}
	?>
	</table>
	
	<?php } ?></div>
		

	</div>
	
	<?php

	}
	*/
	function generateMap()
	{
		
		?>
		<script type="text/javascript" src="administrator/components/com_easysdi_core/common/lib/js/openlayers2.7/OpenLayers.js"></script>
		<script type="text/javascript" src="administrator/components/com_easysdi_core/common/lib/js/proj4js/lib/proj4js.js"></script>
		<script type="text/javascript" src="administrator/components/com_easysdi_core/common/lib/js/proj4js/lib/defs/EPSG21781.js"></script>
	
		<script type="text/javascript" >
		
		var vectorsCatalog;            
		var mapCatalog;
		var baseLayerVectorCatalog;
		
		function setAlpha(imageformat)
		{
			var filter = false;
			if (imageformat.toLowerCase().indexOf("png") > -1) {
				filter = OpenLayers.Util.alphaHack(); 
			}
			return filter;
		}
		
		function initMapCatalog(){
			
		 <?php
		global  $mainframe;
		$db =& JFactory::getDBO();
		$query = "select * from #__easysdi_basemap_definition where def = 1"; 
		$db->setQuery( $query);
		$rows = $db->loadObjectList();		  
		if ($db->getErrorNum()) {						
					echo "<div class='alert'>";			
					echo 			$db->getErrorMsg();
					echo "</div>";
		}
		?>
					var options = {
			    	projection: "<?php echo $rows[0]->projection; ?>",
		            displayProjection: new OpenLayers.Projection("<?php echo $rows[0]->projection; ?>"),
		            units: "<?php echo $rows[0]->unit; ?>",
					<?php if ($rows[0]->projection == "EPSG:4326") {}else{ ?>
		            minScale: <?php echo $rows[0]->minResolution; ?>,
		            maxScale: <?php echo $rows[0]->maxResolution; ?>,                
					<?php } ?>
		            maxExtent: new OpenLayers.Bounds(<?php echo $rows[0]->maxExtent; ?>)
					};
			mapCatalog = new OpenLayers.Map("mapCatalog",options);
			
			baseLayerVectorCatalog = new OpenLayers.Layer.Vector("BackGround Catalog",{isBaseLayer: true,transparent: "true"}); 
			mapCatalog.addLayer(baseLayerVectorCatalog);
		
		<?php
		
		$query = "select * from #__easysdi_basemap_content where basemap_def_id = ".$rows[0]->id." order by ordering"; 
		$db->setQuery( $query);
		$rows = $db->loadObjectList();		  
		if ($db->getErrorNum()) {						
					echo "<div class='alert'>";			
					echo 			$db->getErrorMsg();
					echo "</div>";
		}
		$i=0;
		foreach ($rows as $row){				  
		?>				
						  
						layer<?php echo $i; ?> = new OpenLayers.Layer.<?php echo $row->url_type; ?>( "<?php echo $row->name; ?>",
		                    <?php 
							if ($row->user != null && strlen($row->user)>0){
								//if a user and password is requested then use the joomla proxy.
								$proxyhost = config_easysdi::getValue("PROXYHOST");
								$proxyhost = $proxyhost."&type=wms&basemapscontentid=$row->id&url=";
								echo "\"$proxyhost".urlencode  (trim($row->url))."\",";												
							}else{	
								//if no user and password then don't use any proxy.					
								echo "\"$row->url\",";	
							}					
							?>
		                    
		                    {layers: '<?php echo $row->layers; ?>', format : "<?php echo $row->img_format; ?>",transparent: "true"},                                          
		                     {singleTile: <?php echo $row->singletile; ?>},                                                    
		                     {     
		                      maxExtent: new OpenLayers.Bounds(<?php echo $row->maxExtent; ?>),
		                      	<?php if ($rowsBaseMap->projection == "EPSG:4326") {}else{ ?>
		                      	minScale: <?php echo $row->minResolution; ?>,
		                        maxScale: <?php echo $row->maxResolution; ?>,
		                        <?php } ?>                     
		                     projection:"<?php echo $row->projection; ?>",
		                      units: "<?php echo $row->unit; ?>",
		                      transparent: "true"
		                     }
		                    );
		                  <?php
			                    if (strtoupper($row->url_type) =="WMS")
			                    {
			                    	?>
			                    	layer<?php echo $i; ?>.alpha = setAlpha('image/png');
			                    	<?php
			                    } 
			                    ?>
		                 mapCatalog.addLayer(layer<?php echo $i; ?>);
		<?php 
		$i++;
		} ?>                    
		
					 
				mapCatalog.events.register("zoomend", null, 
							function() { 
								document.getElementById('previousExtent').value = mapCatalog.getExtent().toBBOX();
							})
		                
		               mapCatalog.addControl(new OpenLayers.Control.LayerSwitcher());
		                mapCatalog.addControl(new OpenLayers.Control.Attribution());                                
		            vectorsCatalog = new OpenLayers.Layer.Vector(
		                "Vector Layer",
		                {isBaseLayer: false,transparent: "true"                                
		                }
		            );
		            
		           
		                       
		                        
		            mapCatalog.addLayer(vectorsCatalog);
		           <?php
					if ( JRequest::getVar('previousExtent') != "")
					{
						?>
							mapCatalog.zoomToExtent(new OpenLayers.Bounds(<?php echo JRequest::getVar('previousExtent'); ?>) );
						<?php
					}
					else
					{
						?>
							mapCatalog.zoomToMaxExtent();
						<?php	
					}
				?>
		            
		            var containerPanel = document.getElementById("panelDiv");
		            var panel = new OpenLayers.Control.Panel({div: containerPanel});
		            
				  var panelEdition = new OpenLayers.Control.Panel({div: containerPanel});
		          rectControl = new OpenLayers.Control.DrawFeature(vectorsCatalog, OpenLayers.Handler.RegularPolygon,{'displayClass':'olControlDrawFeatureRectangle'});
				  rectControl.featureAdded = function(event) { removeSelection();setLonLat(event);};												
				  rectControl.handler.setOptions({irregular: true});                                  
		          panelEdition.addControls([rectControl] );
		          mapCatalog.addControl(panelEdition);      
		          showSelection();   	
		}
		
		function showSelection(){
		
			if (document.getElementById("bboxMinX").value == "-180" && 	
		  		document.getElementById("bboxMinY").value == "-90" &&
		  		document.getElementById("bboxMaxX").value == "180" &&  	
		  		document.getElementById("bboxMaxY").value == "90" )
		  		{
		  		//show nothing
		  		return;
		  		} 
		  		else
		  		{
		  			//alert ('in show selection');
		  			bounds = new OpenLayers.Bounds(document.getElementById("bboxMinX").value,document.getElementById("bboxMinY").value,document.getElementById("bboxMaxX").value,document.getElementById("bboxMaxY").value).toGeometry();
		  			bounds.transform(new OpenLayers.Projection("EPSG:4326"),new OpenLayers.Projection("<?php echo $rows[0]->projection; ?>"));
		  			vectorsCatalog.addFeatures([new OpenLayers.Feature.Vector(bounds )]);
		  			
		  		}
		
		}
		
		function removeSelection()
		{
			if (vectorsCatalog.features.length > 1)
			{
				vectorsCatalog.removeFeatures(vectorsCatalog.features[0]);
			}
		}
		
		function setLonLat(feature)
		{
			var bounds = feature.geometry.getBounds();
			var transformedBounds =  	bounds.transform(new OpenLayers.Projection("<?php echo $rows[0]->projection; ?>"),new OpenLayers.Projection("EPSG:4326"));
		  	  	   	  	 
		  document.getElementById("bboxMinX").value =transformedBounds.left; 	
		  document.getElementById("bboxMinY").value =transformedBounds.bottom;
		  document.getElementById("bboxMaxX").value =transformedBounds.right; 	
		  document.getElementById("bboxMaxY").value =transformedBounds.top;
		  
		  
		}
		</script>
		
		<!-- Geographic filter deactivated for now...
		<table >
			<tr>
				<td >
				<fieldset>
				<legend><?php echo JText::_("EASYSDI_PUBLISH_CARTO_FILTER"); ?></legend>
					<table>
						<tr>
							<td>
						
							</td>
							<td>
								<div id="mapCatalog"  class="tinymap"></div>
						
							</td>
							</tr>
							<tr>
							<td>
						
							</td>
							<td>
								<div id="panelDiv" class="olControlEditingToolbar"></div>
							</td>
						</tr>
					</table>
				</fieldset>
				</td>
			</tr>
		</table>
		 -->
		 
		<input type='hidden' id='previousExtent' name='previousExtent' value="<?php echo JRequest::getVar('previousExtent'); ?>" />
		<script>
		
		/* Geographic filter deactivated for now...
			window.onload=function()
				{	
					initMapCatalog();
				}
		*/
			</script>
		<br/>
		<div id="docs"></div>
		
		<br/>
		<?php

	}
	
	function alter_array_value_with_Jtext(&$rows)
	{		
		if (count($rows)>0)
		{
			foreach($rows as $key => $row)
			{		  	
      			$rows[$key]->text = JText::_($rows[$key]->text);
  			}			    
		}
	}

	function getPages($pageNav)
	{
		echo 'getPages';
		//$list = array();
		$pages = $pageNav->getPagesLinks();
		$links = $pages['pages'];
		foreach ($links as $page )
		{
			echo $page;	
		}
		//return $list;	
	}
}


?>