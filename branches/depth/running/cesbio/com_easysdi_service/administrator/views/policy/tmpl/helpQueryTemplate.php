<?php
?>
		<h2>
		<?php echo JText::_(  'EASYSDI_HELP_TEMPLATE_TITLE' ); ?>
		</h2>
		<h3>
		<?php if(JRequest::getVar('filter_type','') == "filterQuery")
		{
			echo JText::_(  'EASYSDI_HELP_TEMPLATE_FILTER_TYPE_QUERY' ); 
		}
		else
		{
			echo JText::_(  'EASYSDI_HELP_TEMPLATE_FILTER_TYPE_ANSWER' ); 
		}
		?>
		</h3>
		
		<?php if(JRequest::getVar('filter_type','') == "filterQuery")
		{
		?>
		<textarea ROWS="8" COLS="75"><Filter xmlns:gml='http://www.opengis.net/gml'>
  <BBOX>
    <PropertyName>geometryName</PropertyName>
      <Box srsName=\"EPSG:4326\" gml=\"http://www.opengis.net/gml\">
        <coordinates>-180,-90 180,90</coordinates>
      </Box>
  </BBOX>
</Filter></textarea>
		<?php
		}
		else
		{ 
		?>
		<textarea ROWS="13" COLS="75"><Filter xmlns:gml='http://www.opengis.net/gml'>
  <Within>
    <PropertyName>geometryName</PropertyName>
      <gml:Polygon xmlns:gml='http://www.opengis.net/gml' srsName='EPSG:4326'>
      <gml:outerBoundaryIs>
        <gml:LinearRing>
          <gml:coordinates>-180,-90 -180,90 180,90 180,-90 -180,-90
          </gml:coordinates>
        </gml:LinearRing>
      </gml:outerBoundaryIs>
    </gml:Polygon>
  </Within>
</Filter></textarea>
		<?php
		} 
		if(JRequest::getVar('filter_type','') == "filterQuery")
		{
			?>
			<p>
			<?php 
			echo JText::_(  'EASYSDI_HELP_TEMPLATE_FILTER_TYPE_QUERY_REM' );
			?>
			</p>
			<?php
		}