Configuration hint
------------------

Working with WFS could generate a lot of GML. 
In this case, using http compression could dramatically improve the performance.
Http compression can be set on the servlet engine hosting the Easysdi's proxy.
With apache just edit the server.xml file and add the following parameter to the connector. 

compression="on"
compressionMinSize="2048"
noCompressionUserAgents="gozilla, traviata"
compressableMimeType="text/html,text/xml"


Connector will look like that : 
               
<Connector port="8080" maxHttpHeaderSize="8192"
               maxThreads="150" minSpareThreads="25" maxSpareThreads="75"
               enableLookups="false" redirectPort="8443" acceptCount="100"
               connectionTimeout="20000" disableUploadTimeout="true"
			   compression="on"
               compressionMinSize="2048"
               noCompressionUserAgents="gozilla, traviata"
               compressableMimeType="text/html,text/xml"               
               >
  