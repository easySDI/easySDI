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

public class OWSServiceMetadata implements Serializable{
	
	private static final long serialVersionUID = -1522579526391545420L;
	
	private OWSServiceIdentification identification = null;
	private OWSServiceProvider provider =null;
	
	private boolean isEmpty = true;
	
	public boolean isEmpty (){
		return isEmpty;
	}
	
	
	/**
	 * @param provider the provider to set
	 */
	public void setProvider(OWSServiceProvider provider) {
		if(provider != null && !provider.isEmpty())
		{
			isEmpty = false;
			this.provider = provider;
		}
	}

	/**
	 * @return the provider
	 */
	public OWSServiceProvider getProvider() {
		return provider;
	}


	/**
	 * @param identification the identification to set
	 */
	public void setIdentification(OWSServiceIdentification identification) {
		if(identification != null && !identification.isEmpty())
		{
			isEmpty = false;
			this.identification = identification;
		}
	}


	/**
	 * @return the identification
	 */
	public OWSServiceIdentification getIdentification() {
		return identification;
	}

}
