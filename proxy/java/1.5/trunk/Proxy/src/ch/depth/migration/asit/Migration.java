/**
 * EasySDI, a solution to implement easily any spatial data infrastructure
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

import java.io.BufferedWriter;
import java.io.OutputStreamWriter;

/**
 * @author Administrateur
 *
 */
public class Migration {
    
public static void main(String [] args){
  
    String s = "http://www.asit.vd.ch/geoportal/service/administration/service/xml.asp?lng=FR&form=metadata";
    
    int inc = 5;
    for (int i = 0; i < 800;i = i +inc){
	  try{
    	java.net.URL url = new java.net.URL(s+"&first="+i+"&last="+(i+inc));
    	System.out.println(s+"&first="+i+"&last="+(i+inc));

    	java.net.HttpURLConnection hpcon = null;
    	
    	hpcon = (java.net.HttpURLConnection) url.openConnection();            
    	hpcon.setRequestMethod("GET");						
    	hpcon.setUseCaches (false);
    	hpcon.setDoInput(true);
    	hpcon.setDoOutput(false);
    	java.io.BufferedReader in = new java.io.BufferedReader(new java.io.InputStreamReader(hpcon.getInputStream()));
    	String input;
    	java.io.FileOutputStream fos = new java.io.FileOutputStream(new java.io.File("C:\\download\\"+i+".xml"));
    	BufferedWriter wr = new BufferedWriter(new OutputStreamWriter(fos));
    		    	
    	while((input = in.readLine()) != null) {
    		wr.append(input+"\n");
    	}
    	in.close();
    	wr.close();
	  }catch (Exception e){e.printStackTrace();}	
    } 
}
    
}
