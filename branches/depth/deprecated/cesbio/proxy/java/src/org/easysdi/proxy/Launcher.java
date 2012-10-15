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
package org.easysdi.proxy;

import org.mortbay.jetty.Connector;
import org.mortbay.jetty.Handler;
import org.mortbay.jetty.Server;
import org.mortbay.jetty.handler.DefaultHandler;
import org.mortbay.jetty.handler.HandlerList;
import org.mortbay.jetty.nio.SelectChannelConnector;
import org.mortbay.jetty.webapp.WebAppContext;

public class Launcher {

    public static void main(String[] args) throws Exception {
	String warPath = "web";
	if(args.length>0) warPath = args[0];
	
	Server jetty = new Server();
	Connector connector = new SelectChannelConnector();
	connector.setPort(8070);
	jetty.setConnectors(new Connector[] { connector });
	WebAppContext appContext = new WebAppContext();
	appContext.setContextPath("/proxy");
	appContext.setWar(warPath);
	HandlerList handlers = new HandlerList();
	handlers.setHandlers(new Handler[] { appContext, new DefaultHandler() });
	jetty.setHandler(handlers);
	jetty.start();
	jetty.join();
    }
}
