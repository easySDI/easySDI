package org.easysdi.jdom.filter;

import org.jdom.Element;
import org.jdom.Namespace;
import org.jdom.filter.Filter;

public class ElementOperationFilter implements Filter{

private static final long serialVersionUID = 1L;

	
	public boolean matches(Object ob)
      {
         //Check if filtered objects are Element 
         if(!(ob instanceof Element)){return false;}

         //Filter to use against Elements
         Element element = (Element)ob;
         if(element.getName().equals("Operation"))
         {
        	 return true;
         }
         else
         {
        	 return false;
         }

      }
}
