package org.easysdi.publish.gui.config;

import javax.servlet.http.HttpServletRequest;

import org.springframework.context.ApplicationContext;
import org.springframework.context.support.FileSystemXmlApplicationContext;

public class TestConfig {
		
	public static void main(String args[]){
		ApplicationContext context = new FileSystemXmlApplicationContext("publish-servlet.xml");
		Config conf = new Config();
		String str = conf.getPublicationServerlist();
		System.out.println(str);		
	}
	
}
