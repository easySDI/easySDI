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

class SITE_listPerim {
	
	function show(){
		?>
		<script type="text/javascript" src="./administrator/components/com_easysdi_shop/lib/openlayers2.8/lib/OpenLayers.js"></script>
		<script>
		
		function trim (myString)
		{
		    return myString.replace(/^\s+/g,'').replace(/\s+$/g,'')
		} 
		
		function ValidateFrmt(){
			var perimeterVal = document.getElementById('perimeterContent').value;
			var wkt = new OpenLayers.Format.Text();
			var points = perimeterVal.split("\n");
			
			//add lon/lat at beginning of the array
			if(trim(points[0]) != "lon\tlat"){
				points.unshift("lon\tlat");
			}
			
			//add first point to the end if needed
			if(trim(points[1]) != trim(points[points.length-1]))
				points.push(points[1]);
			
			perimeterVal = points.join("\n");
			
			document.getElementById('perimeterContent').value = perimeterVal;
			
	                var collection = wkt.read(perimeterVal);
			if(!collection) {
				alert("<?php echo JText::_('SHOP_PERIMETER_LOAD_PERIMETER_FROM_LIST_BAD_FORMAT'); ?>");
				return false;
			}else if(!collection.length || collection.length==0){
				alert("<?php echo JText::_('SHOP_PERIMETER_LOAD_PERIMETER_FROM_LIST_EMPTY_COLLECTION'); ?>");
				return false;
			}

			return true;
		}
		</script>
		<div id="metadata" class="contentin">
			<h2 class="contentheading"><?php echo JText::_('SHOP_PERIMETER_LOAD_PERIMETER_FROM_LIST_TITLE'); ?></h2>
				<table width="100%">
				  <tr>
				    <td>&nbsp;</td>
				  </tr>
				  <tr>
				    <td valign="top">
				      <FORM ACTION="index.php" METHOD="POST" target="_parent">
				        <fieldset>
				        <legend><?php echo JText::_('SHOP_PERIMETER_LOAD_PERIMETER_FROM_LIST_COMMENT');?></legend>
					<TEXTAREA ID="perimeterContent" NAME="perimeterContent" COLS=30 ROWS=16></TEXTAREA><BR/>
					<button onclick="return ValidateFrmt();" type="submit"><?php echo JText::_("SHOP_PERIMETER_LOAD_PERIMETER_FROM_LIST_SEND_BTN"); ?></button>
					<input type="hidden" name="option" value="com_easysdi_shop"/>
					<input type="hidden" name="step" value="2"/>
					<input type="hidden" name="task" value="redirectFromListForPerim"/>
					<input type='hidden' name='Itemid' value="<?php echo JRequest::getVar('Itemid'); ?>">
					<input type='hidden' name='perimeter_id' value="<?php echo JRequest::getVar('perimeter_id'); ?>">
					</fieldset>
				      </FORM>
				    </td>
				    <td valign="top">
				      <fieldset>
				      <legend><?php echo JText::_('SHOP_PERIMETER_LOAD_PERIMETER_FROM_LIST_EXAMPLE');?></legend>
				      <p><?php echo JText::_('SHOP_PERIMETER_LOAD_PERIMETER_FROM_LIST_EXAMPLE_HEADER'); ?><BR/></p>
				      <p align="center"><img style="border: 1px solid" src="components/com_easysdi_shop/img/ex_load_perim_csv.png" width="188" height="159"/></p>
				      <BR/>
				      <p>
				      <ul>
				        <li><?php echo JText::_('SHOP_PERIMETER_LOAD_PERIMETER_FROM_LIST_EXAMPLE_FOOTER1'); ?></li>
					<li><?php echo JText::_('SHOP_PERIMETER_LOAD_PERIMETER_FROM_LIST_EXAMPLE_FOOTER2'); ?></li>
					<li><a href="components/com_easysdi_shop/img/ex_csv_tab_file.txt" target="_blank"><?php echo JText::_('SHOP_PERIMETER_LOAD_PERIMETER_FROM_LIST_EXAMPLE_FILE'); ?></a></li>
				      </ul>
				      </p>
				      </fieldset>
				    </td>
				  </tr>
			</div>
			
		<?php
	}
}