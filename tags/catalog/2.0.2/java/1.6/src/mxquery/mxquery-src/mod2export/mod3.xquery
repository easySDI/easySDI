module namespace eth="http://www.mxquery.org/wsdl/mod3/";
declare variable $eth:unTypedVariable := 100;
declare function eth:simpleInputSimpleOutputFunc($a as xs:double,$b as xs:integer) as xs:double {$a + $b};
declare function eth:seqInputSimpleOutputFunc($a as xs:string*) as xs:integer {1};
declare function eth:simpleInputSeqOutputFunc($a as xs:integer) as xs:integer* {($a - 1,$a,$a+1)};
declare function eth:seqInputSeqOutputFunc($a as xs:integer+) as xs:integer+ {$a};