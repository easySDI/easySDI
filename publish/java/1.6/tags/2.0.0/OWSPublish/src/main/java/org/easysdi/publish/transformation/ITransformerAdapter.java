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
package org.easysdi.publish.transformation;

import java.io.IOException;
import java.util.List;

import org.deegree.services.wps.ProcessletExecutionInfo;
import org.easysdi.publish.exception.DataSourceNotFoundException;
import org.easysdi.publish.exception.DataSourceWrongFormatException;
import org.easysdi.publish.exception.DiffuserException;
import org.easysdi.publish.exception.DiffuserNotFoundException;
import org.easysdi.publish.exception.FeatureSourceException;
import org.easysdi.publish.exception.IncompatibleUpdateFeatureSourceException;
import org.easysdi.publish.exception.PublicationException;
import org.easysdi.publish.exception.PublishConfigurationException;
import org.easysdi.publish.exception.PublishGeneralException;
import org.easysdi.publish.exception.ScriptNotFoundException;
import org.easysdi.publish.exception.TransformationException;


public interface ITransformerAdapter {
	
	/**
	 * 
	 * @param featureSourceId
	 * @param diffusorName
	 * @param URLs
	 * @param ScriptName
	 * @param epsgProj
	 * @return
	 * @throws DataSourceNotFoundException
	 * @throws DiffuserNotFoundException
	 * @throws ScriptNotFoundException
	 * @throws TransformationException
	 * @throws DataSourceWrongFormatException
	 * @throws PublishConfigurationException
	 * @throws IncompatibleUpdateFeatureSourceException 
	 */
	public void transformDataset( ProcessletExecutionInfo info, String postgisOutputTableName, String sourceFileDir, List<String> URLs, String dbhost, String dbport, String dbname,
			String dbusername, String dbpassword, String dbschema, String epsgProj, String dataset) throws IOException, DataSourceWrongFormatException, PublishConfigurationException, DataSourceNotFoundException, TransformationException;

	/**
	 * 
	 * @param path: the real path from where this plugin is being loaded
	 * @return
	 */
	public void setLocation( String location );
	
	public float getProgress();
	
	public void setProgress(Float progress);
}
