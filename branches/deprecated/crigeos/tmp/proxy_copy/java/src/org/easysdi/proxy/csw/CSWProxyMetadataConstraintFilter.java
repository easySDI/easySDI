package org.easysdi.proxy.csw;

import org.jdom.Element;
import org.jdom.filter.*;

public class CSWProxyMetadataConstraintFilter implements Filter
{
	private static final long serialVersionUID = 1L;
	
	
	public boolean matches(Object ob)
      {
         //Check if filtered objects are Element 
         if(!(ob instanceof Element)){return false;}

         //Filter to use against Elements
         Element element = (Element)ob;
         if(element.getQualifiedName().equals("csw:Constraint"))
         {
        	 return true;
         }
         else
         {
        	 return false;
         }

      }
}
