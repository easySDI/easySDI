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
package eu.bauel.publish.transformation;

import java.io.IOException;
import java.util.List;

import eu.bauel.publish.exception.DataSourceNotFoundException;
import eu.bauel.publish.exception.DataSourceWrongFormatException;
import eu.bauel.publish.exception.DiffuserException;
import eu.bauel.publish.exception.DiffuserNotFoundException;
import eu.bauel.publish.exception.FeatureSourceException;
import eu.bauel.publish.exception.IncompatibleUpdateFeatureSourceException;
import eu.bauel.publish.exception.PublicationException;
import eu.bauel.publish.exception.PublishConfigurationException;
import eu.bauel.publish.exception.PublishGeneralException;
import eu.bauel.publish.exception.ScriptNotFoundException;
import eu.bauel.publish.exception.TransformationException;

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
	public void transformDataset( String postgisOutputTableName, String sourceFileDir, List<String> URLs, String dbhost, String dbport, String dbname,
			String dbusername, String dbpassword, String dbschema, String epsgProj, String dataset) throws IOException, DataSourceWrongFormatException, PublishConfigurationException, DataSourceNotFoundException, TransformationException;

	/**
	 * 
	 * @param path: the real path from where this plugin is being loaded
	 * @return
	 */
	public void setLocation( String location );
	
	public float getProgress();
	
	public void setProgress(float progress);
}
