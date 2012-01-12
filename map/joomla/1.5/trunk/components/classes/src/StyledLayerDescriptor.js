/**
 * EasySDI, a solution to implement easily any spatial data infrastructure
 * Copyright (C) EasySDI Community * For more information : www.easysdi.org
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
 
 Ext.namespace("EasySDI_Map");
 
 EasySDI_Map.StyledLayerDescriptor = function()
 {
 };
 
 EasySDI_Map.StyledLayerDescriptor.getSLDHeader = function()
 {
 	return String.format (
	'<StyledLayerDescriptor version="1.0.0" xmlns="http://www.opengis.net/sld" xmlns:ogc="http://www.opengis.net/ogc" '+
    '  xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"'+
    '  xsi:schemaLocation="http://www.opengis.net/sld http://schemas.opengis.net/sld/1.0.0/StyledLayerDescriptor.xsd">'
	);
 };
 
EasySDI_Map.StyledLayerDescriptor.getSLDFooter = function ()
{
	return String.format (
	'</StyledLayerDescriptor>'
	);
};
	
/**
  * Build an SLD string from the fill, stroke and opacity.
  */
 EasySDI_Map.StyledLayerDescriptor.getSLD = function(layer, fillColor, opacity, strokeColor) {
   return String.format(
     '<NamedLayer>'+
     '<Name>{0}</Name>'+
     '<UserStyle>'+
     '<FeatureTypeStyle>'+
     '<Rule>'+
     '<Title>Polygon</Title>'+
     '<PolygonSymbolizer>'+
     '<Fill>'+
     '<CssParameter name="fill">#{1}</CssParameter>'+
     '<CssParameter name="fill-opacity">{2}</CssParameter>'+
     '</Fill>'+
     '<Stroke>'+
     '<CssParameter name="stroke">#{3}</CssParameter>'+
     '</Stroke>'+
     '</PolygonSymbolizer>'+
     '</Rule>'+
     '</FeatureTypeStyle>'+
     '</UserStyle>'+
     '</NamedLayer>',
     layer, fillColor, opacity, strokeColor);
 };
 
  EasySDI_Map.StyledLayerDescriptor.getDefaultSLD = function() {
   return String.format(
     '<NamedLayer>'+
     '<Name>undefined</Name>'+
     '<UserStyle>'+
     '<FeatureTypeStyle>'+
     '<Rule>'+
     '<Title>Polygon</Title>'+
     '<PolygonSymbolizer>'+
     '<Fill />'+
     '<Stroke />'+
     '</PolygonSymbolizer>'+
     '</Rule>'+
     '</FeatureTypeStyle>'+
     '</UserStyle>'+
     '</NamedLayer>');
 };