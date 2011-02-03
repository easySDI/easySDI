package org.easysdi.proxy.wmts;

import org.jdom.Attribute;
import org.jdom.Element;
import org.jdom.Namespace;
import org.jdom.filter.Filter;

public class WMTSProxyCapabilitiesXlinkFilter implements Filter{

	private static final long serialVersionUID = 1L;
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
