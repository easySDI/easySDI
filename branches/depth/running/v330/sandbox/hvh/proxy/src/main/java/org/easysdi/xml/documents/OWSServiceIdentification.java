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
import java.util.List;

@Deprecated
public class OWSServiceIdentification implements Serializable{
	
	private static final long serialVersionUID = -1522579526391545420L;
	private String title = null;
	private String abst = null;
	private String fees = null;
	private String accessConstraints = null;
	private List<String> keywords = null;
	private boolean isEmpty = true;
	
	public boolean isEmpty (){
		return isEmpty;
	}
	
	/**
	 * @param title the title to set
	 */
	public void setTitle(String title) {
		if(title != null && !"".equals(title))
		{
			isEmpty = false;
			this.title = title;
		}
	}

	/**
	 * @return the title
	 */
	public String getTitle() {
		return title;
	}

	/**
	 * @param abst the abst to set
	 */
	public void setAbst(String abst) {
		if(abst != null && !"".equals(abst))
		{
			isEmpty = false;
			this.abst = abst;
		}
	}

	/**
	 * @return the abst
	 */
	public String getAbst() {
		return abst;
	}

	/**
	 * @param fees the fees to set
	 */
	public void setFees(String fees) {
		if(fees != null && !"".equals(fees))
		{
			isEmpty = false;
			this.fees = fees;
		}
	}

	/**
	 * @return the fees
	 */
	public String getFees() {
		return fees;
	}

	/**
	 * @param accessConstraints the accessConstraints to set
	 */
	public void setAccessConstraints(String accessConstraints) {
		if(accessConstraints != null && !"".equals(accessConstraints))
		{
			isEmpty = false;
			this.accessConstraints = accessConstraints;
		}
	}

	/**
	 * @return the accessConstraints
	 */
	public String getAccessConstraints() {
		return accessConstraints;
	}

	/**
	 * @param keywords the keywords to set
	 */
	public void setKeywords(List<String> keywords) {
		if(keywords != null && keywords.size() != 0)
		{
			isEmpty = false;
			this.keywords = keywords;
		}
	}

	/**
	 * @return the keywords
	 */
	public List<String> getKeywords() {
		return keywords;
	}


}

