package org.easysdi.publish.gui.webapp.controllers;

import java.util.Calendar;

import org.easysdi.publish.biz.database.Geodatabase;
import org.easysdi.publish.biz.diffuser.Diffuser;
import org.easysdi.publish.biz.layer.FeatureSource;
import org.easysdi.publish.biz.layer.Layer;
import org.easysdi.publish.dat.dao.GeodatabaseDaoHelper;
import org.easysdi.publish.gui.webapp.ApplicationContextProvider;
import org.springframework.context.ApplicationContext;
import org.springframework.context.support.FileSystemXmlApplicationContext;

public class DatabaseController{


	public static void main(String[] args) {

		ApplicationContext context = new FileSystemXmlApplicationContext("publish-servlet.xml");
		
		
		final Geodatabase geodb = Geodatabase.getFromIdString("1");
		System.out.println(geodb.getName());
		System.out.println(geodb.getGeodatabaseTypeId());
		/*	
		Geodatabase db2 = new Geodatabase();
		db2.setGeodatabaseTypeId(1);
		db2.setName("foo");
		db2.setUser("myUser");
		db2.setPwd("plop");
		db2.setScheme("myschema");
		db2.setUrl("url");
		db2.setPwd("bar");
		if(!db2.persist())
            System.out.println("false");
        else
        	System.out.println("true");
		
		//delete
		Geodatabase geodb3 = Geodatabase.getFromIdString("5");
		if(!geodb3.delete())
            System.out.println("false");
        else
        	System.out.println("true");
    */
    
	/* Diffuser */
		final Diffuser diff = Diffuser.getFromIdString("1");
		System.out.println(diff.getName());
		System.out.println(diff.getPwd());
		System.out.println(diff.getType());
		System.out.println(diff.getUrl());
		System.out.println("diffuser database:");
		System.out.println(diff.getGeodatabase().getName());
		System.out.println(diff.getGeodatabase().getUrl());
		
		//new diff
		Diffuser diff2 = new Diffuser();
		diff2.setName("diff2");
		diff2.setPwd("bar");
		diff2.setType(1);
		diff2.setUrl("http://blah.com");
		diff2.setGeodatabase(geodb);
		diff2.persist();
		
		//delete diff:
		diff2.delete();
		
		//New fs
		FeatureSource fs2 = new FeatureSource();
		fs2.setDiffuser(diff);
		fs2.setGuid("116f0b40-588c-11df-af05-00238b529635");
		fs2.setTableName("116f0b40588c11dfaf0500238b529635");
		fs2.setScriptName("none");
		fs2.setSourceDataType("OGR");
		fs2.setCrsCode("EPSG:21781");
		fs2.setFieldsName("area,perimeter,objectval,objectid,objectorig,yearofchan,seenr,gemteil,bezirksnr,kantonsnr,gemname,gemflaeche");
		fs2.setCreationDate(Calendar.getInstance());
		fs2.setUpdateDate(Calendar.getInstance());
		
		System.out.println("Create fs2");
		fs2.persist();
		
		
		Layer l2 = new Layer();
		l2.setFeatureSource(fs2);
		l2.setGuid("116f0b40-588c-11df-af05-00238b529636");
		l2.setKeywordList("key1,key2,keysha");
		l2.setTitle("My Layer 2");
		l2.setName("My name 2");
		l2.setDescription("descr");
		l2.setStatus("PUBLISHED");
		l2.setCreationDate(Calendar.getInstance());
		l2.setUpdateDate(Calendar.getInstance());
       
		System.out.println("Save layer2");
		l2.persist();
		
		//delete the Layer
		System.out.println("delete layer2");
		l2.delete();
	}
}
