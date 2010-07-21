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

class HTML_cpanel_account {
	
	function showSummaryForAccount($summaryForId, $print, $toolbar){
		$database = & JFactory::getDBO();
		
		$request="SELECT * FROM #__sdi_account p 
							LEFT JOIN #__sdi_address a ON p.id=a.account_id 
							LEFT JOIN #__users u ON p.user_id=u.id 
							WHERE a.type_id=1 
							AND u.id=".$summaryForId;
		
		$database->setQuery($request);
		if (!$database->query()) {
			echo "<div class='alert'>";
			echo $database->getErrorMsg();
			echo "</div>";
		}
		$rows =  $database->loadObjectList();
		
		$request="SELECT name FROM #__sdi_list_country p 
							WHERE id= ".$rows[0]->country_id;
		
		$database->setQuery($request);
		if (!$database->query()) {
			echo "<div class='alert'>";
			echo $database->getErrorMsg();
			echo "</div>";
		}
		$country = $database->loadResult();
		
		$isRoot = $rows[0]->root_id == null ? true : false;
		
		//Select the root partner name if not root
		$rowsRoot = null;
		if(!$isRoot){
			$request="SELECT p.name FROM #__sdi_account p 
							LEFT JOIN #__sdi_address a ON p.id=a.account_id 
							LEFT JOIN #__users u ON p.user_id=u.id 
							WHERE a.type_id=1 
							AND p.id=".$rows[0]->root_id;
		
			$database->setQuery($request);
			if (!$database->query()) {
				echo "<div class='alert'>";
				echo $database->getErrorMsg();
				echo "</div>";
			}
			$rowsRoot =  $database->loadObjectList();
		}
		
		?>
		<?php
		if ($toolbar==1){
		?>
		<script type="text/javascript" src="./media/system/js/mootools.js"></script>
		<script>
		window.addEvent('domready', function() {
		$('printPartnerRecap').addEvent( 'click' , function() { 
			window.open('./index.php?tmpl=component&option=com_easysdi_shop&task=showSummaryForPartner&SummaryForId=<?php echo $summaryForId?>&toolbar=0&print=1','win2','status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no');
			});
		});
		</script>
		<div title ="<?php echo JText::_("SHOP_ORDER_PRINT"); ?>" id="printPartnerRecap"></div>
		
		<?php
		}
		if ($print ==1 ){
			echo "<script>window.print();</script>";
		}
		?>
		<div id="page" class="partnerList">
		<h2 class="contentheading"><?php echo JText::_("SHOP_ORDER_ACCOUNT_INFO") ?></h2>
		<div class="contentin">
		<h3><?php if($rowsRoot != null) echo $rowsRoot[0]->name." (".$rows[0]->corporatename1 ." ". $rows[0]->corporatename2 .")"; else echo $rows[0]->corporatename1 ." ". $rows[0]->corporatename2;?></h3>
	<table width="100%" border="0">
	  <tr>
	     <td>&nbsp;</td>
	     <td width="60%" align="center">
	     <table class="partnerSummaryContent">
		 <tr>
		   <td class="ptitle" width="100" valign="top"><?php echo JText::_("SHOP_ORDER_ACCOUNT_ID") ?></td>
		   <td><?php echo $rows[0]->id ?></td>
		 </tr>
		 <tr>    
		   <td class="ptitle" valign="top"><?php echo JText::_("SHOP_ORDER_ACCOUNT_ENTRYDATE") ?></td>
		   <td><?php echo $rows[0]->created ?></td>
		 </tr>
		 <tr>    
		   <td class="ptitle" valign="top"><?php echo JText::_("CORE_ACCOUNT_TAB_CONTACT_LABEL") ?></td>
		   <td><?php echo $rows[0]->agentfirstname ?> <?php echo $rows[0]->agentlastname ?></td>
		 </tr>
		 <!-- address -->
		 <tr>  
		   <td class="ptitle">&nbsp;</td>
		   <td><?php echo $rows[0]->street1 ?> <?php echo $rows[0]->street2 ?></td>
		 </tr>
		 <tr>    
		   <td class="ptitle" valign="top"><?php echo JText::_("CORE_ACCOUNT_LOCALITY_LABEL") ?></td>
		   <td><?php echo $rows[0]->postalcode ?> <?php echo $rows[0]->locality ?></td>
		 </tr>
		 <tr>    
		   <td class="ptitle" valign="top"><?php echo JText::_("CORE_ACCOUNT_COUNTRY_LABEL") ?></td>
		   <td><?php echo $country ?></td>
		 </tr>
		 <tr>    
		   <td class="ptitle" valign="top"><?php echo JText::_("CORE_ACCOUNT_PHONE_LABEL") ?></td>
		   <td><?php echo $rows[0]->phone ?></td>
		 </tr>
		 <tr>    
		   <td class="ptitle" valign="top"><?php echo JText::_("CORE_ACCOUNT_FAX_LABEL") ?></td>
		   <td><?php echo $rows[0]->fax ?></td>
		 </tr>
		 <tr>    
		   <td class="ptitle" valign="top"><?php echo JText::_("CORE_ACCOUNT_EMAIL_LABEL") ?></td>
		   <td><a href="mailto:<?php echo $rows[0]->email ?>"><?php echo $rows[0]->email ?></a></td>
		 </tr>
		 <tr>    
		   <td class="ptitle" valign="top"><?php echo JText::_("CORE_ACCOUNT_WEBSITE_LABEL") ?></td>
		   <td><a href="<?php echo $rows[0]->url ?>" target="_blank"><?php echo $rows[0]->url ?></a></td>
		 </tr>
	       </table>
	      </td>
	      <td>&nbsp;</td>
	      </tr>
	     </table>
	   </div>
	   </div>
	   <?php
	}
}
?>