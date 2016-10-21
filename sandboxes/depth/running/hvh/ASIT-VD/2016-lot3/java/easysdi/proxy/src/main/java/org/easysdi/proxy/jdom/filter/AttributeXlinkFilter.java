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
package org.easysdi.proxy.jdom.filter;

import org.jdom.Attribute;
import org.jdom.Element;
import org.jdom.Namespace;
import org.jdom.filter.Filter;

/**
 * @author DEPTH SA
 *
 */
@SuppressWarnings("serial")
public class AttributeXlinkFilter implements Filter{

	private static final  Namespace ns = Namespace.getNamespace("http://www.w3.org/1999/xlink");
		
	public boolean matches(Object ob)
	  {
	     //Check if filtered objects are Element 
	     if(!(ob instanceof Element)){return false;}
	
	     //Filter to use against Elements
	     Element element = (Element)ob;
	     Attribute xlink = element.getAttribute("href", ns);
	     if(xlink != null)
	     {
	    	 return true;
	     }
	     else
	     {
	    	 return false;
	     }
	  }

}
