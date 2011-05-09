(: A SOAP call against Google search API 
   Shows the WSDL as module import feature of MXQuery
:)

import module namespace ws="urn:GoogleSearch" at "http://api.google.com/GoogleSearch.wsdl" ;
let $result := ws:doGoogleSearch("oIqddkdQFHIlwHMXPerc1KlNm+FDcPUf", "ETH Zurich", 0, 10, fn:true(), "", fn:false(), "", "UTF-8", "UTF-8")
for $url in $result//URL 
where string($url) eq "http://www.ethz.ch/index_EN" 
return data($url) 

