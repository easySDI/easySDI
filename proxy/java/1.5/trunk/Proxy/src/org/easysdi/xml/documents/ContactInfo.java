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

public class ContactInfo implements Serializable{
	
	private static final long serialVersionUID = -1522579526391545420L;

	private String linkage = null;
	private String hoursofSservice = null;
	private String instructions = null;
	private boolean isEmpty = true;
	private ServiceContactPhone contactPhone = null;
	private ServiceContactAdress contactAdress = null;
	
	public boolean isEmpty (){
		return isEmpty;
	}
	
	public void setContactPhone(ServiceContactPhone contactPhone) {
		if(contactPhone!=null && !"".equals(contactPhone))
		{
			isEmpty = false;
			this.contactPhone = contactPhone;
		}
	}


	public ServiceContactPhone getContactPhone() {
		return contactPhone;
	}

	public void setContactAdress(ServiceContactAdress contactAdress) {
		if(contactAdress!=null && !"".equals(contactAdress))
		{
			isEmpty = false;
			this.contactAdress = contactAdress;
		}
	}

	public ServiceContactAdress getContactAdress() {
		return this.contactAdress;
	}

	
	
	/**
	 * @param linkage the linkage to set
	 */
	public void setLinkage(String linkage) {
		if(linkage!=null && !"".equals(linkage))
		{
			isEmpty = false;
			this.linkage = linkage;
		}
	}
	/**
	 * @return the linkage
	 */
	public String getLinkage() {
		return linkage;
	}
	/**
	 * @param hoursofSservice the hoursofSservice to set
	 */
	public void setHoursofSservice(String hoursofSservice) {
		if(hoursofSservice!= null &&!"".equals(hoursofSservice))
		{
			isEmpty = false;
			this.hoursofSservice = hoursofSservice;
		}
	}
	/**
	 * @return the hoursofSservice
	 */
	public String getHoursofSservice() {
		return hoursofSservice;
	}
	/**
	 * @param instructions the instructions to set
	 */
	public void setInstructions(String instructions) {
		if(instructions!= null && !"".equals(instructions))
		{
			isEmpty = false;
			this.instructions = instructions;
		}
	}
	/**
	 * @return the instructions
	 */
	public String getInstructions() {
		return instructions;
	}


}

