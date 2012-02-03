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

public class ServiceContactAdressInfo implements Serializable{
	
	private static final long serialVersionUID = -1522579526391545420L;
	
	private String type;
	private String address;
	private String postalCode;
	private String city;
	private String state;
	private String country;
	private boolean isEmpty = true;
	
	public boolean isEmpty (){
		return isEmpty;
	}
	/**
	 * @param type the type to set
	 */
	public void setType(String type) {
		if(!type.equals(""))
			isEmpty = false;
		this.type = type;
	}
	/**
	 * @return the type
	 */
	public String getType() {
		return type;
	}
	/**
	 * @param address the address to set
	 */
	public void setAddress(String address) {
		if(!address.equals(""))
			isEmpty = false;
		this.address = address;
	}
	/**
	 * @return the address
	 */
	public String getAddress() {
		return address;
	}
	/**
	 * @param postalCode the postalCode to set
	 */
	public void setPostalCode(String postalCode) {
		if(!postalCode.equals(""))
			isEmpty = false;
		this.postalCode = postalCode;
	}
	/**
	 * @return the postalCode
	 */
	public String getPostalCode() {
		return postalCode;
	}
	/**
	 * @param city the city to set
	 */
	public void setCity(String city) {
		if(!city.equals(""))
			isEmpty = false;
		this.city = city;
	}
	/**
	 * @return the city
	 */
	public String getCity() {
		return city;
	}
	/**
	 * @param state the state to set
	 */
	public void setState(String state) {
		if(!state.equals(""))
			isEmpty = false;
		this.state = state;
	}
	/**
	 * @return the state
	 */
	public String getState() {
		return state;
	}
	public void setCountry(String country) {
		if(!country.equals(""))
			isEmpty = false;
		this.country = country;
	}
	public String getCountry() {
		return country;
	}
}
