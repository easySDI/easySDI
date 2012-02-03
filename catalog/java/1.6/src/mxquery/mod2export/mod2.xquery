module namespace eth="http://www.mxquery.org/wsdl/mod2/";
declare variable $eth:sequenceVariable as xs:integer* := (15,30);
declare function eth:nodeSeqResFunc() as element()*  {(<a><a2 x='yesk'>kyumars joox</a2></a>,<b>sheykh</b>,<c>esmaili</c>)};
declare function eth:atomicSeqResFunc() as xs:string *{('kyumars sheykh','esmaili')};
