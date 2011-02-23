/**
 * EasySDI, a solution to implement easily any spatial data infrastructure
 * Copyright (C) 2008 Remy Baud (remy.baud@asitvd.ch), Antoine Elbel (antoine@probel.eu)
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
package org.easysdi.publish.diffusion;

import java.util.List;

import org.easysdi.publish.biz.diffuser.Diffuser;
import org.easysdi.publish.biz.layer.Layer;
import org.easysdi.publish.exception.DiffuserException;
import org.easysdi.publish.exception.FeatureSourceException;
import org.easysdi.publish.exception.PublicationException;
import org.easysdi.publish.exception.PublishConfigurationException;
import org.easysdi.publish.exception.PublishGeneralException;


public class MockDiffuserControllerAdaptor extends CommonDiffuserControllerAdapter {

	@Override
	
	// Todo change to match the new api:
	public PublishLayerResponse publishLayer(String layerId, String featureTypeId,
			List<String> attributeAlias, String title, String name, String qualityArea,
			String keywordList, String abstr, String geometry)throws PublishGeneralException, FeatureSourceException, DiffuserException, PublicationException, PublishConfigurationException{
	//public PublishLayerResponse publishLayer(Diffuser diff,  Layer layer) {

		PublishLayerResponse resp = new PublishLayerResponse();
		
		resp.endPointsTypes.add("WMS_URL");
		resp.endPoints.add("http://sigma.openplans.org/geoserver/wms/layers=topp:states");
		resp.endPointsTypes.add("WFS_URL");
		resp.endPoints.add("http://sigma.openplans.org/geoserver/wfs/layers=topp:states");
		resp.endPointsTypes.add("KML_URL");
		resp.endPoints.add("http://sigma.openplans.org/geoserver/wms/kml?layers=topp:states");
		 //WPSResponseFiller("PDF_URL", "http://sigma.openplans.org/geoserver/wms?bbox=-134.57670484741593,-1.5622183474159284,-57.12456615258405,75.88992034741594&styles=&Format=application/pdf&request=GetMap&version=1.1.1&layers=topp:states&width=800&height=750&srs=EPSG:4326")+

		return resp;
	} 
	
	@Override
	public boolean removeLayer(Diffuser diff, Layer layer) throws PublishGeneralException, DiffuserException, PublicationException, PublishConfigurationException {
		super.removeLayer( diff, layer );
		return true;
	}

}
