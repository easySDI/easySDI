
 
 (: A low-level SOAP call against the Google search API :)
module namespace eth="http://inf.ethz.ch/";
declare function eth:testsoap($a as xs:string) {
let $input:=<soapenv:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:urn="urn:Hello">
   <soapenv:Header/>
   <soapenv:Body>
      <doHello >
 <doHello >\$a
 </doHello >
 </doHello >
   </soapenv:Body>
</soapenv:Envelope>
let $temp:= soap-call(xs:anyURI("http://localhost/sdiv2/testwsdl/hello_server.php"),"POST","",$input)
    return $temp//return/text()
};