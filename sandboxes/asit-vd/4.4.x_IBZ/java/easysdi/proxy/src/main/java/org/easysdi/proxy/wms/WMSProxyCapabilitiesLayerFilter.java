package org.easysdi.proxy.wms;

import org.jdom.Element;
import org.jdom.filter.*;

public class WMSProxyCapabilitiesLayerFilter implements Filter
{
	private static final long serialVersionUID = 1L;
	
	
	public boolean matches(Object ob)
      {
         //Check if filtered objects are Element 
         if(!(ob instanceof Element)){return false;}

         //Filter to use against Elements
         Element element = (Element)ob;
         if(element.getName().equals("Layer"))
         {
        	 return true;
         }
         else
         {
        	 return false;
         }

      }
}
