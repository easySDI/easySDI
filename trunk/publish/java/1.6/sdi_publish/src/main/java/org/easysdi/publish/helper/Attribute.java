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
package org.easysdi.publish.helper;

/*
 * This class describes the attributes of a geo dataset.
 */
public class Attribute
{
	protected String name;
	public String getName() {
		return name;
	}
	public Class getType() {
		return type;
	}
	public String getTypeName() {
		return typeName;
	}
	public String getTypeAsText() {
		return typeAsText;
	}
	public Object getValue() {
		return value;
	}
	public Boolean isGeometry() {
		return isGeometry;
	}
	
	protected Class type;
	protected String typeAsText;
	protected String typeName;
	protected Object value;
	protected Boolean isGeometry;
	
	public Attribute(String name, Class type, String typeName){
		this.name = name;
		this.type =type;
		this.isGeometry = false;
		this.typeName = typeName;
	}
	public Attribute(String name, String typeAsText, String typeName){
		this.name = name;
		this.typeAsText = typeAsText;
		this.isGeometry = false;
		this.typeName = typeName;
	}
	public Attribute(String name, String typeAsText, String typeName, Boolean isGeometry){
		this.name = name;
		this.typeAsText = typeAsText;
		this.isGeometry = false;
		this.typeName = typeName;
		this.isGeometry=isGeometry;
	}
	public Attribute(String name, Class type, String typeName, Boolean isGeometry){
		this.name = name;
		this.type =type;
		this.typeName = typeName;
		this.isGeometry=isGeometry;
	}
	
}