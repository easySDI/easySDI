/**
 * EasySDI, a solution to implement easily any spatial data infrastructure
 * Copyright (C) 2009 Antoine Elbel & Remy Baud (aelbel@solnet.ch remy.baud@asitvd.ch)
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
package org.easysdi.publish.util;

import java.lang.reflect.*;
import java.io.*;
import java.net.*;

/*
 * A class that can add class to a classloader in runtime.
 */
public class ClassPathHacker {
	 
	private static final Class[] parameters = new Class[]{URL.class};
	 
	public static void addFile(String s) throws IOException {
		File f = new File(s);
		addFile(f);
	}//end method
	 
	public static void addFile(String s, ClassLoader cl) throws IOException {
		File f = new File(s);
		addURL(f.toURL(), cl);
	}//end method
	
	public static void addFile(File f) throws IOException {
		addURL(f.toURL());
	}//end method
	 
	 
	public static void addURL(URL u) throws IOException {
			
		URLClassLoader sysloader = (URLClassLoader)ClassLoader.getSystemClassLoader();
		Class sysclass = URLClassLoader.class;
	 
		try {
			Method method = sysclass.getDeclaredMethod("addURL",parameters);
			method.setAccessible(true);
			method.invoke(sysloader,new Object[]{ u });
		} catch (Throwable t) {
			t.printStackTrace();
			throw new IOException("Error, could not add URL to system classloader");
		}//end try catch
			
	}//end method
	 
	public static void addURL(URL u, ClassLoader cl) throws IOException {
		
		URLClassLoader loader = (URLClassLoader)cl;
		Class sysclass = URLClassLoader.class;
	 
		try {
			Method method = sysclass.getDeclaredMethod("addURL",parameters);
			method.setAccessible(true);
			method.invoke(loader,new Object[]{ u });
		} catch (Throwable t) {
			t.printStackTrace();
			throw new IOException("Error, could not add URL to system classloader");
		}//end try catch
			
	}//end method
	
	}//end class
