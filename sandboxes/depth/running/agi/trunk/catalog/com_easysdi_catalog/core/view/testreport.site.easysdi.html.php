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

class testreport {

	function main()
	{
		?>
			<div id="page">
				<h2 class="contentheading"><?php echo JText::_("CATALOG_SEARCH_TITLE"); ?></h2>
				<div class="contentin">
				
					<form method="GET">
						<input type="hidden" name="option" id="option" value="<?php echo JRequest::getVar('option' );?>" /> 
						<fieldset>
							<legend>Rapports simples dans tous les formats</legend>
							<table width="100%">
								<tr>
									<td align="left"><a href="https://demo.depth.ch/geodbmeta/index.php?option=com_easysdi_catalog&task=getReport&metadatatype=geoproduct&reporttype=test&metadata_guid[]=f9e4502d-f753-4ef6-8590-8b8367e12d9d&language=german&lastVersion=no&format=xml">XML de f9e4502d-f753-4ef6-8590-8b8367e12d9d [german] [test]</a></td>
								</tr>
								<tr>
									<td align="left"><a href="https://demo.depth.ch/geodbmeta/index.php?option=com_easysdi_catalog&task=getReport&metadatatype=geoproduct&reporttype=test&metadata_guid[]=f9e4502d-f753-4ef6-8590-8b8367e12d9d&language=german&lastVersion=no&format=csv">CSV de f9e4502d-f753-4ef6-8590-8b8367e12d9d [german] [test]</a></td>
								</tr>
								<tr>
									<td align="left"><a href="https://demo.depth.ch/geodbmeta/index.php?option=com_easysdi_catalog&task=getReport&metadatatype=geoproduct&reporttype=test&metadata_guid[]=f9e4502d-f753-4ef6-8590-8b8367e12d9d&language=german&lastVersion=no&format=rtf">RTF de f9e4502d-f753-4ef6-8590-8b8367e12d9d [german] [test]</a></td>
								</tr>
								<tr>
									<td align="left"><a href="https://demo.depth.ch/geodbmeta/index.php?option=com_easysdi_catalog&task=getReport&metadatatype=geoproduct&reporttype=test&metadata_guid[]=f9e4502d-f753-4ef6-8590-8b8367e12d9d&language=german&lastVersion=no&format=xhtml">XHTML de f9e4502d-f753-4ef6-8590-8b8367e12d9d [german] [test]</a></td>
								</tr>
								<tr>
									<td align="left"><a href="https://demo.depth.ch/geodbmeta/index.php?option=com_easysdi_catalog&task=getReport&metadatatype=geoproduct&reporttype=test&metadata_guid[]=f9e4502d-f753-4ef6-8590-8b8367e12d9d&language=german&lastVersion=no&format=pdf">PDF de f9e4502d-f753-4ef6-8590-8b8367e12d9d [german] [test]</a></td>
								</tr>
							</table>
						</fieldset>
						<fieldset>
							<legend>Rapports sur plusieurs métadonnées en même temps</legend>
							<table width="100%">
								<tr>
									<td align="left"><a href="https://demo.depth.ch/geodbmeta/index.php?option=com_easysdi_catalog&task=getReport&metadatatype=geoproduct&reporttype=test&metadata_guid[]=f9e4502d-f753-4ef6-8590-8b8367e12d9d&metadata_guid[]=0e7ca58c-5e2b-42f5-8c2a-0af2ce284da5&language=german&lastVersion=no&format=xml">XML de f9e4502d-f753-4ef6-8590-8b8367e12d9d et 0e7ca58c-5e2b-42f5-8c2a-0af2ce284da5, versions demandées</a></td>
								</tr>
								<tr>
									<td align="left"><a href="https://demo.depth.ch/geodbmeta/index.php?option=com_easysdi_catalog&task=getReport&metadatatype=geoproduct&reporttype=test&metadata_guid[]=f9e4502d-f753-4ef6-8590-8b8367e12d9d&metadata_guid[]=0e7ca58c-5e2b-42f5-8c2a-0af2ce284da5&language=german&lastVersion=yes&format=xml">XML de f9e4502d-f753-4ef6-8590-8b8367e12d9d et 0e7ca58c-5e2b-42f5-8c2a-0af2ce284da5, dernières versions</a></td>
								</tr>
							</table>
						</fieldset>
						<fieldset>
							<legend>Cas d'erreur</legend>
							<table width="100%">
								<tr>
									<td align="left"><a href="https://demo.depth.ch/geodbmeta/index.php?option=com_easysdi_catalog&task=getReport&metadatatype=geoproduct&reporttype=test&metadata_guid[]=f9e4502d-f753-4ef6-8590-8b8367e12d9d&lastVersion=no&format=xml">XML de f9e4502d-f753-4ef6-8590-8b8367e12d9d, paramètre manquant</a></td>
								</tr>
								<tr>
									<td align="left"><a href="https://demo.depth.ch/geodbmeta/index.php?option=com_easysdi_catalog&task=getReport&metadatatype=geoproduct&reporttype=test&metadata_guid[]=08356f27-f3ca-4f49-8d58-2724c2a1e221&language=german&lastVersion=no&format=csv">CSV de 08356f27-f3ca-4f49-8d58-2724c2a1e221, métadonnée privée</a></td>
								</tr>
								<tr>
									<td align="left"><a href="https://demo.depth.ch/geodbmeta/index.php?option=com_easysdi_catalog&task=getReport&metadatatype=geoproduct&reporttype=test&metadata_guid[]=f9e4502d-f753-4ef6-8590-8b8367e12d95&language=german&lastVersion=no&format=rtf">RTF de f9e4502d-f753-4ef6-8590-8b8367e12d95, pas de métadonnée correspondante dans Easysdi</a></td>
								</tr>
							</table>
						</fieldset>
					</form>
				</div>
			</div>
		<?php
	}
}
?>