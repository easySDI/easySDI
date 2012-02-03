(: A low-level SOAP call against the Google search API :)

let $input:=<SOAP-ENV:Envelope xmlns:SOAP-ENV='http://schemas.xmlsoap.org/soap/envelope/' xmlns:xsi='http://www.w3.org/2001/XMLSchema-instance' xmlns:xsd='http://www.w3.org/2001/XMLSchema'> 
    <SOAP-ENV:Body><tns:doSpellingSuggestion xmlns:tns='urn:GoogleSearch' SOAP-ENV:encodingStyle='http://schemas.xmlsoap.org/soap/encoding/'>
    <key xsi:type='xsd:string'>oIqddkdQFHIlwHMXPerc1KlNm+FDcPUf</key><phrase xsi:type='xsd:string'>web serviice</phrase></tns:doSpellingSuggestion></SOAP-ENV:Body></SOAP-ENV:Envelope>		
let $temp:= soap-call(xs:anyURI("http://api.google.com/search/beta2"),"POST","",$input)
    return $temp//return/text()
