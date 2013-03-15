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
public class OWSTelephone implements Serializable{
	
	private static final long serialVersionUID = -1522579526391545420L;
	
	private String voicePhone = null;
	private String facSimile = null;
	private boolean isEmpty = true;
	
	public boolean isEmpty (){
		return isEmpty;
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

}
