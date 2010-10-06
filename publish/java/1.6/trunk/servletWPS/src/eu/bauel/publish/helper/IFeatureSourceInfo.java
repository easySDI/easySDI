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
package eu.bauel.publish.helper;

import java.util.HashMap;
import java.util.List;
import java.util.Map;

import eu.bauel.publish.persistence.Geodatabase;

/*
 * This interface should be implemented by a class that is specific to a 
 * spatial geodatabase. It gives feedback about a geodata set.
 */
public interface IFeatureSourceInfo{
	public List<Attribute> getAtrList();

	public String getCrsCode();

	public HashMap<String, Double> getBbox();

	public String getGeometry();

	public String getTable();

	public String getCrsWkt();
}
