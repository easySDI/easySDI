module namespace eth="http://www.mxquery.org/wsdl/mod4/"; 
import schema namespace sch ="urn:myNamespace" at "ListSchema.xsd"; 
import schema namespace sch2= "urn:myNamespace2" at "ComplexTypeSchema.xsd";
declare function eth:elementMappingFunc($a as element(*,sch2:testComplexType)) as element(sch:testElement) {<testElement xmlns="urn:myNamespace">Yes</testElement>}; 
