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
	Server jetty = new Server();
	Connector connector = new SelectChannelConnector();
	connector.setPort(8081);
	jetty.setConnectors(new Connector[] { connector });
	WebAppContext appContext = new WebAppContext();
	appContext.setContextPath("/proxy");
	appContext.setWar("web");
	HandlerList handlers = new HandlerList();
	handlers.setHandlers(new Handler[] { appContext, new DefaultHandler() });
	jetty.setHandler(handlers);
	jetty.start();
	jetty.join();
    }
}
