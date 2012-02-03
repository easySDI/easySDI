(: Sample module for web service (SOAP) export, defining various different functions with different types, 
including Schema types

The pre-packaged WAR archive of MXQuery contains this as a sample
:)

module namespace eth="http://www.mxquery.org/wsdl";
import schema namespace sch ="urn:myNamespace" at "ListSchema.xsd"; 
import schema namespace sch2= "urn:myNamespace2" at "ComplexTypeSchema.xsd";
declare variable $eth:simpleVariable as xs:string := 'Kyumars';
declare variable $eth:sequenceVariable as xs:integer* := (15,30);
declare variable $eth:unTypedVariable := 100;
declare function eth:unTypedIdentityFunc($a){$a};
declare function eth:nodeSeqResFunc() as element()*  {(<a><a2 x='yesk'>kyumars joox</a2></a>,<b>sheykh</b>,<c>esmaili</c>)};
declare function eth:atomicSeqResFunc() as xs:string *{('kyumars sheykh','esmaili')};
declare function eth:simpleInputSimpleOutputFunc($a as xs:double,$b as xs:integer) as xs:double {$a + $b};
declare function eth:seqInputSimpleOutputFunc($a as xs:string*) as xs:integer {1};
declare function eth:simpleInputSeqOutputFunc($a as xs:integer) as xs:integer* {($a - 1,$a,$a+1)};
declare function eth:seqInputSeqOutputFunc($a as xs:integer+) as xs:integer+ {$a};
declare function eth:elementMappingFunc($a as element(*,sch2:testComplexType)) as element(sch:testElement) {<testElement xmlns="urn:myNamespace">Yes</testElement>}; 