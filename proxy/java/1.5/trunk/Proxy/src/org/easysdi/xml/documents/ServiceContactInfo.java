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

public class ServiceContactInfo implements Serializable{
	
	private static final long serialVersionUID = -1522579526391545420L;
	private String name = null;
	private String organization = null;
	private String site = null;
	private String position = null;
	private ServiceContactAdressInfo adress;
	private String voicePhone = null;
	private String facSimile = null;
	private String eMail = null;
	private String linkage = null;
	private String hoursofSservice = null;
	private String instructions = null;
	private boolean isEmpty = true;
	
	public boolean isEmpty (){
		return isEmpty;
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
	 * @param organization the organization to set
	 */
	public void setOrganization(String organization) {
		if(organization!= null && !"".equals(organization))
		{
			isEmpty = false;
			this.organization = organization;
		}
		
	}
	/**
	 * @return the organization
	 */
	public String getOrganization() {
		return organization;
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
	/**
	 * @param adress the adress to set
	 */
	public void setContactAddress(ServiceContactAdressInfo adress) {
		if(adress != null && !"".equals(adress))
		{
			isEmpty = false;
			this.adress = adress;
		}
		
	}
	/**
	 * @return the adress
	 */
	public ServiceContactAdressInfo getContactAddress() {
		return adress;
	}
	/**
	 * @param voicePhone the voicePhone to set
	 */
	public void setVoicePhone(String voicePhone) {
		if(voicePhone != null && !"".equals(voicePhone))
		{
			isEmpty = false;
			this.voicePhone = voicePhone;
		}
	}
	/**
	 * @return the voicePhone
	 */
	public String getVoicePhone() {
		return voicePhone;
	}
	/**
	 * @param facSimile the facSimile to set
	 */
	public void setFacSimile(String facSimile) {
		if(facSimile!= null && !"".equals(facSimile))
		{
			isEmpty = false;
			this.facSimile = facSimile;
		}
	}
	/**
	 * @return the facSimile
	 */
	public String getFacSimile() {
		return facSimile;
	}
	/**
	 * @param eMail the eMail to set
	 */
	public void seteMail(String eMail) {
		if(eMail!= null && !"".equals(eMail))
		{
			isEmpty = false;
			this.eMail = eMail;
		}
	}
	/**
	 * @return the eMail
	 */
	public String geteMail() {
		return eMail;
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
//	public void setSite(String site) {
//		this.site = site;
//	}
//	public String getSite() {
//		return site;
//	}

}

