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
package org.easysdi.jdom.filter;

import java.util.List;

import org.jdom.Attribute;
import org.jdom.Element;
import org.jdom.Namespace;
import org.jdom.filter.Filter;

/**
 * @author DEPTH SA
 *
 */
@SuppressWarnings("serial")
public class ElementMD_MetadataNonAuthorizedFilter implements Filter {

	public Namespace nsSDI = Namespace.getNamespace("sdi","http://www.easysdi.org/2011/sdi") ;
	public Namespace nsGMD = Namespace.getNamespace("gmd","http://www.isotc211.org/2005/gmd") ;
	public Namespace nsGCO = Namespace.getNamespace("gco","http://www.isotc211.org/2005/gco") ;


	private List <String> _authorizedGuidList;
	private Boolean _withHarvested;

	public ElementMD_MetadataNonAuthorizedFilter(List <String> authorizedGuidList, Boolean withHarvested) {
		super();
		this._authorizedGuidList = authorizedGuidList;
		this._withHarvested = withHarvested;
	}

	public boolean matches(Object ob)
	{
		//Check if filtered objects are Element 
		if(!(ob instanceof Element)){return false;}

		//Filter to use against Elements
		Element element = (Element)ob;
		if(element.getName().equals("MD_Metadata"))
		{
			Element sdi = element.getChild("platform",nsSDI);
			if(sdi != null){
				Attribute a = sdi.getAttribute("harvested");
				if(a != null){
					if(a.getValue().equalsIgnoreCase("true")){
						if(!this._withHarvested)
							return true;
						else
							return false;
					}
				}
			}
			
			//This list is null if all the EasySDI Metadatas are authorized to be delivered
			if(this._authorizedGuidList == null){
				return false;
			}
			
			Element fId = element.getChild("fileIdentifier", nsGMD);
			if(fId != null)
			{
				String text = fId.getChildText("CharacterString", nsGCO);
				if(!this._authorizedGuidList.contains(text)){
					return true;
				}

			}
			return false;
		}
		else
		{
			return false;
		}

	}

}
