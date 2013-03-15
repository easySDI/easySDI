/**
 * EasySDI, a solution to implement easily any spatial data infrastructure
 * Copyright (C) EasySDI Community
 * For more information : www.easysdi.org
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
package org.easysdi.xml.documents;

import java.io.Serializable;

@Deprecated
public class OWSResponsibleParty implements Serializable{
	
	private static final long serialVersionUID = -1522579526391545420L;
	private String name = null;
	private String position = null;
	private String role = null;
	private OWSContact contact = null;
	private boolean isEmpty = true;
	
	public boolean isEmpty (){
		return isEmpty;
	}
	
	public void setContactInfo(OWSContact contact) {
		if(contact!=null && !"".equals(contact))
		{
			isEmpty = false;
			this.contact = contact;
		}
	}

	public OWSContact getContactInfo() {
		return this.contact;
	}
	
	/**
	 * @param name the name to set
	 */
	public void setName(String name) {
		if(name != null && !"".equals(name))
		{
			isEmpty = false;
			this.name = name;
		}
	}
	/**
	 * @return the name
	 */
	public String getName() {
		return name;
	}
	
	/**
	 * @param position the position to set
	 */
	public void setPosition(String position) {
		if(position != null && !"".equals(position))
		{
			isEmpty = false;
			this.position = position;
		}
	}
	/**
	 * @return the position
	 */
	public String getPosition() {
		return position;
	}

	public void setRole(String role) {
		if(role != null && !"".equals(role))
		{
			isEmpty = false;
			this.role = role;
		}
	}

	public String getRole() {
		return role;
	}
	

}

