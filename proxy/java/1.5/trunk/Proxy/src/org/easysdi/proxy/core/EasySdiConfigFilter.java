package org.easysdi.proxy.core;

import java.io.File;
import java.io.FileInputStream;
import java.io.FileNotFoundException;
import java.io.IOException;
import java.io.InputStream;
import java.security.Principal;

import javax.servlet.FilterChain;
import javax.servlet.ServletException;
import javax.servlet.ServletRequest;
import javax.servlet.ServletResponse;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import javax.xml.bind.JAXBContext;
import javax.xml.bind.JAXBException;
import javax.xml.bind.Unmarshaller;

import net.sf.ehcache.Cache;
import net.sf.ehcache.CacheManager;
import net.sf.ehcache.Element;

import org.easysdi.proxy.policy.Policy;
import org.easysdi.proxy.policy.PolicySet;
import org.easysdi.xml.documents.Config;
import org.easysdi.xml.handler.ConfigFileHandler;
import org.springframework.web.filter.GenericFilterBean;
import org.xml.sax.InputSource;
import org.xml.sax.SAXException;
import org.xml.sax.XMLReader;
import org.xml.sax.helpers.XMLReaderFactory;

public class EasySdiConfigFilter extends GenericFilterBean {

	private Cache configCache;
	private String servletName;
	private CacheManager cm;
	private Unmarshaller u;

	public EasySdiConfigFilter(CacheManager cm) {
		this.cm = cm;
		configCache = cm.getCache("configCache");
		JAXBContext jc;
		try {
			jc = JAXBContext.newInstance(org.easysdi.proxy.policy.PolicySet.class);
			u = jc.createUnmarshaller();
		} catch (JAXBException e) {
			e.printStackTrace();
		}
	}

	public void destroy() {

	}

	public void doFilter(ServletRequest req, ServletResponse res, FilterChain chain) throws IOException, ServletException {
		final HttpServletRequest request = (HttpServletRequest) req;
		final HttpServletResponse response = (HttpServletResponse) res;

		servletName = request.getPathInfo().substring(1);
		Config configuration;
		try {
			configuration = setConfig();
			setPolicySet(configuration, request);
		} catch (SAXException e) {
			e.printStackTrace();
		} catch (JAXBException e) {
			e.printStackTrace();
		}
		chain.doFilter(request, response);
	}

	private Config setConfig() throws SAXException, IOException {
		Config configuration = null;
		String configFile = getServletContext().getInitParameter("configFile");
		File configF = new File(configFile).getAbsoluteFile();
		long lastmodified = configF.lastModified();
		Element configE = configCache.get(servletName + "configFile");
		if (configE != null && configE.getVersion() != lastmodified)
			configE = null;
		if (configE == null) {
			XMLReader xr = XMLReaderFactory.createXMLReader();
			ConfigFileHandler confHandler = new ConfigFileHandler(servletName);
			InputStream is = new java.io.FileInputStream(configFile);
			xr.setContentHandler(confHandler);
			xr.parse(new InputSource(is));
			configuration = confHandler.getConfig();
			configE = new Element(servletName + "configFile", configuration);
			configE.setVersion(lastmodified);
			configCache.put(configE);
		} else
			configuration = (Config) configE.getValue();
		return configuration;
	}

	private void setPolicySet(Config configuration, HttpServletRequest req) throws JAXBException, FileNotFoundException {
		String filePath = new File(configuration.getPolicyFile()).getAbsolutePath();
		File policyF = new File(filePath).getAbsoluteFile();
		long plastmodified = policyF.lastModified();

		String user = null;
		Principal principal = req.getUserPrincipal();
		if (principal != null)
			user = principal.getName();

		Element policyE = configCache.get(servletName + user + "policyFile");

		if (policyE != null && policyE.getVersion() != plastmodified)
			policyE = null;

		if (policyE == null) {

			PolicySet policySet = (PolicySet) u.unmarshal(new FileInputStream(filePath));
			PolicyHelpers ph = new PolicyHelpers(policySet, servletName);
			Policy policy = ph.getPolicy(user, req);
			policyE = new Element(servletName + user + "policyFile", policy);
			policyE.setVersion(plastmodified);
			configCache.put(policyE);
		}
	}

}
