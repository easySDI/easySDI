/**
 * EasySDI, a solution to implement easily any spatial data infrastructure
 * Copyright (C) 2009 Antoine Elbel & Remy Baud (aelbel@solnet.ch remy.baud@asitvd.ch)
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

import java.io.IOException;
import java.util.List;
import java.util.logging.Logger;

import org.easysdi.publish.biz.diffuser.Diffuser;
import org.easysdi.publish.biz.layer.Layer;
import org.easysdi.publish.exception.DiffuserException;
import org.easysdi.publish.exception.FeatureSourceException;
import org.easysdi.publish.exception.FeatureSourceNotFoundException;
import org.easysdi.publish.exception.PublicationException;
import org.easysdi.publish.exception.PublishConfigurationException;
import org.easysdi.publish.exception.PublishGeneralException;


public abstract class CommonDiffuserControllerAdapter implements IDiffuserAdapter {
	
	Logger logger = Logger.getLogger("org.easysdi.publish.diffusion.CommonDiffusorControllerAdaptor");

	public CommonDiffuserControllerAdapter(){
		logger.info("Dedans constructeur CommonDiffusorControllerAdaptor ");
	}
	
	@Override
	public PublishLayerResponse publishLayer(String layerId, String featureSourceId,
			List<String> attributeAlias, String title, String name, String qualityArea,
			String keywordList, String abstract1, String geometry) throws PublishGeneralException, FeatureSourceException, DiffuserException, PublicationException, PublishConfigurationException
	{
		logger.info("Dedans CommonDiffusorControllerAdapter publishLayer");
		return null;
	}

	@Override
	public boolean removeLayer(Diffuser diff, Layer layer) throws PublishGeneralException, DiffuserException, PublicationException, PublishConfigurationException {
		logger.info( diff.getUrl() +  " layerName: " + layer.getName() );
		return true;
	}
	
	public Logger getLogger()
	{
		return logger;
	}

	protected String username;
	protected String passwd;

	@Override
	public void setCredentials(String username, String passwd)
	{
		this.username = username;
		this.passwd = passwd;
	}


}
