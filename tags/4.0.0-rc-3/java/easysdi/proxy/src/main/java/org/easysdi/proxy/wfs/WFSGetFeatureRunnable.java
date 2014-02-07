package org.easysdi.proxy.wfs;

import java.util.List;
import java.util.Map;

import org.easysdi.proxy.core.ProxyServlet;

public class WFSGetFeatureRunnable implements Runnable {

	private String type;
	private String url;
	private String content;
	private ProxyServlet servlet;
	private List serversIndex;
	private Map<Integer, String> filePathList;
	private int idServer;
	private int id;

	public WFSGetFeatureRunnable(String type, String url, String content, ProxyServlet servlet, List serversIndex, Map<Integer, String> filePathList,
			int idServer, int id) {
		this.type = type;
		this.url = url;
		this.content = content;
		this.servlet = servlet;
		this.serversIndex = serversIndex;
		this.filePathList = filePathList;
		this.idServer = idServer;
		this.id = id;
	}

	public void run() {
		synchronized (servlet) {
			String filePath = servlet.sendData(type, url, content);
			synchronized (serversIndex) {
				this.serversIndex.add(idServer);
			}
			synchronized (filePathList) {
				this.filePathList.put(id, filePath);
			}
		}
	}
}
