/**
 * Copyright (C) 2008 DEPTH SA, Chemin d’Arche 40b, CH-1870 Monthey, easysdi@depth.ch 
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
package ch.depth.migration.asit;

import java.io.File;
import java.io.InputStream;
import java.util.Vector;

import org.xml.sax.Attributes;
import org.xml.sax.InputSource;
import org.xml.sax.SAXException;
import org.xml.sax.XMLReader;
import org.xml.sax.helpers.DefaultHandler;
import org.xml.sax.helpers.XMLReaderFactory;

public class ISO191139Handler extends DefaultHandler {

    private Vector<clazz> v = new Vector<clazz>();
    
    //private Hashtable h = new Hashtable();
//    private Hashtable hLocFreeText = new Hashtable();
//    private Hashtable hFreeText = new Hashtable();
    private int curId= 4000;
    private Vector parentId = new Vector();
    private int classclassid = 5000;
    
    private class freetext{
	int id;
	int text_id;
	int classes_id;
    }
    private class clazz {
	int parentId;
	int id;
	String name;
	String iso_key;
	int partner_id = 0;
	int is_global = 0;
	String description ;
	int is_final = 0;
	int is_editable = 1;
	String type;	
	
	public void getCreateClass (){
	    if (parentId==4001) {
		
		is_final = 1;		
	    
	    }else {
		
		is_final = 0;
		
	    }
	    String type2 = type;
	    if (type2.equals("Date")){type2 = "freetext";}
	    if (type2.equals("Decimal")){type2 = "freetext";}
	    if (type2.equals("list")){type2 = "freetext";}
	    
	    System.out.print("INSERT INTO `jos_easysdi_metadata_classes` (`id`, `name`, `iso_key`, `partner_id`, `is_global`, `description`, `is_final`, `is_editable`, `type`) VALUES ");	    
	    System.out.println("("+id +",'"+ name +"','"+ iso_key+"',"+partner_id+","+is_global+",'"+description+"',"+is_final+","+is_editable+",'"+type2+"');");
	    
	    if (type.equals("freetext")){
		System.out.println("INSERT INTO `jos_easysdi_metadata_classes_freetext` (`id`, `classes_id`, `freetext_id`) VALUES (0, "+id+", (SELECT ID FROM jos_easysdi_metadata_freetext WHERE NAME = 'ASIT - Simple freetext without a default value'));");				
	    }
	    if (type.equals("locfreetext")){
		System.out.println("INSERT INTO `jos_easysdi_metadata_classes_locfreetext` (`id`, `classes_id`, `loc_freetext_id`) VALUES (0, "+id+", (SELECT ID FROM jos_easysdi_metadata_loc_freetext WHERE NAME ='ASIT - Texte en  Francais suisse sans valeur par défaut'));");
	    }
	    if (type.equals("Date")){
		System.out.println("INSERT INTO `jos_easysdi_metadata_classes_freetext` (`id`, `classes_id`, `freetext_id`) VALUES (0, "+id+", (SELECT ID FROM jos_easysdi_metadata_freetext WHERE NAME = 'ASIT - Date'));");		
	    }
	    if (type.equals("Decimal")){
		System.out.println("INSERT INTO `jos_easysdi_metadata_classes_freetext` (`id`, `classes_id`, `freetext_id`) VALUES (0, "+id+", (SELECT ID FROM jos_easysdi_metadata_freetext WHERE NAME = 'ASIT - Decimal'));");		
	    }
	    
	     
	    if (is_final == 1){
		
		System.out.print("INSERT INTO `jos_easysdi_metadata_standard_classes` (`id`, `standard_id`, `position`, `partner_id`, `tab_id`, `class_id`) VALUES ");
		System.out.println("(0,(SELECT ID FROM jos_easysdi_metadata_standard WHERE NAME ='ASITVD - ISO 19115:2003/19139'),0,0,2001,"+id+" );");
		
	    }
	}
	
	public void getCreateClassClass (){	    
	 System.out.print("INSERT INTO `jos_easysdi_metadata_classes_classes` (`id`, `classes_from_id`, `classes_to_id`) VALUES ");
	 System.out.println("("+classclassid+","+parentId+","+id  +");");		 
	}
	
	
	
    };
    
    public void startElement(String nameSpace, String localName, String qName,
	    Attributes attr) throws SAXException {

	
//System.err.println("==>"+qName+"<===");
	curId ++;
	
	//if (!h.containsKey(qName)){
	    //h.put(curId, qName);
	    
	    clazz c= new clazz();
	    if (parentId.size()>0){
		c.parentId= ((Integer)parentId.get(parentId.size()-1)).intValue();
	    }else {
		c.parentId= 0;
	    }
	    c.id = curId;
	    c.name = "ASIT - "+qName;
	    c.iso_key = qName;
	    c.is_global = 1;
	    c.description = qName;
	    if (qName.equals("gmd:LocalisedCharacterString")){
		c.type = "locfreetext";
		
		//Recherche son parent et met le à freetext
		
		for (int j= v.size()-1;j>=0;j--){
		    clazz c2 = ((clazz)v.get(j));
		    if (c2.id == c.parentId){
			c2.type="locfreetext";
			break;
		    }		    
		}
	    }else if (qName.equals("gco:CharacterString")){
		c.type = "freetext";
		
		//Recherche son parent et met le à freetext
		for (int j= v.size()-1;j>=0;j--){
		    clazz c2 = ((clazz)v.get(j));
		    if (c2.id == c.parentId){
			c2.type="freetext";			
			break;
		    }		    
		}			
	    }else if (qName.equals("gco:Date")){
		c.type = "freetext";
		
		//Recherche son parent et met le à freetext
		for (int j= v.size()-1;j>=0;j--){
		    clazz c2 = ((clazz)v.get(j));
		    if (c2.id == c.parentId){
			c2.type="Date";			
			break;
		    }		    
		}			
	    }else if (qName.equals("gco:Decimal")){
		c.type = "freetext";
		
		//Recherche son parent et met le à freetext
		for (int j= v.size()-1;j>=0;j--){
		    clazz c2 = ((clazz)v.get(j));
		    if (c2.id == c.parentId){
			c2.type="Decimal";			
			break;
		    }		    
		}			
	    }else if (qName.equals("gmd:MD_TopicCategoryCode")){
		c.type = "list";
		
	    }
	    
	    
	    
	    else {
		c.type = "class";				
	    }
	    
	    if (c.type.equals("class"))  v.add(c);
	    
	    parentId.add(new Integer(curId));
	//}	
	
	
	    
	//System.out.println(qName+" "+curId + "parent ID : "+c.parentId);
		
    }


    public void endElement(String nameSpace, String localName, String qName)
    throws SAXException {

	//System.out.println("FIN :"+qName);
	parentId.remove(parentId.size()-1);
    }

    public void endDocument() {
    }

    public void characters(char[] caracteres, int debut, int longueur)
    throws SAXException {


    }

    public void parcourClass(){    
        for (int i=0;i<v.size();i++){            
    		((clazz)v.get(i)).getCreateClass();    	
        }
    }
    
    
    public void parcourClassClass(){
	    
        for (int i=0;i<v.size();i++){
            classclassid++;
    		((clazz)v.get(i)).getCreateClassClass();    	
        }
    }
    /**
     * This program reads all the file in the folder c:\download and
     * converts the non-standard asitvd metadata into the standard iso19139 under the directory c:\download\iso
     * @param args
     */
    public static void main(String args[]) {
	try {
	    final String DIR =  "C:\\download2\\iso\\";

	    File dir = new File(DIR);

	    String[] s = dir.list();
	    //s.length
	    for (int i = 0; i < 1; i++) {
		s[i] = "32_5.xml";
		//File f = new File(DIR + s[i]);
		File f = new File(DIR + s[i]);
		if (f.isFile()) {
		    XMLReader xr = XMLReaderFactory.createXMLReader();
		    ISO191139Handler avdHandler = new ISO191139Handler();

		    InputStream is = new java.io.FileInputStream(new File(
			    DIR + s[i]));
		    System.out.println(DIR + s[i]);

		    xr.setContentHandler(avdHandler);
		    xr.parse(new InputSource(is));
		    avdHandler.parcourClass();
		    avdHandler.parcourClassClass();
		   }
	    }
	} catch (Exception e) {
	    // TODO Auto-generated catch block
	    e.printStackTrace();
	}
    }
}
