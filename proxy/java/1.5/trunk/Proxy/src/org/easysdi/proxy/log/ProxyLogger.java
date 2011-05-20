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

import org.apache.log4j.Level;
import org.apache.log4j.Logger;
import org.apache.log4j.Priority;

/**
 * @author DEPTH SA
 *
 */
public class ProxyLogger extends Logger {

	protected ProxyLogger(String name) {
		super(name);
		// TODO Auto-generated constructor stub
	}

	private static Logger rootLogger;
	
	
	public static Logger getRootLogger(){
		if (rootLogger == null){
			rootLogger = new ProxyLogger();
		}
		return rootLogger;
	}

	/* (non-Javadoc)
	 * @see org.apache.log4j.Logger#trace(java.lang.Object)
	 */
	@Override
	public void trace(Object message) {
		// TODO Auto-generated method stub
		super.trace(message);
	}

	/* (non-Javadoc)
	 * @see org.apache.log4j.Category#debug(java.lang.Object)
	 */
	@Override
	public void debug(Object message) {
		// TODO Auto-generated method stub
		super.debug(message);
	}

	/* (non-Javadoc)
	 * @see org.apache.log4j.Category#error(java.lang.Object)
	 */
	@Override
	public void error(Object message) {
		// TODO Auto-generated method stub
		super.error(message);
	}

	/* (non-Javadoc)
	 * @see org.apache.log4j.Category#fatal(java.lang.Object)
	 */
	@Override
	public void fatal(Object message) {
		// TODO Auto-generated method stub
		super.fatal(message);
	}

	/* (non-Javadoc)
	 * @see org.apache.log4j.Category#info(java.lang.Object)
	 */
	@Override
	public void info(Object message) {
		// TODO Auto-generated method stub
		super.info(message);
	}

	/* (non-Javadoc)
	 * @see org.apache.log4j.Category#log(org.apache.log4j.Priority, java.lang.Object)
	 */
	@Override
	public void log(Priority priority, Object message) {
		// TODO Auto-generated method stub
		super.log(priority, message);
	}

	/* (non-Javadoc)
	 * @see org.apache.log4j.Category#setLevel(org.apache.log4j.Level)
	 */
	@Override
	public void setLevel(Level level) {
		// TODO Auto-generated method stub
		super.setLevel(level);
	}

	/* (non-Javadoc)
	 * @see org.apache.log4j.Category#warn(java.lang.Object)
	 */
	@Override
	public void warn(Object message) {
		// TODO Auto-generated method stub
		super.warn(message);
	}

}
