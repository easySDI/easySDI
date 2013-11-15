<%@ page language="java" contentType="text/html; charset=UTF-8"
	pageEncoding="UTF-8"%>
<%@ page import="java.io.File"%>
<%@ page isELIgnored="false"%>
<%@ taglib prefix="c" uri="http://java.sun.com/jsp/jstl/core"%>
<%@ taglib prefix="x" uri="http://java.sun.com/jsp/jstl/xml"%>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>EasySDI PROXY</title>
</head>
<body>
<h1>EasySDI: Available PROXY services</h1>
<%
    
	String configFile = application.getInitParameter("configFile");
    pageContext.setAttribute("configFile", configFile);
    pageContext.setAttribute("configExist", new File(configFile).exists());
%>
<c:choose>
	<c:when test="${!configExist}">
		<h1>Config file not found !</h1>
	</c:when>
	<c:when test="${configExist}">
		<c:import url="file:///${configFile}" var="config" />
		<x:parse xml="${config}" var="doc" />
		<ul>
			<x:forEach var="n" select="$doc//config">
				<x:set var="service" select="string($n/@id)" scope="page" />
				<li><a
					href="ogc/<%=pageContext.getAttribute("service").toString()%>"><x:out
					select="$n/@id" /></a></li>
			</x:forEach>
		</ul>
	</c:when>
</c:choose>
</body>
</html>