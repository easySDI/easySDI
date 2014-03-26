/**
 * EasySDI, a solution to implement easily any spatial data infrastructure
 * Copyright (C) EasySDI Community
 * For more information : www.easysdi.org
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
package org.easysdi.proxy.log;

import java.io.BufferedWriter;
import java.io.File;
import java.io.FileWriter;
import java.text.DateFormat;
import java.text.SimpleDateFormat;
import java.util.Date;
import java.util.HashMap;
import java.util.Iterator;
import java.util.List;
import java.util.Vector;

import javax.servlet.http.HttpServletRequest;

import org.apache.log4j.Level;
import org.apache.log4j.Logger;
import org.apache.log4j.Priority;
import org.springframework.security.core.context.SecurityContextHolder;

/**
 * @author DEPTH SA
 *
 */
public class ProxyLogger extends Logger {



	private static Logger rootLogger;
	private List<String> lLogs = new Vector<String>();
	private  String logDateFormat;
	private Level level = Level.INFO;
	private  String logFile;
	private static HashMap<String,ProxyLogger> hLogger = new HashMap<String,ProxyLogger>();
	
	public ProxyLogger(String name) {
		super(name);
	}

	public static Logger getRootLogger(){
		if (rootLogger == null){
			rootLogger = new ProxyLogger("root");
		}
		return rootLogger;
	}
	
	public static synchronized Logger getLogger(String name){
		if(!hLogger.containsKey(name))
			hLogger.put(name, new ProxyLogger(name));
		return hLogger.get(name);
	}
	
	public  void setDateFormat (String logDateFormat){
		this.logDateFormat = logDateFormat;
	}
	
	public  void setLogFile (String logFile){
		this.logFile = logFile;
	}

	/* (non-Javadoc)
	 * @see org.apache.log4j.Logger#trace(java.lang.Object)
	 */
	@Override
	public void trace(Object message) {
		this.log(Level.TRACE, message);
	}

	/* (non-Javadoc)
	 * @see org.apache.log4j.Category#debug(java.lang.Object)
	 */
	@Override
	public void debug(Object message) {
		this.log(Level.DEBUG, message);
	}

	/* (non-Javadoc)
	 * @see org.apache.log4j.Category#error(java.lang.Object)
	 */
	@Override
	public void error(Object message) {
		this.log(Level.ERROR, message);
	}

	/* (non-Javadoc)
	 * @see org.apache.log4j.Category#fatal(java.lang.Object)
	 */
	@Override
	public void fatal(Object message) {
		this.log(Level.FATAL, message);
	}
	
	/* (non-Javadoc)
	 * @see org.apache.log4j.Category#warn(java.lang.Object)
	 */
	@Override
	public void warn(Object message) {
		this.log(Level.WARN, message);
	}
	
	/* (non-Javadoc)
	 * @see org.apache.log4j.Category#debug(java.lang.Object, java.lang.Throwable)
	 */
	@Override
	public void debug(Object message, Throwable t) {
		this.log(Level.DEBUG, message + " - " + t.toString());
	}

	/* (non-Javadoc)
	 * @see org.apache.log4j.Category#error(java.lang.Object, java.lang.Throwable)
	 */
	@Override
	public void error(Object message, Throwable t) {
		this.log(Level.ERROR, message + " - " + t.toString());
	}

	/* (non-Javadoc)
	 * @see org.apache.log4j.Category#fatal(java.lang.Object, java.lang.Throwable)
	 */
	@Override
	public void fatal(Object message, Throwable t) {
		this.log(Level.FATAL, message + " - " + t.toString());
	}

	/* (non-Javadoc)
	 * @see org.apache.log4j.Category#info(java.lang.Object, java.lang.Throwable)
	 */
	@Override
	public void info(Object message, Throwable t) {
		this.log(Level.INFO, message + " - " + t.toString());
	}

	/* (non-Javadoc)
	 * @see org.apache.log4j.Category#info(java.lang.Object)
	 */
	@Override
	public void info(Object message) {
		this.log(Level.INFO, message);
	}

	/* (non-Javadoc)
	 * @see org.apache.log4j.Category#log(org.apache.log4j.Priority, java.lang.Object)
	 */
	@Override
	public void log(Priority priority, Object message) {
		String name = null;
		String text = null;
		
		if(priority == null){
			priority = level;
		}
		
		if(!priority.isGreaterOrEqual(level))
			return;
		
		if(message == null)
			text = "null";
		if(message instanceof Object[] ){
			name = ((String[])message)[0];
			text = ((String[])message)[1];
		}else{
			text = message.toString();
			if(text.contains("=")){
				name = text.substring(0,text.indexOf("="));
				text = text.substring(text.indexOf("=")+1);
			}
		}
		
		
		StringBuffer sb = new StringBuffer();

		DateFormat dateFormat = new SimpleDateFormat(this.logDateFormat);
		Date d = new Date();

		sb.append("<logEntry time=\"" + dateFormat.format(d) + "\" severity=\""+priority.toString()+"\"");
		if (name != null) {
			sb.append(" name=\"");
			sb.append(name);
			sb.append("\"");
		}
		sb.append(">");
		sb.append("<![CDATA[");
		sb.append(text);
		sb.append("]]>");
		sb.append("</logEntry>");

		synchronized (lLogs) {
			if (lLogs == null)
				lLogs = new Vector<String>();
			lLogs.add(sb.toString());
		}
	}

	/* (non-Javadoc)
	 * @see org.apache.log4j.Category#setLevel(org.apache.log4j.Level)
	 */
	@Override
	public void setLevel(Level level) {
		this.level = level;
	}

	

	public void writeInLog(String date, HttpServletRequest req) {
		boolean newLog = false;
		String u = "";
		if (SecurityContextHolder.getContext().getAuthentication() != null) {
			u = SecurityContextHolder.getContext().getAuthentication().getName();
		}

		try {
			String logFile = this.logFile;
			if (logFile != null) {
				File fLogFile = new File(logFile);
				if (!fLogFile.exists()) {
					fLogFile.createNewFile();
					newLog = true;
				}

				FileWriter fstream = new FileWriter(fLogFile, true);
				BufferedWriter bWriter = new BufferedWriter(fstream);

//				this.info( new String[]{ "RemoteAddr", req.getRemoteAddr()});
//				this.info( new String[]{ "RemoteUser", req.getRemoteUser()});
//				this.info( new String[]{ "QueryString", req.getQueryString()});
//				this.info( new String[]{ "RequestURL", req.getRequestURL().toString()});

				if (newLog)
					bWriter.write("<Log>");
				bWriter.write("<LogRequest user=\"" + u + "\" requestTime=\"" + date + "\"> \n");
				synchronized (lLogs) {
					for (Iterator<String> i = lLogs.iterator(); i.hasNext();) {
						bWriter.write(i.next() + "\n");
					}
				}
				bWriter.write("</LogRequest>" + "\n");
				bWriter.close();
			} else {
				String sHeader = "<LogRequest user=\"" + u + "\" requestTime=\"" + date + "\">" + "\n";
				System.err.println(sHeader);
				synchronized (lLogs) {
					for (Iterator<String> i = lLogs.iterator(); i.hasNext();) {
						System.err.println(i.next() + "\n");
					}
				}
				System.err.println("</LogRequest>" + "\n");
			}
		} catch (Exception e) {
			e.printStackTrace();
			String sHeader = "<LogRequest user=\"" + u + "\" requestTime=\"" + date + "\">" + "\n";
			System.err.println(sHeader);
			synchronized (lLogs) {
				for (Iterator<String> i = lLogs.iterator(); i.hasNext();) {
					System.err.println(i.next() + "\n");
				}
			}
			System.err.println("</LogRequest>" + "\n");

		}

	}
}
