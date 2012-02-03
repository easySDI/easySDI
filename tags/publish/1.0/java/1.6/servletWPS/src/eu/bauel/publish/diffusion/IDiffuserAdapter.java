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
package eu.bauel.publish.diffusion;

import java.util.List;

import eu.bauel.publish.exception.DiffuserException;
import eu.bauel.publish.exception.FeatureSourceException;
import eu.bauel.publish.exception.PublicationException;
import eu.bauel.publish.exception.PublishConfigurationException;
import eu.bauel.publish.exception.PublishGeneralException;
import eu.bauel.publish.persistence.*;

public interface IDiffuserAdapter {

	boolean removeLayer( Diffuser diff, Layer layer ) throws PublishGeneralException, DiffuserException, PublicationException, PublishConfigurationException;
	
	void setCredentials( String username, String passwd );
	
	PublishLayerResponse publishLayer(String layerId, String featureTypeId,
			List<String> attributeAlias, String title, String name, String qualityArea,
			String keywordList, String abstr, String geometry) throws PublishGeneralException, FeatureSourceException, DiffuserException, PublicationException, PublishConfigurationException;
}
