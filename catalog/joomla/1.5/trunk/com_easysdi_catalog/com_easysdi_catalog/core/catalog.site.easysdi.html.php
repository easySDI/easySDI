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

class HTML_catalog{

	
function listCatalogContent($pageNav,$cswResults,$option, $total,$searchCriteria){
	global  $mainframe;
	$db =& JFactory::getDBO();
	
		?>	
		<div class="contentin">	
		<h2 class="contentheading"><?php echo JText::_("EASYSDI_CATALOG_TITLE"); ?></h2>
	
		<h3> <?php echo JText::_("EASYSDI_CATALOG_SEARCH_CRITERIA_TITLE"); ?></h3>
		
		<form name="catalog_search_form" id="catalog_search_form" method="POST" > 
		<table width="100%">
			<tr>
				<td align="left">
					<b><?php echo JText::_("EASYSDI_CATALOG_FILTER_TITLE");?></b>&nbsp;
					<input type="text" name="filterfreetextcriteria" value="<?php echo $searchCriteria;?>" class="inputbox"  />			
				</td>
			</tr>
		</table>
		</form>
		
		<button type="submit" class="easysdi_search_button" onClick="document.getElementById('catalog_search_form').submit()"><?php echo JText::_("EASYSDI_CATALOG_SEARCH_BUTTON"); ?></button>
		
		<?php if($cswResults){ ?>
		<br>		
		<table width="100%">
			<tr>																																						
				<td align="left"><?php echo $pageNav->getPagesCounter(); ?></td><td align="right"> <?php echo $pageNav->getPagesLinks(); ?></td>
			</tr>
		</table>
	<h3><?php echo JText::_("EASYSDI_SEARCH_RESULTS_TITLE"); ?></h3>
	
	<span class="easysdi_number_of_metadata_found"><?php echo JText::_("EASYSDI_CATALOG_NUMBER_OF_METADATA_FOUND");?> <?php echo $total ?> </span>
	<table>
	<thead>
	<tr>	
	<th><?php echo JText::_('EASYSDI_CATALOG_PRODUCT_SHARP'); ?></th>
	<th><?php echo JText::_('EASYSDI_CATALOG_ORDERABLE'); ?></th>	
	<th><?php echo JText::_('EASYSDI_CATALOG_PRODUCT_NAME'); ?></th>
	</tr>
	</thead>
	<tbody>
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
			<tr >			
			<td><?php echo $i; ?></td>
			<?php
			$query = "select count(*) from #__easysdi_product where metadata_id = '".$md->getFileIdentifier()."'";
			$db->setQuery( $query);

			$metadataId_count = $db->loadResult();
			if ($db->getErrorNum()) {								
					$metadataId_count = '0';					
			}			
			?>
			<td><div class="<?php if ($metadataId_count>0) {echo "easysdi_product_exists";} else {echo "easysdi_product_does_not_exist";} ?>"><?php echo $metadataId_count;?> </div></td>								
			<td><a class="modal" title="<?php echo JText::_("EASYSDI_VIEW_MD"); ?>" href="./index.php?tmpl=component&option=com_easysdi_shop&task=showMetadata&id=<?php echo $md->getFileIdentifier();  ?>" rel="{handler:'iframe',size:{x:500,y:500}}"> <?php echo $md->getDataIdentificationTitle();?></a></td>			
			</tr>			
				<?php		
		}		
	?>
	</tbody>
	</table>
	<?php } ?>
		</div>
	<?php
		
}


}
?>