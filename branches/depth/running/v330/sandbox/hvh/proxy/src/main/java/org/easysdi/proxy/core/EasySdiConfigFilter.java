package org.easysdi.proxy.core;

import java.io.IOException;

import javax.servlet.FilterChain;
import javax.servlet.ServletException;
import javax.servlet.ServletRequest;
import javax.servlet.ServletResponse;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;

import org.easysdi.proxy.ows.OWSExceptionReport;
import org.easysdi.proxy.ows.v200.OWS200ExceptionReport;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.transaction.annotation.Transactional;
import org.springframework.web.filter.GenericFilterBean;

@Transactional
public class EasySdiConfigFilter extends GenericFilterBean {

	
	
	private Logger logger = LoggerFactory.getLogger("EasySdiConfigFilter");

	public EasySdiConfigFilter() {
		
	}

	public void destroy() {

	}

	public void doFilter(ServletRequest req, ServletResponse res, FilterChain chain) throws IOException, ServletException {
		final HttpServletRequest request = (HttpServletRequest) req;
		final HttpServletResponse response = (HttpServletResponse) res;

		if( request.getPathInfo() == null ||  request.getPathInfo().equals("/"))
		{
			logger.error("Could not determine proxy request from http request. Service name is missing.");
			new OWS200ExceptionReport().sendExceptionReport(request, response, "Could not determine request from http request. Service name is missing.", OWSExceptionReport.CODE_MISSING_PARAMETER_VALUE, "service", HttpServletResponse.SC_BAD_REQUEST) ;
			return;
		}

		if(request.getMethod().equalsIgnoreCase("GET"))
		{
			if(!request.getPathInfo().equalsIgnoreCase("/invalidate")){
				if(request.getParameter("request") == null && request.getParameter("REQUEST") == null && request.getParameter("Request") == null){
					logger.error("Could not determine proxy request from http request. Parameter REQUEST is missing.");
					new OWS200ExceptionReport().sendExceptionReport(request, response, "Could not determine proxy request from http request. Parameter REQUEST is missing.", OWSExceptionReport.CODE_MISSING_PARAMETER_VALUE, "request", HttpServletResponse.SC_BAD_REQUEST) ;
					return;
				}
			}
		}
		else
		{
			if(request.getContentLength() == 0){
				logger.error("Could not determine proxy request from http request. Parameter REQUEST is missing.");
				new OWS200ExceptionReport().sendExceptionReport(request, response, "Could not determine proxy request from http request. Parameter REQUEST is missing.", OWSExceptionReport.CODE_MISSING_PARAMETER_VALUE, "request", HttpServletResponse.SC_BAD_REQUEST) ;
				return;
			}
		}

		chain.doFilter(request, response);
	}
}
