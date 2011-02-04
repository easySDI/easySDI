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

public class OWSServiceProvider implements Serializable{
	
	private static final long serialVersionUID = -1522579526391545420L;
	private String name = null;
	private String linkage = null;
	private OWSResponsibleParty responsible = null;
	private boolean isEmpty = true;
	
	public boolean isEmpty (){
		return isEmpty;
	}
	
	public void setResponsibleParty(OWSResponsibleParty responsibleParty) {
		if(responsibleParty!=null && !"".equals(responsibleParty))
		{
			isEmpty = false;
			this.responsible = responsibleParty;
		}
	}

	public OWSResponsibleParty getResponsibleParty() {
		return this.responsible;
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
	

	public void setLinkage(String linkage) {
		if(linkage != null && !"".equals(linkage))
		{
			isEmpty = false;
			this.linkage = linkage;
		}
	}

	public String getLinkage() {
		return linkage;
	}
	

}

