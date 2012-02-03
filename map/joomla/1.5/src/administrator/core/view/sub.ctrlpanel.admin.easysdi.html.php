<tr>
	<td>
		<table class="header_sdi_table" width="100%">
			<tbody>
				<tr>
					<td class="header_sdi_comp_title">
						<div class="header_sdi icon-48-map"><?php echo JText::_( 'CORE_CPANEL_MAP_LABEL' );?></div>
					</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td colspan="2">
						<table width="100%">
							<tr>
								<td class="header_sdi_comp_links">&nbsp;</td>
								<td class="header_sdi_list">
									<ul>
										<li><?php printf('<a href="index.php?option=%s&amp;task=profile">', 'com_easysdi_map'); echo JText::_( 'CORE_CPANEL_MAP_RIGHT_LABEL' ); ?> </li>
										<li><?php printf('<a href="index.php?option=%s&amp;task=display">', 'com_easysdi_map'); echo JText::_( 'CORE_CPANEL_MAP_DISPLAY_LABEL' ); ?> </li>
										<li><?php printf('<a href="index.php?option=%s&amp;task=annotationStyle">', 'com_easysdi_map'); echo JText::_( 'CORE_CPANEL_MAP_ANNOTATION_LABEL' ); ?> </li>
										<li><?php printf('<a href="index.php?option=%s&amp;task=editResource&filename='.JPATH_ROOT.DS.'components'.DS.'com_easysdi_map'.DS.'resource'.DS.'xslt'.DS.'getFeatureInfo.xslt'.'">', 'com_easysdi_map'); echo JText::_( 'CORE_CPANEL_MAP_RESOURCE_XSL_LABEL' ); ?> </li>
										<li><?php printf('<a href="index.php?option=%s&amp;task=editResource&filename='.JPATH_ROOT.DS.'components'.DS.'com_easysdi_map'.DS.'resource'.DS.'xslt'.DS.'getFeatureInfo.css'.'">', 'com_easysdi_map'); echo JText::_( 'CORE_CPANEL_MAP_RESOURCE_CSS_LABEL' ); ?> </li>
									</ul>
								</td>
								<td class="header_sdi_list">
									<ul >
										<li><?php printf('<a href="index.php?option=%s&amp;task=projection">', 'com_easysdi_map'); echo JText::_( 'CORE_CPANEL_MAP_PROJECTION_LABEL' ); ?> </li>
										 <li><?php printf('<a href="index.php?option=%s&amp;task=baseLayer">', 'com_easysdi_map'); echo JText::_( 'CORE_CPANEL_MAP_BASELAYER' ); ?></li>
							             <li><?php printf('<a href="index.php?option=%s&amp;task=overlayGroup">', 'com_easysdi_map'); echo JText::_( 'CORE_CPANEL_MAP_OVERLAY_GROUP' ); ?></li>
							             <li><?php printf('<a href="index.php?option=%s&amp;task=overlay">', 'com_easysdi_map'); echo JText::_( 'CORE_CPANEL_MAP_OVERLAY_DEFINITION' ); ?></li>
							             <li><?php printf('<a href="index.php?option=%s&amp;task=geolocation">', 'com_easysdi_map'); echo JText::_( 'CORE_CPANEL_MAP_GEOLOCATION' ); ?></li>
									</ul>
								</td>
<!--							</tr>-->
<!--						</table>-->
<!--					</td>-->
					<?php 
					$db =& JFactory::getDBO(); 
					$query ="SELECT value FROM #__sdi_configuration WHERE code ='enableQueryEngine' ";
					$db->setQuery( $query );
					$value = $db->loadResult();
					
					if ($value == '1')
					{
					?>
<!--					<td colspan="2">-->
<!--						<table width="100%">-->
<!--							<tr>-->
<!--								<td class="header_sdi_comp_links">&nbsp;</td>-->
								<td class="header_sdi_list">
									<ul >
										<li><?php printf('<a href="index.php?option=%s&amp;task=featureType">', 'com_easysdi_map'); echo JText::_( 'CORE_CPANEL_MAP_FEATURETYPE_LABEL' ); ?> </li>
										<li><?php printf('<a href="index.php?option=%s&amp;task=comment">', 'com_easysdi_map'); echo JText::_( 'CORE_CPANEL_MAP_COMMENT_LABEL' ); ?> </li>
										<li><?php printf('<a href="index.php?option=%s&amp;task=simplesearchCtrlPanel">', 'com_easysdi_map'); echo JText::_( 'CORE_CPANEL_MAP_SIMPLESEARCH_LABEL' ); ?> </li>
										<li><?php printf('<a href="index.php?option=%s&amp;task=searchLayer">', 'com_easysdi_map'); echo JText::_( 'CORE_CPANEL_MAP_SEARCHLAYER_LABEL' ); ?> </li>
										<li><?php printf('<a href="index.php?option=%s&amp;task=precision">', 'com_easysdi_map'); echo JText::_( 'CORE_CPANEL_MAP_PRECISION_LABEL' ); ?> </li>
										<li><?php printf('<a href="index.php?option=%s&amp;task=resultGrid">', 'com_easysdi_map'); echo JText::_( 'CORE_CPANEL_MAP_RESULTGRID_LABEL' ); ?> </li>
									</ul>
								</td>
<!--							</tr>-->
<!--						</table>-->
<!--					</td>-->
					<?php 
					}
					else {
					?>
					<td>&nbsp;</td>
					<?php 
					}
					?>
							</tr>
						</table>
					</td>
				</tr>
			</tbody>
		</table>
	</td>
</tr>

