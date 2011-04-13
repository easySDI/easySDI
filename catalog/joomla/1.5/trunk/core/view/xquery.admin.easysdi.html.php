<?php
/**
 * EasySDI, a solution to implement easily any spatial data infrastructure
 * Copyright (C) 2008 DEPTH SA, Chemin d’Arche 40b, CH-1870 Monthey, easysdi@depth.ch 
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

?>


<?php 
class  HTML_xquery{
	
	function list_XQueryReports($rows, $pagination){

?>
<form action="" method="post" name="adminForm">

<table class="adminlist">
		<thead>
			<tr>
				<th class='title' style="width:10px"><?php echo JText::_("CORE_SHARP"); ?></th>
				<th class='title' style="width:10px"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>				
				<th class='title' style="width:30px"><?php echo JText::_("CATALOG_XQUERY_REPORTNAME");  ?></th>
				<th class='title' style="width:100px"><?php echo JText::_("CATALOG_XQUERY_SQLFILTER");  ?></th>
				<th class='title' style="width:100px"><?php echo JText::_("CATALOG_XQUERY_OGCFILTER");  ?></th>
				<th class='title' style="width:100px"><?php echo JText::_("CATALOG_XQUERY_XSLTURL");  ?></th>
				
			</tr>
		</thead>
		<tbody>
			<?php 
			
				$limitstart	=  JRequest::getVar('limitstart', 0);
				$num = $limitstart+1;
				foreach ($rows as $row) 
				
				{?> 
				
				<tr>
				<td  style="width:10px"><?php echo $num?></td>
				<td  style="width:10px"><input type="checkbox" name="toggle" value="" onclick="setReportIdToPreview(this, <?php echo $row->id?>)" /></td>				
				<td  style="width:30px"><?php echo  $row->xqueryname  ?></td>
				<td  style="width:100px"><?php echo $row->sqlfilter ?></td>
				<td  style="width:100px"><?php echo $row->ogcfilter  ?></td>
				<td  style="width:100px"><?php echo $row->xslttemplateurl ?></td>
				
				</tr>
				
			<?php 
			$num = $num+1;
				}
			?>


		</tbody>
		<tfoot>
		<!-- add pagination for in footer -->
			<tr>	
				<td colspan="13"><?php echo $pagination->getListFooter(); ?></td>
			</tr>
		</tfoot>
		</table>
</form>


<?php }// end function list_XQueryReports()

function newXQueryReport(){
?>


	<form action="" method="post" name="adminForm">
		<!-- Name of the report -->
			<fieldset>
							<legend align="top"><?php echo JText::_("CATALOG_XQUERY_NAME"); ?></legend>
							<table><tbody><tr>
								
									<td class="td_left"> <?php echo JText::_("CATALOG_XQUERY_ENTERNAME"); ?>	</td>
									<td class="td_middle"></td>
									<td class="td_right"> <input name="xQueryReportName" value="" class="input_width300"/>    </td>
								</tr></tbody>
							</table>
			</fieldset>
			
			<fieldset>
							<legend align="top"><?php echo JText::_("CATALOG_XQUERY_SQLFILTERLABEL"); ?></legend>
							<table><tbody><tr>
									<td class="td_left"> <?php echo JText::_("CATALOG_XQUERY_ENTERSQL"); ?>	</td>
									<td class="td_middle"></td>
									<td class="td_right"> <textarea name ="metadataIdSql"  class="text_area  textarea_size">
							 			</textarea>   
							 		 </td>
							 		</tr></tbody>
							</table>
			</fieldset>
		
			<fieldset>
							<legend align="top"><?php echo JText::_("CATALOG_XQUERY_SQLFILTERLABEL"); ?></legend>
							<table><tbody><tr>
									<td class="td_left"> <?php echo JText::_("CATALOG_XQUERY_ENTEROGC"); ?>	</td>
									<td class="td_middle"></td>
									<td class="td_right"> <textarea name ="ogcfilter"  class="text_area  textarea_size" >
		 								</textarea>  
							 		 </td>
							 		</tr></tbody>							 	
							</table>
			</fieldset>
		

			<fieldset>
							<legend align="top"><?php echo JText::_("CATALOG_XQUERY_XSLTURL"); ?></legend>
							<table><tbody><tr>
								
									<td class="td_left"> <?php echo JText::_("CATALOG_XQUERY_ENTERXSLTURL"); ?>	</td>
									<td class="td_middle"></td>
									<td class="td_right"> <input name="xsltUrl" value="" class="input_width300"/>    </td>
								</tr></tbody>
							</table>
			</fieldset>
	
	</form>
	
	
	

<?php 
}
function editXQueryReport($rows){
	$row =$rows[0];

	?>
	<script>reportToUpdateId = <?php echo $row->id?></script>
	<form action="" method="post" name="adminForm">
	
	<input name="selectedUsersIdArr" type="hidden" value=""></input>
	<!-- Name of the report -->
			<fieldset>
							<legend align="top"><?php echo JText::_("CATALOG_XQUERY_NAME"); ?></legend>
							<table><tbody><tr>
								
									<td class="td_left"> <?php echo JText::_("CATALOG_XQUERY_ENTERNAME"); ?>	</td>
									<td class="td_middle"></td>
									<td class="td_right"> <input name="xQueryReportName"  class="input_width300" value="<?php echo $row->xqueryname  ?>"/>    </td>
								</tr></tbody>
							</table>
			</fieldset>
			
			<fieldset>
							<legend align="top"><?php echo JText::_("CATALOG_XQUERY_SQLFILTERLABEL"); ?></legend>
							<table><tbody><tr>
									<td class="td_left"> <?php echo JText::_("CATALOG_XQUERY_ENTERSQL"); ?>	</td>
									<td class="td_middle"></td>
									<td class="td_right"> <textarea name ="metadataIdSql" class="text_area  textarea_size"><?php echo $row->sqlfilter  ?>
							 			</textarea>   
							 		 </td>
							 		</tr></tbody>
							</table>
			</fieldset>
		
			<fieldset>
							<legend align="top"><?php echo JText::_("CATALOG_XQUERY_SQLFILTERLABEL"); ?></legend>
							<table><tbody><tr>
									<td class="td_left"> <?php echo JText::_("CATALOG_XQUERY_ENTEROGC"); ?>	</td>
									<td class="td_middle"></td>
									<td class="td_right"> <textarea name ="ogcfilter" class="text_area  textarea_size" ><?php echo $row->ogcfilter    ?> 
		 								</textarea>  
							 		 </td>
							 		</tr></tbody>							 	
							</table>
			</fieldset>
		

	
			<fieldset>
							<legend align="top"><?php echo JText::_("CATALOG_XQUERY_XSLTURL"); ?></legend>
							<table><tbody><tr>								
									<td class="td_left"> <?php echo JText::_("CATALOG_XQUERY_ENTERXSLTURL"); ?>	</td>
									<td class="td_middle"></td>
									<td class="td_right"> <input name="xsltUrl" value="<?php echo $row->xslttemplateurl   ?>" class="input_width300"/>    </td>
								</tr></tbody>
							</table>
			</fieldset>
	
	
	
	</form>

<?php 
	
}	

function assignXQueryReport($orgrows, $accountrowsbyorg,$pagination, $assignedUsers){ ?>

<div id ="filterbyOrgDiv" style="width:300px;padding:20px;float:left">
	<div style="float:left; padding:0 10px 0 0"><?php echo JText::_("CATALOG_XQUERY_FILTERBYORG"); ?></div>
	<select id="orgfilter" size="1" onchange="filterByOrg()" style="float:left">
	<?php 
	$root_acc_id= JRequest::getVar('root');
	foreach ($orgrows as $orgrow) {?> 
	<option value="<?php echo $orgrow->id ?>" 
		<?php $selected = null;
			if ($orgrow->id == $root_acc_id){?>
			selected="selected"
		<?php }?>
	
	
	 >
	 
	<?php echo  $orgrow->name ?></option>
	<?php }?>
	<option value="" 
		<?php  if ($root_acc_id == ''){?>
			selected="selected"
		 <?php }?>
	
	>
	
	<?php echo JText::_("CATALOG_XQUERY_ALL_ORGS"); ?></option>
	</select>

</div>
<div style="clear:both"></div>
<form action="" method="post" name="adminForm">
	<table class="adminlist">
		<thead>
			<tr>
				<th class='title' style="width:10px"><?php echo JText::_("CORE_SHARP"); ?></th>
				<th class='title' style="width:30px"><?php echo JText::_("CATALOG_XQUERY_USER_FULLNAME");  ?></th>
				<th class='title' style="width:100px"><?php echo JText::_("CATALOG_XQUERY_USER_EMAIL");  ?></th>
				<th class='title' style="width:100px"><?php echo JText::_("CATALOG_XQUERY_ACCOUNTTYPE");  ?></th>	
				<th class='title' style="width:100px"><?php echo JText::_("CATALOG_XQUERY_ENABLEREPORTACCESS");  ?></th>					
				
			</tr>
		</thead>
		<tbody>
			<?php 
				//$limit		=  JRequest::getVar('limit', 10);
				$limitstart	=  JRequest::getVar('limitstart', 0);
				$num = $limitstart+1;
				foreach ($accountrowsbyorg as $accountrow) 
				
				{?> 
				
				<tr>
				<td  style="width:10px"><?php echo $num?></td>
				<td  style="width:30px"><?php echo  $accountrow->account_name  ?></td>
				<td  style="width:100px"><?php echo $accountrow->account_email ?></td>
				<td  style="width:100px"><?php echo $accountrow->account_usertype  ?></td>		
				<td  style="width:10px;text-align:center"><input type="checkbox" name="toggle" value="" <?php  if($assignedUsers[$accountrow->id]) echo "checked";?> onclick="setUserReportAccess(this, <?php echo $accountrow->id?>)" /></td>				
				
				
				</tr>
				
			<?php 
			$num = $num+1;
				}
			?>


		</tbody>
		<tfoot>
		<!-- add pagination for in footer -->
			<tr>	
				<td colspan="13"><?php echo $pagination->getListFooter(); ?></td>
			</tr>
		</tfoot>
		</table>
 </form>

	
		
	
<?php
}



} //end class  HTML_xquery
?>