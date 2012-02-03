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

class HTML_config {
	
	function showConfig($option, $coreList, $catalogItem, $catalogList, $shopItem, $shopList, $proxyItem, $proxyList, $monitorItem, $monitorList, $publishItem, $publishList,$mapItem, $mapList,$fieldsLength, $attributetypelist )
	{
		global $mainframe;

		// Load tooltips behavior
		JHTML::_('behavior.tooltip');
		JHTML::_('behavior.switcher');

		// Load component specific configurations
		$table =& JTable::getInstance('component');
		$table->loadByOption( 'com_users' );
		$userparams = new JParameter( $table->params, JPATH_ADMINISTRATOR.DS.'components'.DS.'com_users'.DS.'config.xml' );
		$table->loadByOption( 'com_media' );
		$mediaparams = new JParameter( $table->params, JPATH_ADMINISTRATOR.DS.'components'.DS.'com_media'.DS.'config.xml' );

		// Build the component's submenu
		?>
		<div class="submenu-box">
			<div class="submenu-pad">
				<ul id="submenu" class="configuration">
					<li><a id="core" class="active"><?php echo JText::_( 'CORE_CONFIGURATION_CORE_TAB_TITLE' ); ?></a></li>
<?php
if ($catalogItem > 0){
?>
					<li><a id="catalog"><?php echo JText::_( 'CORE_CONFIGURATION_CATALOG_TAB_TITLE' ); ?></a></li>
<?php
}
if ($shopItem > 0){
?>
					<li><a id="shop"><?php echo JText::_( 'CORE_CONFIGURATION_SHOP_TAB_TITLE' ); ?></a></li>
<?php
}
if ($proxyItem > 0){
?>					
					<li><a id="proxy"><?php echo JText::_( 'CORE_CONFIGURATION_PROXY_TAB_TITLE' ); ?></a></li>
<?php
}
if ($monitorItem > 0){
?>					
					<li><a id="monitor"><?php echo JText::_( 'CORE_CONFIGURATION_MONITOR_TAB_TITLE' ); ?></a></li>
<?php
}
if ($publishItem > 0){
?>					
					<li><a id="publish"><?php echo JText::_( 'CORE_CONFIGURATION_PUBLISH_TAB_TITLE' ); ?></a></li>
<?php
}
if ($mapItem > 0){
?>					
					<li><a id="map"><?php echo JText::_( 'CORE_CONFIGURATION_MAP_TAB_TITLE' ); ?></a></li>
<?php
}
?>
				</ul>
				<div class="clr"></div>
			</div>
		</div>
		<div class="clr"></div>
		<?php 
		// Load settings for the FTP layer
		jimport('joomla.client.helper');
		$ftp =& JClientHelper::setCredentialsFromRequest('ftp');
		?>
		<form action="index.php" method="post" name="adminForm">
		<?php if ($ftp) {
			require_once($tmplpath.DS.'ftp.php');
		} ?>
		<div id="config-document">
			<div id="page-core">
				<table class="noshow">
					<tr>
						<td width="65%">
							<fieldset class="adminform">
								<legend><?php echo JText::_( 'CORE_CONFIGURATION_CORE_FIELDSET_TITLE' ); ?></legend>
								 
								<table class="admintable" cellspacing="1">
									<tbody>
									<tr>
										<td valign="top" class="key">
											<span class="editlinktip hasTip" title="<?php echo JText::_( 'CORE_CONFIGURATION_CORE_DESCRIPTIONLENGTH_LABEL' ); ?>">
												<?php echo JText::_( 'CORE_CONFIGURATION_CORE_DESCRIPTIONLENGTH_LABEL' ); ?>
											</span>
										</td>
										<td>
											<input class="text_area" type="text" size="10" name="description_length" value="<?php echo $coreList['DESCRIPTION_LENGTH']->value; ?>" maxlength="<?php echo $fieldsLength['value'];?>" />
										</td>
									</tr>
									<tr>
										<td valign="top" class="key">
											<span class="editlinktip hasTip" title="<?php echo JText::_( 'CORE_CONFIGURATION_CORE_LOGOWIDTH_LABEL' ); ?>">
												<?php echo JText::_( 'CORE_CONFIGURATION_CORE_LOGOWIDTH_LABEL' ); ?>
											</span>
										</td>
										<td>
											<input class="text_area" type="text" size="10" name="logo_width" value="<?php echo $coreList['LOGO_WIDTH']->value; ?>" maxlength="<?php echo $fieldsLength['value'];?>" />
										</td>
									</tr>
									<tr>
										<td valign="top" class="key">
											<span class="editlinktip hasTip" title="<?php echo JText::_( 'CORE_CONFIGURATION_CORE_LOGOHEIGHT_LABEL' ); ?>">
												<?php echo JText::_( 'CORE_CONFIGURATION_CORE_LOGOHEIGHT_LABEL' ); ?>
											</span>
										</td>
										<td>
											<input class="text_area" type="text" size="10" name="logo_height" value="<?php echo $coreList['LOGO_HEIGHT']->value; ?>" maxlength="<?php echo $fieldsLength['value'];?>" />
										</td>
									</tr>
									<tr>
										<td valign="top" class="key">
											<span class="editlinktip hasTip" title="<?php echo JText::_( 'CORE_CONFIGURATION_CORE_WELCOMEREDIRECT_LABEL' ); ?>">
												<?php echo JText::_( 'CORE_CONFIGURATION_CORE_WELCOMEREDIRECT_LABEL' ); ?>
											</span>
										</td>
										<td>
											<input class="text_area" type="text" size="100" name="welcome_redirect_url" value="<?php echo $coreList['WELCOME_REDIRECT_URL']->value; ?>" maxlength="<?php echo $fieldsLength['value'];?>" />
										</td>
									</tr>
									</tbody>
								</table> 
							</fieldset>	
						</td>
					</tr>
				</table>
			</div>
<?php
if ($catalogItem > 0){
?>
			<div id="page-catalog">
				<table class="noshow">
					<tr>
						<td width="60%">
							<fieldset class="adminform">
								<legend><?php echo JText::_( 'CORE_CONFIGURATION_CATALOG_FIELDSET_TITLE' ); ?></legend>
								<table class="admintable" cellspacing="1">
									<tbody>
									<tr>
										<td valign="top" class="key">
											<span class="editlinktip hasTip" title="<?php echo JText::_( 'CORE_CONFIGURATION_CORE_JAVABRIDGE_LABEL' ); ?>">
												<?php echo JText::_( 'CORE_CONFIGURATION_CORE_JAVABRIDGE_LABEL' ); ?>
											</span>
										</td>
										<td>
											<input class="text_area" type="text" size="100" name="java_bridge_url" value="<?php echo $catalogList['JAVA_BRIDGE_URL']->value; ?>" maxlength="<?php echo $fieldsLength['value'];?>" />
										</td>
									</tr>
									<tr>
										<td valign="top" class="key">
											<span class="editlinktip hasTip" title="<?php echo JText::_( 'CORE_CONFIGURATION_CATALOG_URL_LABEL' ); ?>">
												<?php echo JText::_( 'CORE_CONFIGURATION_CATALOG_URL_LABEL' ); ?>
											</span>
										</td>
										<td>
											<input class="text_area" type="text" size="100" name="catalog_url" value="<?php echo $catalogList['CATALOG_URL']->value; ?>" maxlength="<?php echo $fieldsLength['value'];?>" />
										</td>
									</tr>
									<tr>
										<td valign="top" class="key">
											<span class="editlinktip hasTip" title="<?php echo JText::_( 'CORE_CONFIGURATION_CATALOG_PAGINATIONSEARCHRESULT_LABEL' ); ?>">
												<?php echo JText::_( 'CORE_CONFIGURATION_CATALOG_PAGINATIONSEARCHRESULT_LABEL' ); ?>
											</span>
										</td>
										<td>
											<input class="text_area" type="text" size="10" name="catalog_pagination_searchresult" value="<?php echo $catalogList['CATALOG_PAGINATION_SEARCHRESULT']->value; ?>" maxlength="<?php echo $fieldsLength['value'];?>" />
										</td>
									</tr>
									<tr>
										<td valign="top" class="key">
											<span class="editlinktip hasTip" title="<?php echo JText::_( 'CORE_CONFIGURATION_CATALOG_METADATA_COLLAPSE' ); ?>">
												<?php echo JText::_( 'CORE_CONFIGURATION_CATALOG_METADATA_COLLAPSE' ); ?>
											</span>
										</td>
										<td>
											<?php
												$collapseState = array();
												$collapseState[] = JHTML::_('select.option','true', JText::_("CORE_CONFIGURATION_CATALOG_METADATA_COLLAPSE_CLOSE") );
												$collapseState[] = JHTML::_('select.option','false', JText::_("CORE_CONFIGURATION_CATALOG_METADATA_COLLAPSE_OPEN") ); 
											
												echo JHTML::_("select.genericlist",$collapseState, 'metadata_collapse', 'size="1" class="inputbox"', 'value', 'text', $catalogList['METADATA_COLLAPSE']->value ); ?>
										</td>
									</tr>
									<tr>
										<td valign="top" class="key">
											<span class="editlinktip hasTip" title="<?php echo JText::_( 'CORE_CONFIGURATION_CATALOG_METADATA_QTIPDELAY' ); ?>">
												<?php echo JText::_( 'CORE_CONFIGURATION_CATALOG_METADATA_QTIPDELAY' ); ?>
											</span>
										</td>
										<td>
											<input class="text_area" type="text" size="100" name="catalog_metadata_qtipdelay" value="<?php echo $catalogList['CATALOG_METADATA_QTIPDELAY']->value; ?>" maxlength="<?php echo $fieldsLength['value'];?>" />
										</td>
										<td>
											<div style="font-weight: bold" >
												<img src="<?php echo JURI::root(true);?>/includes/js/ThemeOffice/warning.png" style="vertical-align:top" alt="" /> <?php echo JText::_( 'CORE_CONFIGURATION_CATALOG_METADATA_QTIPDELAY_FORMAT' ); ?>
											</div>
										</td>
										<td>
											<div  ><?php echo JText::_( 'CORE_CONFIGURATION_CATALOG_METADATA_QTIPDELAY_NOTE' ); ?></div>
										</td>
									</tr>
									<tr>
										<td valign="top" class="key">
											<span class="editlinktip hasTip" title="<?php echo JText::_( 'CORE_CONFIGURATION_CATALOG_ENCODING_CODE' ); ?>">
												<?php echo JText::_( 'CORE_CONFIGURATION_CATALOG_ENCODING_CODE' ); ?>
											</span>
										</td>
										<td>
											<input class="text_area" type="text" size="100" name="catalog_encoding_code" value="<?php echo $catalogList['CATALOG_ENCODING_CODE']->value; ?>" maxlength="<?php echo $fieldsLength['value'];?>" />
										</td>
									</tr>
									<tr>
										<td valign="top" class="key">
											<span class="editlinktip hasTip" title="<?php echo JText::_( 'CORE_CONFIGURATION_CATALOG_ENCODING_VAL' ); ?>">
												<?php echo JText::_( 'CORE_CONFIGURATION_CATALOG_ENCODING_VAL' ); ?>
											</span>
										</td>
										<td>
											<input class="text_area" type="text" size="100" name="catalog_encoding_val" value="<?php echo $catalogList['CATALOG_ENCODING_VAL']->value; ?>" maxlength="<?php echo $fieldsLength['value'];?>" />
										</td>
									</tr>
									<tr>
										<td valign="top" class="key">
											<span class="editlinktip hasTip" title="<?php echo JText::_( 'CORE_CONFIGURATION_CATALOG_BOUNDARY_ISOCODE_LABEL' ); ?>">
												<?php echo JText::_( 'CORE_CONFIGURATION_CATALOG_BOUNDARY_ISOCODE_LABEL' ); ?>
											</span>
										</td>
										<td>
											<input class="text_area" type="text" size="100" name="catalog_boundary_isocode" value="<?php echo $catalogList['CATALOG_BOUNDARY_ISOCODE']->value; ?>" maxlength="<?php echo $fieldsLength['value'];?>" />
										</td>
									</tr>
									<tr>
										<td valign="top" class="key">
											<span class="editlinktip hasTip" title="<?php echo JText::_( 'CORE_CONFIGURATION_CATALOG_BOUNDARY_NORTH_LABEL' ); ?>">
												<?php echo JText::_( 'CORE_CONFIGURATION_CATALOG_BOUNDARY_NORTH_LABEL' ); ?>
											</span>
										</td>
										<td>
											<input class="text_area" type="text" size="100" name="catalog_boundary_north" value="<?php echo $catalogList['CATALOG_BOUNDARY_NORTH']->value; ?>" maxlength="<?php echo $fieldsLength['value'];?>" />
										</td>
									</tr>
									<tr>
										<td valign="top" class="key">
											<span class="editlinktip hasTip" title="<?php echo JText::_( 'CORE_CONFIGURATION_CATALOG_BOUNDARY_SOUTH_LABEL' ); ?>">
												<?php echo JText::_( 'CORE_CONFIGURATION_CATALOG_BOUNDARY_SOUTH_LABEL' ); ?>
											</span>
										</td>
										<td>
											<input class="text_area" type="text" size="100" name="catalog_boundary_south" value="<?php echo $catalogList['CATALOG_BOUNDARY_SOUTH']->value; ?>" maxlength="<?php echo $fieldsLength['value'];?>" />
										</td>
									</tr>
									<tr>
										<td valign="top" class="key">
											<span class="editlinktip hasTip" title="<?php echo JText::_( 'CORE_CONFIGURATION_CATALOG_BOUNDARY_EAST_LABEL' ); ?>">
												<?php echo JText::_( 'CORE_CONFIGURATION_CATALOG_BOUNDARY_EAST_LABEL' ); ?>
											</span>
										</td>
										<td>
											<input class="text_area" type="text" size="100" name="catalog_boundary_east" value="<?php echo $catalogList['CATALOG_BOUNDARY_EAST']->value; ?>" maxlength="<?php echo $fieldsLength['value'];?>" />
										</td>
									</tr>
									<tr>
										<td valign="top" class="key">
											<span class="editlinktip hasTip" title="<?php echo JText::_( 'CORE_CONFIGURATION_CATALOG_BOUNDARY_WEST_LABEL' ); ?>">
												<?php echo JText::_( 'CORE_CONFIGURATION_CATALOG_BOUNDARY_WEST_LABEL' ); ?>
											</span>
										</td>
										<td>
											<input class="text_area" type="text" size="100" name="catalog_boundary_west" value="<?php echo $catalogList['CATALOG_BOUNDARY_WEST']->value; ?>" maxlength="<?php echo $fieldsLength['value'];?>" />
										</td>
									</tr>
									<tr>
										<td valign="top" class="key">
											<span class="editlinktip hasTip" title="<?php echo JText::_( 'CORE_CONFIGURATION_CATALOG_BOUNDARY_TYPE_LABEL' ); ?>">
												<?php echo JText::_( 'CORE_CONFIGURATION_CATALOG_BOUNDARY_TYPE_LABEL' ); ?>
											</span>
										</td>
										<td>
											<?php echo JHTML::_("select.genericlist",$attributetypelist, 'catalog_boundary_type', 'size="1" class="inputbox"', 'value', 'text', $catalogList['CATALOG_BOUNDARY_TYPE']->value ); ?>
										</td>
									</tr>
									<tr>
										<td valign="top" class="key">
											<span class="editlinktip hasTip" title="<?php echo JText::_( 'CORE_CONFIGURATION_CATALOG_SEARCH_MULTILIST_LENGTH' ); ?>">
												<?php echo JText::_( 'CORE_CONFIGURATION_CATALOG_SEARCH_MULTILIST_LENGTH' ); ?>
											</span>
										</td>
										<td>
											<input class="text_area" type="text" size="4" name="catalog_search_multilist_length" value="<?php echo $catalogList['CATALOG_SEARCH_MULTILIST_LENGTH']->value; ?>" maxlength="<?php echo $fieldsLength['value'];?>" />
										</td>
									</tr>
									<tr>
										<td valign="top" class="key">
											<span class="editlinktip hasTip" title="<?php echo JText::_( 'CORE_CONFIGURATION_CATALOG_SEARCH_OGCFILTERFILEID' ); ?>">
												<?php echo JText::_( 'CORE_CONFIGURATION_CATALOG_SEARCH_OGCFILTERFILEID' ); ?>
											</span>
										</td>
										<td>
											<input class="text_area" type="text" size="50" name="catalog_search_ogcfilterfileid" value="<?php echo $catalogList['CATALOG_SEARCH_OGCFILTERFILEID']->value; ?>" maxlength="<?php echo $fieldsLength['value'];?>" />
										</td>
									</tr>
									<tr>
										<td valign="top" class="key">
											<span class="editlinktip hasTip" title="<?php echo JText::_( 'CORE_CONFIGURATION_CATALOG_MXQUERYURL' ); ?>">
												<?php echo JText::_( 'CORE_CONFIGURATION_CATALOG_MXQUERYURL' ); ?>
											</span>
										</td>
										<td>
											<input class="text_area" type="text" size="50" name="catalog_mxqueryurl" value="<?php echo $catalogList['CATALOG_MXQUERYURL']->value; ?>" maxlength="<?php echo $fieldsLength['value'];?>" />
										</td>
									</tr>									
									<tr>
										<td valign="top" class="key">
											<span class="editlinktip hasTip" title="<?php echo JText::_( 'CORE_CONFIGURATION_CATALOG_MXQUERYPAGINATION' ); ?>">
												<?php echo JText::_( 'CORE_CONFIGURATION_CATALOG_MXQUERYPAGINATION' ); ?>
											</span>
										</td>
										<td>
											<input class="text_area" type="text" size="50" name="catalog_mxquerypagination" value="<?php echo $catalogList['CATALOG_MXQUERYPAGINATION']->value; ?>" maxlength="<?php echo $fieldsLength['value'];?>" />
										</td>
									</tr>
									<tr>
										<td valign="top" class="key">
											<span class="editlinktip hasTip" title="<?php echo JText::_( 'CORE_CONFIGURATION_CATALOG_THESAURUSRURL' ); ?>">
												<?php echo JText::_( 'CORE_CONFIGURATION_CATALOG_THESAURUSRURL' ); ?>
											</span>
										</td>
										<td>
											<input class="text_area" type="text" size="50" name="thesaurusUrl" value="<?php echo $catalogList['thesaurusUrl']->value; ?>" maxlength="<?php echo $fieldsLength['value'];?>" />
										</td>
									</tr>
									<tr>
										<td valign="top" class="key" colspan="2">
											<fieldset class="adminform">
												<legend>
												<?php echo JText::_( 'CORE_CONFIGURATION_BBOXMAP_FIELDSET_TITLE' ); ?>
												</legend>

												<table class="admintable" cellspacing="1">
													<tbody>
														<tr>
															<td valign="top" class="key" 	style="width: 140px;"><span
																class="editlinktip hasTip"
																title="<?php echo JText::_( 'CORE_CONFIGURATION_BBOXMAP_FIELDSET_TIP' ); ?>">
																<?php echo JText::_( 'CORE_CONFIGURATION_BBOXMAP_FIELDSET_LABEL' ); ?>
															</span>
															</td>
															<td><textarea class="textarea resolutions"
																	style="height: 200px; width: 440px;"
																	name="defaultBboxConfig"
																	maxlength="<?php  echo $fieldsLength['value'];?>" /><?php  echo trim($catalogList['defaultBboxConfig']->value);?></textarea>
															</td>
														</tr>
														<tr>
															<td valign="top" class="key">
																<span class="editlinktip hasTip" title="<?php echo JText::_( 'CORE_CONFIGURATION_BBOXMAP_FIELDSET_LEFT' ); ?>">
																	<?php echo JText::_( 'CORE_CONFIGURATION_BBOXMAP_FIELDSET_LEFT' ); ?>
																</span>
															</td>
															<td>
																<input class="text_area" type="text" size="100" name="defaultBboxConfigExtentLeft" value="<?php echo trim($catalogList['defaultBboxConfigExtentLeft']->value); ?>" maxlength="<?php echo $fieldsLength['value'];?>" />
															</td>
														</tr>
														<tr>
															<td valign="top" class="key">
																<span class="editlinktip hasTip" title="<?php echo JText::_( 'CORE_CONFIGURATION_BBOXMAP_FIELDSET_BOTTOM' ); ?>">
																	<?php echo JText::_( 'CORE_CONFIGURATION_BBOXMAP_FIELDSET_BOTTOM' ); ?>
																</span>
															</td>
															<td>
																<input class="text_area" type="text" size="100" name="defaultBboxConfigExtentBottom" value="<?php echo trim($catalogList['defaultBboxConfigExtentBottom']->value); ?>" maxlength="<?php echo $fieldsLength['value'];?>" />
															</td>
														</tr>
														<tr>
															<td valign="top" class="key">
																<span class="editlinktip hasTip" title="<?php echo JText::_( 'CORE_CONFIGURATION_BBOXMAP_FIELDSET_RIGHT' ); ?>">
																	<?php echo JText::_( 'CORE_CONFIGURATION_BBOXMAP_FIELDSET_RIGHT' ); ?>
																</span>
															</td>
															<td>
																<input class="text_area" type="text" size="100" name="defaultBboxConfigExtentRight" value="<?php echo trim($catalogList['defaultBboxConfigExtentRight']->value); ?>" maxlength="<?php echo $fieldsLength['value'];?>" />
															</td>
														</tr>
														<tr>
															<td valign="top" class="key">
																<span class="editlinktip hasTip" title="<?php echo JText::_( 'CORE_CONFIGURATION_BBOXMAP_FIELDSET_TOP' ); ?>">
																	<?php echo JText::_( 'CORE_CONFIGURATION_BBOXMAP_FIELDSET_TOP' ); ?>
																</span>
															</td>
															<td>
																<input class="text_area" type="text" size="100" name="defaultBboxConfigExtentTop" value="<?php echo trim($catalogList['defaultBboxConfigExtentTop']->value); ?>" maxlength="<?php echo $fieldsLength['value'];?>" />
															</td>
														</tr>
																			
													</tbody>
												</table>
											</fieldset>
										</td>
									</tr> 
								</tbody>
								</table>
							</fieldset>	
						</td>
					</tr>
				</table>
			</div>
<?php
}
if ($shopItem > 0){
?>
			<div id="page-shop">
				<table class="noshow">
					<tr>
						<td width="60%">
							<fieldset class="adminform">
								<legend><?php echo JText::_( 'CORE_CONFIGURATION_SHOP_FIELDSET_TITLE' ); ?></legend>
								
								<table class="admintable" cellspacing="1">
									<tbody>
									<tr>
										<td valign="top" class="key">
											<span class="editlinktip hasTip" title="<?php echo JText::_( 'CORE_CONFIGURATION_SHOP_PROXYHOST' ); ?>">
												<?php echo JText::_( 'CORE_CONFIGURATION_SHOP_PROXYHOST' ); ?>
											</span>
										</td>
										<td>
											<input class="text_area" type="text" size="100" name="proxyhost" value="<?php echo $shopList['SHOP_CONFIGURATION_PROXYHOST']->value; ?>" maxlength="<?php echo $fieldsLength['value'];?>" />
										</td>
									</tr>
									<tr>
										<td valign="top" class="key">
											<span class="editlinktip hasTip" title="<?php echo JText::_( 'CORE_CONFIGURATION_SHOP_MAX_FILE_SIZE' ); ?>">
												<?php echo JText::_( 'CORE_CONFIGURATION_SHOP_MAX_FILE_SIZE' ); ?>
											</span>
										</td>
										<td>
											<input class="text_area" type="text" size="10" name="max_file_size" value="<?php echo $shopList['SHOP_CONFIGURATION_MAX_FILE_SIZE']->value; ?>" maxlength="<?php echo $fieldsLength['value'];?>" />
										</td>
									</tr>
									<tr>
										<td valign="top" class="key">
											<span class="editlinktip hasTip" title="<?php echo JText::_( 'CORE_CONFIGURATION_SHOP_ARCHIVE_DELAY' ); ?>">
												<?php echo JText::_( 'CORE_CONFIGURATION_SHOP_ARCHIVE_DELAY' ); ?>
											</span>
										</td>
										<td>
											<input class="text_area" type="text" size="10" name="archive_delay" value="<?php echo $shopList['SHOP_CONFIGURATION_ARCHIVE_DELAY']->value; ?>" maxlength="<?php echo $fieldsLength['value'];?>" />
										</td>
									</tr>
									<tr>
										<td valign="top" class="key">
											<span class="editlinktip hasTip" title="<?php echo JText::_( 'CORE_CONFIGURATION_SHOP_HISTORY_DELAY' ); ?>">
												<?php echo JText::_( 'CORE_CONFIGURATION_SHOP_HISTORY_DELAY' ); ?>
											</span>
										</td>
										<td>
											<input class="text_area" type="text" size="10" name="history_delay" value="<?php echo $shopList['SHOP_CONFIGURATION_HISTORY_DELAY']->value; ?>" maxlength="<?php echo $fieldsLength['value'];?>" />
										</td>
									</tr>
									
									<tr>
										<td valign="top" class="key">
											<span class="editlinktip hasTip" title="<?php echo JText::_( 'CORE_CONFIGURATION_SHOP_CADDY_DESC_LENGTH' ); ?>">
												<?php echo JText::_( 'CORE_CONFIGURATION_SHOP_CADDY_DESC_LENGTH' ); ?>
											</span>
										</td>
										<td>
											<input class="text_area" type="text" size="100" name="caddy_description_length" value="<?php echo $shopList['SHOP_CONFIGURATION_CADDY_DESC_LENGTH']->value; ?>" maxlength="<?php echo $fieldsLength['value'];?>" />
										</td>
									</tr>
									<tr>
										<td valign="top" class="key">
											<span class="editlinktip hasTip" title="<?php echo JText::_( 'CORE_CONFIGURATION_SHOP_MOD_PERIM_AREAPRECISION' ); ?>">
												<?php echo JText::_( 'CORE_CONFIGURATION_SHOP_MOD_PERIM_AREAPRECISION' ); ?>
											</span>
										</td>
										<td>
											<input class="text_area" type="text" size="100" name="mod_perim_area_precision" value="<?php echo $shopList['SHOP_CONFIGURATION_MOD_PERIM_AREAPRECISION']->value; ?>" maxlength="<?php echo $fieldsLength['value'];?>" />
										</td>
									</tr>
									<tr>
										<td valign="top" class="key">
											<span class="editlinktip hasTip" title="<?php echo JText::_( 'CORE_CONFIGURATION_SHOP_MOD_PERIM_METERTOKILOMETERLIMIT' ); ?>">
												<?php echo JText::_( 'CORE_CONFIGURATION_SHOP_MOD_PERIM_METERTOKILOMETERLIMIT' ); ?>
											</span>
										</td>
										<td>
											<input class="text_area" type="text" size="100" name="mod_perim_metertokilometerlimit" value="<?php echo $shopList['SHOP_CONFIGURATION_MOD_PERIM_METERTOKILOMETERLIMIT']->value; ?>" maxlength="<?php echo $fieldsLength['value'];?>" />
										</td>
									</tr>
									<tr>
										<td valign="top" class="key">
											<span class="editlinktip hasTip" title="<?php echo JText::_( 'CORE_CONFIGURATION_SHOP_ARTICLE_STEP4' ); ?>">
												<?php echo JText::_( 'CORE_CONFIGURATION_SHOP_ARTICLE_STEP4' ); ?>
											</span>
										</td>
										<td>
											<input class="text_area" type="text" size="100" name="shop_article_step4" value="<?php echo $shopList['SHOP_CONFIGURATION_ARTICLE_STEP4']->value; ?>" maxlength="<?php echo $fieldsLength['value'];?>" />
										</td>
										<td>
											<div style="font-weight: bold" >
												<img src="<?php echo JURI::root(true);?>/includes/js/ThemeOffice/warning.png" style="vertical-align:top" alt="" /> <?php echo JText::_( 'CORE_CONFIGURATION_SHOP_PLUGIN_REQUIREMENTS' ); ?>
											</div>
										</td>
										<td>
											<div  ><?php echo JText::_( 'CORE_CONFIGURATION_SHOP_PLUGIN_CONTENT_FORMAT' ); ?></div>
										</td>
									</tr>
									<tr>
										<td valign="top" class="key">
											<span class="editlinktip hasTip" title="<?php echo JText::_( 'CORE_CONFIGURATION_SHOP_ARTICLE_STEP5' ); ?>">
												<?php echo JText::_( 'CORE_CONFIGURATION_SHOP_ARTICLE_STEP5' ); ?>
											</span>
										</td>
										<td>
											<input class="text_area" type="text" size="100" name="shop_article_step5" value="<?php echo $shopList['SHOP_CONFIGURATION_ARTICLE_STEP5']->value; ?>" maxlength="<?php echo $fieldsLength['value'];?>" />
										</td>
										<td>
											<div style="font-weight: bold" >
												<img src="<?php echo JURI::root(true);?>/includes/js/ThemeOffice/warning.png" style="vertical-align:top" alt="" /> <?php echo JText::_( 'CORE_CONFIGURATION_SHOP_PLUGIN_REQUIREMENTS' ); ?>
											</div>
										</td>
										<td>
											<div  ><?php echo JText::_( 'CORE_CONFIGURATION_SHOP_PLUGIN_CONTENT_FORMAT' ); ?></div>
										</td>
									</tr>
									<tr>
										<td valign="top" class="key">
											<span class="editlinktip hasTip" title="<?php echo JText::_( 'CORE_CONFIGURATION_SHOP_ARTICLE_TERMS_OF_USE' ); ?>">
												<?php echo JText::_( 'CORE_CONFIGURATION_SHOP_ARTICLE_TERMS_OF_USE' ); ?>
											</span>
										</td>
										<td>
											<input class="text_area" type="text" size="100" name="shop_article_terms_of_use" value="<?php echo $shopList['SHOP_CONFIGURATION_ARTICLE_TERMS_OF_USE']->value; ?>" maxlength="<?php echo $fieldsLength['value'];?>" />
										</td>
										<td>
											<div style="font-weight: bold" >
												<img src="<?php echo JURI::root(true);?>/includes/js/ThemeOffice/warning.png" style="vertical-align:top" alt="" /> <?php echo JText::_( 'CORE_CONFIGURATION_SHOP_PLUGIN_REQUIREMENTS' ); ?>
											</div>
										</td>
										<td>
											<div  ><?php echo JText::_( 'CORE_CONFIGURATION_SHOP_PLUGIN_CONTENT_FORMAT' ); ?></div>
										</td>
									</tr>
									</tbody>
								</table>
								
							</fieldset>				
						</td>
					</tr>
				</table>
			</div>
<?php
}
if ($proxyItem > 0){
?>	
			<div id="page-proxy">
				<table class="noshow">
					<tr>
						<td width="60%">
							<fieldset class="adminform">
								<legend><?php echo JText::_( 'CORE_CONFIGURATION_PROXY_FIELDSET_TITLE' ); ?></legend>
								 
								<table class="admintable" cellspacing="1">
									<tbody>
									<tr>
										<td valign="top" class="key">
											<span class="editlinktip hasTip" title="<?php echo JText::_( 'PROXY_CONFIG' ); ?>">
												<?php echo JText::_( 'PROXY_CONFIG' ); ?>
											</span>
										</td>
										<td>
											<input class="text_area" type="text" size="100" name="proxy_config" value="<?php echo $proxyList['PROXY_CONFIG']->value; ?>" maxlength="<?php echo $fieldsLength['value'];?>" />
										</td>
									</tr>
									</tbody>
								</table>
							</fieldset>	
						</td>
					</tr>
				</table>
			</div>
		
<?php
}
if ($monitorItem > 0){
?>	
			<div id="page-monitor">
				<table class="noshow">
					<tr>
						<td width="60%">
							<fieldset class="adminform">
								<legend><?php echo JText::_( 'CORE_CONFIGURATION_MONITOR_FIELDSET_TITLE' ); ?></legend>
								 
								<table class="admintable" cellspacing="1">
									<tbody>
									<tr>
										<td valign="top" class="key">
											<span class="editlinktip hasTip" title="<?php echo JText::_( 'MONITOR_CONFIG' ); ?>">
												<?php echo JText::_( 'MONITOR_CONFIG' ); ?>
											</span>
										</td>
										<td>
											<input class="text_area" type="text" size="100" name="monitor_url" value="<?php echo $monitorList['MONITOR_URL']->value; ?>" maxlength="<?php echo $fieldsLength['value'];?>" />
										</td>
									</tr>
									</tbody>
								</table>
							</fieldset>	
						</td>
					</tr>
				</table>
			</div>
		
<?php
}
	if ($publishItem > 0){
?>	
			<div id="page-publish">
				<table class="noshow">
					<tr>
						<td width="60%">
							<fieldset class="adminform">
								<legend><?php echo JText::_( 'CORE_CONFIGURATION_PUBLISH_FIELDSET_TITLE' ); ?></legend>
								 
								<table class="admintable" cellspacing="1">
									<tbody>
									<tr>
										<td valign="top" class="key">
											<span class="editlinktip hasTip" title="<?php echo JText::_( 'PUBLISH_CONFIG' ); ?>">
												<?php echo JText::_( 'PUBLISH_CONFIG' ); ?>
											</span>
										</td>
										<td>
											<input class="text_area" type="text" size="100" name="publish_url" value="<?php echo $publishList['WPS_PUBLISHER']->value; ?>" maxlength="<?php echo $fieldsLength['value'];?>" />
										</td>
									</tr>
									</tbody>
								</table>
							</fieldset>	
						</td>
					</tr>
				</table>
			</div>
		
<?php
}
	if ($mapItem > 0){
		JHTML::script('jquery-1.3.2.min.js', 'components/com_easysdi_map/externals/jquery/');
?>	
<script>
var $j = jQuery.noConflict();
$j(document).ready(function() {
	!$j("input[name=mapResolutionOverScale]:radio").change(
			function(e){
				if (e.target.value==0)
				{
					  $j(".scales").removeAttr('disabled'); 
					  $j(".resolutions").attr("disabled","disabled");
				}
				else if (e.target.value==1)  {
					$j(".resolutions").removeAttr('disabled');
					$j(".scales").attr("disabled","disabled");
				}
				}
			);
});
</script>
			<div id="page-map">
				<table class="noshow">
					<tr>
						<td width="60%">
							<fieldset class="adminform">
								<legend><?php echo JText::_( 'CORE_CONFIGURATION_MAP_FIELDSET_TITLE' ); ?></legend>
								 
								<table class="admintable" cellspacing="1">
									<tbody>
									<tr>
										<td valign="top" class="key">
											<span class="editlinktip hasTip" title="<?php echo JText::_( 'CORE_CONFIGURATION_MAP_COMPONENT_PATH_TIP' ); ?>">
												<?php echo JText::_( 'CORE_CONFIGURATION_MAP_COMPONENT_PATH_LABEL' ); ?>
											</span>
										</td>
										<td>
											<input class="text_area" type="text" size="100" name="componentPath" value="<?php echo $mapList['componentPath']->value; ?>" maxlength="<?php echo $fieldsLength['value'];?>" />
										</td>
									</tr>
									<tr>
										<td valign="top" class="key">
											<span class="editlinktip hasTip" title="<?php echo JText::_( 'CORE_CONFIGURATION_MAP_COMPONENT_URL_TIP' ); ?>">
												<?php echo JText::_( 'CORE_CONFIGURATION_MAP_COMPONENT_URL_LABEL' ); ?>
											</span>
										</td>
										<td>
											<input class="text_area" type="text" size="100" name="componentUrl" value="<?php echo $mapList['componentUrl']->value; ?>" maxlength="<?php echo $fieldsLength['value'];?>" />
										</td>
									</tr>
									<tr>
										<td valign="top" class="key">
											<span class="editlinktip hasTip" title="<?php echo JText::_( 'CORE_CONFIGURATION_MAP_LAYERPROXYXMLFILE_TIP' ); ?>">
												<?php echo JText::_( 'CORE_CONFIGURATION_MAP_LAYERPROXYXMLFILE_LABEL' ); ?>
											</span>
										</td>
										<td>
											<input class="text_area" type="text" size="100" name="layerProxyXMLFile" value="<?php echo $mapList['layerProxyXMLFile']->value; ?>" maxlength="<?php echo $fieldsLength['value'];?>" />
										</td>
									</tr>
									<tr>
										<td valign="top" class="key">
											<span class="editlinktip hasTip" title="<?php echo JText::_( 'CORE_CONFIGURATION_MAP_ENABLEQUERYENGINE_TIP' ); ?>">
												<?php echo JText::_( 'CORE_CONFIGURATION_MAP_ENABLEQUERYENGINE_LABEL' ); ?>
											</span>
										</td>
										<td>
											<input type="checkbox"  id="enableQueryEngine" name="enableQueryEngine" value="1" <?php if($mapList['enableQueryEngine']->value == '1')echo " checked" ;?>  /> 
										</td>
									</tr>
									</tbody>
								</table>
							</fieldset>
									
							<fieldset class="adminform">
								<legend><?php echo JText::_( 'CORE_CONFIGURATION_SERVICEPUB_FIELDSET_TITLE' ); ?></legend>
								<table class="admintable" cellspacing="1">
									<tbody>
									<tr>
										<td valign="top" class="key">
											<span class="editlinktip hasTip" title="<?php echo JText::_( 'CORE_CONFIGURATION_MAP_PUBWMSURL_TIP' ); ?>">
												<?php echo JText::_( 'CORE_CONFIGURATION_MAP_PUBWMSURL_LABEL' ); ?>
											</span>
										</td>
										<td>
											<input class="text_area" type="text" size="100" name="pubWmsUrl" value="<?php echo $mapList['pubWmsUrl']->value; ?>" maxlength="<?php echo $fieldsLength['value'];?>" />
										</td>
									</tr>
									<tr>
										<td valign="top" class="key">
											<span class="editlinktip hasTip" title="<?php echo JText::_( 'CORE_CONFIGURATION_MAP_PUBWMSVERSION_TIP' ); ?>">
												<?php echo JText::_( 'CORE_CONFIGURATION_MAP_PUBWMSVERSION_LABEL' ); ?>
											</span>
										</td>
										<td>
											<select class="inputbox" name="pubWmsVersion">
											<option <?php if($mapList['pubWmsVersion']->value == '1.1.0') echo "selected" ; ?> value="1.1.0"><?php echo "1.1.0"; ?></option>
											<option <?php if($mapList['pubWmsVersion']->value == '1.1.1') echo "selected" ; ?> value="1.1.1"><?php echo "1.1.1"; ?></option>
											<option <?php if($mapList['pubWmsVersion']->value == '1.3.0') echo "selected" ; ?> value="1.3.0"><?php echo "1.3.0"; ?></option>
											</select>
										</td>
									</tr>
									<tr>
										<td valign="top" class="key">
											<span class="editlinktip hasTip" title="<?php echo JText::_( 'CORE_CONFIGURATION_MAP_WMSFILTERSUPPORT_TIP' ); ?>">
												<?php echo JText::_( 'CORE_CONFIGURATION_MAP_WMSFILTERSUPPORT_LABEL' ); ?>
											</span>
										</td>
										<td>
											<input type="checkbox"  id="WMSFilterSupport" name="WMSFilterSupport" value="1" <?php if($mapList['WMSFilterSupport']->value == '1')echo " checked" ;?>  /> 
										</td>
									</tr>
									<tr>
										<td valign="top" class="key">
											<span class="editlinktip hasTip" title="<?php echo JText::_( 'CORE_CONFIGURATION_MAP_PUBWFSURL_TIP' ); ?>">
												<?php echo JText::_( 'CORE_CONFIGURATION_MAP_PUBWFSURL_LABEL' ); ?>
											</span>
										</td>
										<td>
											<input class="text_area" type="text" size="100" name="pubWfsUrl" value="<?php echo $mapList['pubWfsUrl']->value; ?>" maxlength="<?php echo $fieldsLength['value'];?>" />
										</td>
									</tr>
									<tr>
										<td valign="top" class="key">
											<span class="editlinktip hasTip" title="<?php echo JText::_( 'CORE_CONFIGURATION_MAP_PUBWFSVERSION_TIP' ); ?>">
												<?php echo JText::_( 'CORE_CONFIGURATION_MAP_PUBWFSVERSION_LABEL' ); ?>
											</span>
										</td>
										<td>
											<select class="inputbox" name="pubWfsVersion">
											<option <?php if($mapList['pubWfsVersion']->value == '1.0.0') echo "selected" ; ?> value="1.0.0"><?php echo "1.0.0"; ?></option>
											</select>
										</td>
									</tr>
									<tr>
										<td valign="top" class="key">
											<span class="editlinktip hasTip" title="<?php echo JText::_( 'CORE_CONFIGURATION_MAP_MAXFEATURES_TIP' ); ?>">
												<?php echo JText::_( 'CORE_CONFIGURATION_MAP_MAXFEATURES_LABEL' ); ?>
											</span>
										</td>
										<td>
											<input class="text_area" type="text" size="100" name="maxFeatures" value="<?php echo $mapList['maxFeatures']->value; ?>" maxlength="<?php echo $fieldsLength['value'];?>" />
										</td>
									</tr>
									<tr>
										<td valign="top" class="key">
											<span class="editlinktip hasTip" title="<?php echo JText::_( 'CORE_CONFIGURATION_MAP_FEATIDATTRIBUTE_TIP' ); ?>">
												<?php echo JText::_( 'CORE_CONFIGURATION_MAP_FEATIDATTRIBUTE_LABEL' ); ?>
											</span>
										</td>
										<td>
											<input class="text_area" type="text" size="100" name="featureIdAttribute" value="<?php echo $mapList['featureIdAttribute']->value; ?>" maxlength="<?php echo $fieldsLength['value'];?>" />
										</td>
									</tr>
									<tr>
										<td valign="top" class="key">
											<span class="editlinktip hasTip" title="<?php echo JText::_( 'CORE_CONFIGURATION_MAP_PUBFEATURENS_TIP' ); ?>">
												<?php echo JText::_( 'CORE_CONFIGURATION_MAP_PUBFEATURENS_LABEL' ); ?>
											</span>
										</td>
										<td>
											<input class="text_area" type="text" size="100" name="pubFeatureNS" value="<?php echo $mapList['pubFeatureNS']->value; ?>" maxlength="<?php echo $fieldsLength['value'];?>" />
										</td>
									</tr>
									<tr>
										<td valign="top" class="key">
											<span class="editlinktip hasTip" title="<?php echo JText::_( 'CORE_CONFIGURATION_MAP_PUBFEATUREPREFIX_TIP' ); ?>">
												<?php echo JText::_( 'CORE_CONFIGURATION_MAP_PUBFEATUREPREFIX_LABEL' ); ?>
											</span>
										</td>
										<td>
											<input class="text_area" type="text" size="100" name="pubFeaturePrefix" value="<?php echo $mapList['pubFeaturePrefix']->value; ?>" maxlength="<?php echo $fieldsLength['value'];?>" />
										</td>
									</tr>
									
									</tbody>
									</table>
								</fieldset>
								<fieldset class="adminform">
								<legend><?php echo JText::_( 'CORE_CONFIGURATION_CARTO_FIELDSET_TITLE' ); ?></legend>
								<table class="admintable" cellspacing="1">
									<tbody>
									<tr>
										<td valign="top" class="key">
											<span class="editlinktip hasTip" title="<?php echo JText::_( 'CORE_CONFIGURATION_MAP_PROJECTION_TIP' ); ?>">
												<?php echo JText::_( 'CORE_CONFIGURATION_MAP_PROJECTION_LABEL' ); ?>
											</span>
										</td>
										<td>
											<input class="text_area" type="text" size="100" name="projection" value="<?php echo $mapList['projection']->value; ?>" maxlength="<?php echo $fieldsLength['value'];?>" />
										</td>
									</tr>
									<tr>
										<td valign="top" class="key">
											<span class="editlinktip hasTip" title="<?php echo JText::_( 'CORE_CONFIGURATION_MAP_UNIT_TIP' ); ?>">
												<?php echo JText::_( 'CORE_CONFIGURATION_MAP_UNIT_LABEL' ); ?>
											</span>
										<td>
											<select class="inputbox" name="mapUnit">
												<option <?php if($mapList['mapUnit']->value == 'm') echo "selected" ; ?> value="m"><?php echo JText::_("CORE_METERS"); ?></option>
												<option <?php if($mapList['mapUnit']->value == 'degrees') echo "selected" ; ?> value="degrees"><?php echo JText::_("CORE_DEGREES"); ?></option>
											</select
										></td>
									</tr>
									<tr>
										<td valign="top" class="key">
											<span class="editlinktip hasTip" title="<?php echo JText::_( 'CORE_CONFIGURATION_MAP_MAXEXTENT_TIP' ); ?>">
												<?php echo JText::_( 'CORE_CONFIGURATION_MAP_MAXEXTENT_LABEL' ); ?>
											</span>
										</td>
										<td>
											<input class="text_area" type="text" size="100" name="mapMaxExtent" value="<?php echo $mapList['mapMaxExtent']->value; ?>" maxlength="<?php echo $fieldsLength['value'];?>" />
										</td>
									</tr>
									<tr>
										<td valign="top" class="key">
											<span class="editlinktip hasTip" title="<?php echo JText::_( 'CORE_CONFIGURATION_MAP_NUMZOOMLEVELS_TIP' ); ?>">
												<?php echo JText::_( 'CORE_CONFIGURATION_MAP_NUMZOOMLEVELS_LABEL' ); ?>
											</span>
										</td>
										<td>
											<input class="text_area" type="text" size="100" name="numZoomLevels" value="<?php echo $mapList['numZoomLevels']->value; ?>" maxlength="<?php echo $fieldsLength['value'];?>" />
										</td>
									</tr>
									<tr>
										<td valign="top" class="key">
											<span class="editlinktip hasTip" title="<?php echo JText::_( 'CORE_CONFIGURATION_MAP_DFTLMAPZOOM_TIP' ); ?>">
												<?php echo JText::_( 'CORE_CONFIGURATION_MAP_DFTLMAPZOOM_LABEL' ); ?>
											</span>
										</td>
										<td>
											<input class="text_area" type="text" size="100" name="defaultCoordMapZoom" value="<?php echo $mapList['defaultCoordMapZoom']->value; ?>" maxlength="<?php echo $fieldsLength['value'];?>" />
										</td>
									</tr>
									<tr>
										<td valign="top"  colspan="2">
											<input type="radio" id="mapResolutionOverScale" name="mapResolutionOverScale" value="0" <?php if ($mapList['mapResolutionOverScale']->value == 0) echo "checked=\"checked\""; ?> /> <?php echo JText::_("CORE_CONFIGURATION_MAP_SCALES"); ?>
										</td>
									</tr>
									<tr>
										<td valign="top" class="key">
											<span class="editlinktip hasTip" title="<?php echo JText::_( 'CORE_CONFIGURATION_MAP_MINSCALE_TIP' ); ?>">
												<?php echo JText::_( 'CORE_CONFIGURATION_MAP_MINSCALE_LABEL' ); ?>
											</span>
										</td>
										<td>
											<input class="inputbox scales" type="text" size="100" name="mapMinScale" <?php if ($mapList['mapResolutionOverScale']->value == 1) echo 'disabled' ?> value="<?php echo $mapList['mapMinScale']->value; ?>" maxlength="<?php echo $fieldsLength['value'];?>" />
										</td>
									</tr>
									<tr>
										<td valign="top" class="key">
											<span class="editlinktip hasTip" title="<?php echo JText::_( 'CORE_CONFIGURATION_MAP_MAXSCALE_TIP' ); ?>">
												<?php echo JText::_( 'CORE_CONFIGURATION_MAP_MAXSCALE_LABEL' ); ?>
											</span>
										</td>
										<td>
											<input class="inputbox scales" type="text" size="100" name="mapMaxScale" <?php if ($mapList['mapResolutionOverScale']->value == 1) echo 'disabled' ?> value="<?php echo $mapList['mapMaxScale']->value; ?>" maxlength="<?php echo $fieldsLength['value'];?>" />
										</td>
									</tr>
									<tr>
										<td valign="top" colspan="2">
											<input type="radio" id="mapResolutionOverScale1" name="mapResolutionOverScale" value="1" <?php if ($mapList['mapResolutionOverScale']->value == 1) echo "checked=\"checked\""; ?> /> <?php echo JText::_("CORE_CONFIGURATION_MAP_RESOLUTIONS"); ?>
										</td>
									</tr>
									<tr>
										<td valign="top" class="key">
											<span class="editlinktip hasTip" title="<?php echo JText::_( 'CORE_CONFIGURATION_MAP_MINRESOLUTION_TIP' ); ?>">
												<?php echo JText::_( 'CORE_CONFIGURATION_MAP_MINRESOLUTION_LABEL' ); ?>
											</span>
										</td>
										<td>
											<input class="inputbox resolutions" type="text" size="100" name="mapMinResolution" <?php if ($mapList['mapResolutionOverScale']->value == 0) echo 'disabled' ?> value="<?php echo $mapList['mapMinResolution']->value; ?>" maxlength="<?php echo $fieldsLength['value'];?>" />
										</td>
									</tr>
									<tr>
										<td valign="top" class="key">
											<span class="editlinktip hasTip" title="<?php echo JText::_( 'CORE_CONFIGURATION_MAP_MAXRESOLUTION_TIP' ); ?>">
												<?php echo JText::_( 'CORE_CONFIGURATION_MAP_MAXRESOLUTION_LABEL' ); ?>
											</span>
										</td>
										<td>
											<input class="inputbox resolutions" type="text" size="100" name="mapMaxResolution" <?php if ($mapList['mapResolutionOverScale']->value == 0) echo 'disabled' ?> value="<?php echo $mapList['mapMaxResolution']->value; ?>" maxlength="<?php echo $fieldsLength['value'];?>" />
										</td>
									</tr>
									<tr>
										<td valign="top" class="key">
											<span class="editlinktip hasTip" title="<?php echo JText::_( 'CORE_CONFIGURATION_MAP_RESOLUTIONS_TIP' ); ?>">
												<?php echo JText::_( 'CORE_CONFIGURATION_MAP_RESOLUTIONS_LABEL' ); ?>
											</span>
										</td>
										<td>
											<textarea class="textarea resolutions" style="height: 200px; width: 500px;" id="mapResolutions" name="mapResolutions" <?php if ($mapList['mapResolutionOverScale']->value == 0) echo 'disabled' ?> >
											<?php echo $mapList['mapResolutions']->value; ?> 
											</textarea>
										</td>
									</tr>
									</tbody>
									</table>
								</fieldset>
								<fieldset class="adminform">
								<legend><?php echo JText::_( 'CORE_CONFIGURATION_SERVICEMAP_FIELDSET_TITLE' ); ?></legend>
								 
								<table class="admintable" cellspacing="1">
									<tbody>
									<tr>
										<td valign="top" class="key">
											<span class="editlinktip hasTip" title="<?php echo JText::_( 'CORE_CONFIGURATION_MAP_WPSREPORTURL_TIP' ); ?>">
												<?php echo JText::_( 'CORE_CONFIGURATION_MAP_WPSREPORTURL_LABEL' ); ?>
											</span>
										</td>
										<td>
											<input class="text_area" type="text" size="100" name="wpsReportsUrl" value="<?php echo $mapList['wpsReportsUrl']->value; ?>" maxlength="<?php echo $fieldsLength['value'];?>" />
										</td>
									</tr>
									<tr>
										<td valign="top" class="key">
											<span class="editlinktip hasTip" title="<?php echo JText::_( 'CORE_CONFIGURATION_MAP_SHP2GML_TIP' ); ?>">
												<?php echo JText::_( 'CORE_CONFIGURATION_MAP_SHP2GML_LABEL' ); ?>
											</span>
										</td>
										<td>
											<input class="text_area" type="text" size="100" name="shp2GmlUrl" value="<?php echo $mapList['shp2GmlUrl']->value; ?>" maxlength="<?php echo $fieldsLength['value'];?>" />
										</td>
									</tr>
									<tr>
										<td valign="top" class="key">
											<span class="editlinktip hasTip" title="<?php echo JText::_( 'CORE_CONFIGURATION_MAP_MAPTOFOPURL_TIP' ); ?>">
												<?php echo JText::_( 'CORE_CONFIGURATION_MAP_MAPTOFOPURL_LABEL' ); ?>
											</span>
										</td>
										<td>
											<input class="text_area" type="text" size="100" name="maptofopURL" value="<?php echo $mapList['maptofopURL']->value; ?>" maxlength="<?php echo $fieldsLength['value'];?>" />
										</td>
									</tr>
									</tbody>
									</table>
								</fieldset>
								
								<fieldset class="adminform">
								<legend><?php echo JText::_( 'CORE_CONFIGURATION_DISPLAYMAP_FIELDSET_TITLE' ); ?></legend>
								 
								<table class="admintable" cellspacing="1">
									<tbody>
									<tr>
										<td valign="top" class="key">
											<span class="editlinktip hasTip" title="<?php echo JText::_( 'CORE_CONFIGURATION_MAP_MAXSEARCHBARS_TIP' ); ?>">
												<?php echo JText::_( 'CORE_CONFIGURATION_MAP_MAXSEARCHBARS_LABEL' ); ?>
											</span>
										</td>
										<td>
											<input class="text_area" type="text" size="100" name="maxSearchBars" value="<?php echo $mapList['maxSearchBars']->value; ?>" maxlength="<?php echo $fieldsLength['value'];?>" />
										</td>
									</tr>
									<tr>
										<td valign="top" class="key">
											<span class="editlinktip hasTip" title="<?php echo JText::_( 'CORE_CONFIGURATION_MAP_AUTOCOMPLCHAR_TIP' ); ?>">
												<?php echo JText::_( 'CORE_CONFIGURATION_MAP_AUTOCOMPLCHAR_LABEL' ); ?>
											</span>
										</td>
										<td>
											<input class="text_area" type="text" size="100" name="autocompleteNumChars" value="<?php echo $mapList['autocompleteNumChars']->value; ?>" maxlength="<?php echo $fieldsLength['value'];?>" />
										</td>
									</tr>
									<tr>
										<td valign="top" class="key">
											<span class="editlinktip hasTip" title="<?php echo JText::_( 'CORE_CONFIGURATION_MAP_AUTOCOMPLFID_TIP' ); ?>">
												<?php echo JText::_( 'CORE_CONFIGURATION_MAP_AUTOCOMPLFID_LABEL' ); ?>
											</span>
										</td>
										<td>
											<input type="checkbox"  id="autocompleteUseFID" name="autocompleteUseFID" value="1" <?php if($mapList['autocompleteUseFID']->value == '1')echo " checked" ;?>  /> 
										</td>
									</tr>
									<tr>
										<td valign="top" class="key">
											<span class="editlinktip hasTip" title="<?php echo JText::_( 'CORE_CONFIGURATION_MAP_AUTOCOMPLMAXF_TIP' ); ?>">
												<?php echo JText::_( 'CORE_CONFIGURATION_MAP_AUTOCOMPLMAXF_LABEL' ); ?>
											</span>
										</td>
										<td>
											<input class="text_area" type="text" size="100" name="autocompleteMaxFeat" value="<?php echo $mapList['autocompleteMaxFeat']->value; ?>" maxlength="<?php echo $fieldsLength['value'];?>" />
										</td>
									</tr>
									<tr>
										<td valign="top" class="key">
											<span class="editlinktip hasTip" title="<?php echo JText::_( 'CORE_CONFIGURATION_MAP_LOCATIONWIDTH_TIP' ); ?>">
												<?php echo JText::_( 'CORE_CONFIGURATION_MAP_LOCATIONWIDTH_LABEL' ); ?>
											</span>
										</td>
										<td>
											<input class="text_area" type="text" size="100" name="localisationInputWidth" value="<?php echo $mapList['localisationInputWidth']->value; ?>" maxlength="<?php echo $fieldsLength['value'];?>" />
										</td>
									</tr>
									<tr>
										<td valign="top" class="key">
											<span class="editlinktip hasTip" title="<?php echo JText::_( 'CORE_CONFIGURATION_MAP_LEGENDWIDTH_TIP' ); ?>">
												<?php echo JText::_( 'CORE_CONFIGURATION_MAP_LEGENDWIDTH_LABEL' ); ?>
											</span>
										</td>
										<td>
											<input class="text_area" type="text" size="100" name="legendOrFilterPanelWidth" value="<?php echo $mapList['legendOrFilterPanelWidth']->value; ?>" maxlength="<?php echo $fieldsLength['value'];?>" />
										</td>
									</tr>
									<tr>
										<td valign="top" class="key">
											<span class="editlinktip hasTip" title="<?php echo JText::_( 'CORE_CONFIGURATION_MAP_TREEWIDTH_TIP' ); ?>">
												<?php echo JText::_( 'CORE_CONFIGURATION_MAP_TREEWIDTH_LABEL' ); ?>
											</span>
										</td>
										<td>
											<input class="text_area" type="text" size="100" name="treePanelWidth" value="<?php echo $mapList['treePanelWidth']->value; ?>" maxlength="<?php echo $fieldsLength['value'];?>" />
										</td>
									</tr>
									</tbody>
								</table>
							</fieldset>	
							
						
							
						</td>
					</tr>
				</table>
			</div>
		
<?php
}
?>
		</div>
		<div class="clr"></div>

		<input type="hidden" name="c" value="global" />
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="catalog_item" value="<?php echo $catalogItem; ?>" />
		<input type="hidden" name="shop_item" value="<?php echo $shopItem; ?>" />
		<input type="hidden" name="proxy_item" value="<?php echo $proxyItem; ?>" />
		<input type="hidden" name="monitor_item" value="<?php echo $monitorItem; ?>" />
		<input type="hidden" name="publish_item" value="<?php echo $publishItem; ?>" />
		<input type="hidden" name="map_item" value="<?php echo $mapItem; ?>" />
		<?php echo JHTML::_( 'form.token' ); ?>
		</form>
		<?php
	}
}
	
?>
