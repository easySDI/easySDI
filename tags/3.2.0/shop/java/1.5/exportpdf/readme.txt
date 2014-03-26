23.3.2010/RB

This webapp enables the ability to print a metadata as pdf. It replaces the use of Javabridge.

For more info, see:
http://forge.easysdi.org/issues/show/142

Location: easysdi\shop\java\1.5\trunk\exportpdf

Installation:
1)Deploy exportpdf.war in the tomcat webapp folder
2)Configure correctly the two paths in the init-param of the web.xml file.
3)Set the Easysdi key: EXPORT_PDF_URL to http://localhost:8083/exportpdf/PdfServlet (accordingly to your tomcat install)