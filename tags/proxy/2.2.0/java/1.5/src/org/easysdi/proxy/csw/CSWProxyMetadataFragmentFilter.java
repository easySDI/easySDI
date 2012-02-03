package org.easysdi.proxy.csw;

import org.jdom.Element;
import org.jdom.filter.*;

public class CSWProxyMetadataFragmentFilter implements Filter
{
	private static final long serialVersionUID = 1L;
	private String _fragment;
	
	public CSWProxyMetadataFragmentFilter(String fragment)
	{
		super();
		_fragment = fragment;
	}
	
	public boolean matches(Object ob)
      {
         //Check if filtered objects are Element 
         if(!(ob instanceof Element)){return false;}

         //Filter to use against Elements
         Element element = (Element)ob;
         if(element.getQualifiedName().equals(_fragment))
         {
        	 return true;
         }
         else
         {
        	 return false;
         }

      }
}
