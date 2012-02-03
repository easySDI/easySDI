import module namespace ggl="urn:GoogleSearch" at "http://api.google.com/GoogleSearch.wsdl"; 
import module namespace dct="http://services.aonaware.com/webservices/" at "http://services.aonaware.com/DictService/DictService.asmx?WSDL"; 

let $spelledWord := ggl:doSpellingSuggestion("oIqddkdQFHIlwHMXPerc1KlNm+FDcPUf",$input)
return 
     if (fn:string-length($spelledWord)=0) 
           then (dct:Define(<Define xmlns="http://services.aonaware.com/webservices/"><word>{$input}</word></Define>)//dct:WordDefinition)
            else (dct:Define(<Define xmlns="http://services.aonaware.com/webservices/"><word>{$spelledWord}</word></Define>)//dct:WordDefinition)
