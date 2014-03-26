package org.easysdi.services.wps;

import java.util.*;
import java.io.*;
import java.io.File;
import java.io.FileWriter;
import java.io.PrintWriter;
import java.io.StringWriter;
import org.apache.commons.mail.*;
import org.apache.commons.mail.SimpleEmail;
import org.apache.commons.mail.EmailException;

/**
* Simple demonstration of using the javax.mail API.
*
* Run from the command line. Please edit the implementation
* to use correct email addresses and host name.
*/

public class Mailer 
{
  /**
  * Send a single email.
  */
	
  private static String mail="";
  
  public void sendEmail(
    String aFromEmailAddr, String aFromEmailName, String aToEmailAddr,
    String aSubject, String aBody
  ){
    //Here, no Authenticator argument is used (it is null).
    //Authenticators are used to prompt the user for user
    //name and password.
	try{
	SimpleEmail email = new SimpleEmail();
	email.setHostName("localhost");
	email.setCharset(Email.ISO_8859_1);
	email.addTo(aToEmailAddr, aToEmailAddr);
	email.setFrom(aFromEmailAddr, aFromEmailName);
	email.setSubject(aSubject);
	email.setMsg(aBody);
	email.send();
	}
	catch (EmailException ex){
        System.err.println("EmailException. " + ex);
      }
  }

  /**
  * Allows the config to be refreshed at runtime, instead of
  * requiring a restart.
  */
  public static void refreshConfig() {
    fMailServerConfig.clear();
    fetchConfig();
  }

  // PRIVATE //

  private static Properties fMailServerConfig = new Properties();

  static {
    fetchConfig();
  }

  /**
  * Open a specific text file containing mail server
  * parameters, and populate a corresponding Properties object.
  */
  private static void fetchConfig() {
    InputStream input = null;
    try {
      //If possible, one should try to avoid hard-coding a path in this
      //manner; in a web application, one should place such a file in
      //WEB-INF, and access it using ServletContext.getResourceAsStream.
      //Another alternative is Class.getResourceAsStream.
      //This file contains the javax.mail config properties mentioned above.

      if (mail != "")
      {
	      input = new FileInputStream( mail );
	      fMailServerConfig.load( input );
      }
    }
    catch ( IOException ex ){
      System.err.println("Cannot open and load mail server properties file.");
    }
    finally {
      try {
        if ( input != null ) input.close();
      }
      catch ( IOException ex ){
        System.err.println( "Cannot close mail server properties file." );
      }
    }
  }
} 

