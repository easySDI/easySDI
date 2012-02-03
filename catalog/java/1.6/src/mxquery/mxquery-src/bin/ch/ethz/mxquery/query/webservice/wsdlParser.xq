declare namespace soap = "http://schemas.xmlsoap.org/wsdl/soap/";
declare namespace ns = "http://schemas.xmlsoap.org/wsdl/";
declare namespace s = "http://www.w3.org/2001/XMLSchema";


declare variable $wsdl external;
declare variable $servicename external;
declare variable $endpoint external;

(:
declare variable $wsdl := doc("xquerypTests/normal/inputData/testWSDL2.xml");
:)
$wsdl/ns:definitions/ns:types/*,

<services>
{
(: considering the presence or absence of the 'service name' and 'enpoint name' there are four different cases  which three of the raise error:)
if ($servicename ="" and count($wsdl/ns:definitions/ns:service) > 1) then
(:  case1: no service name is specified by user and there more than one available :)
"Error1: please specify the service name"
else if ($servicename ="") then
    if ($endpoint ="" and count($wsdl/ns:definitions/ns:service/ns:port/soap:address) > 1) then
        (: case2: there is just one service name available and system chose that one. But it has more tha one available endpoint  :)
        "Error2: please specify the endpoint"
    else if ($endpoint = "") then
	for $service in $wsdl/ns:definitions/ns:service/ns:port
	let $tns := $wsdl/ns:definitions/@targetNamespace
	where $service/soap:address
	return
		<service address="{$service/soap:address/@location}">
		{
			let $binding := $wsdl/ns:definitions/ns:binding[@name eq fn:local-name-from-QName(fn:QName("http://schemas.xmlsoap.org/wsdl/",$service/@binding))]
			let $portType := $wsdl/ns:definitions/ns:portType[@name eq fn:local-name-from-QName(fn:QName("http://schemas.xmlsoap.org/wsdl/",$binding/@type))]
			for $operation in $binding/ns:operation
			return
				let $portOperation := $portType/ns:operation[@name eq $operation/@name]
				let $requestMsg := $wsdl/ns:definitions/ns:message[@name eq fn:local-name-from-QName(fn:QName("http://schemas.xmlsoap.org/wsdl/",$portOperation/ns:input/@message))]
				let $responseMsg := $wsdl/ns:definitions/ns:message[@name eq fn:local-name-from-QName(fn:QName("http://schemas.xmlsoap.org/wsdl/",$portOperation/ns:output/@message))]
				return
					<function
						name="{$operation/@name}"
						style ="{if (fn:exists($operation/soap:operation/@style)) then $operation/soap:operation/@style else $binding/soap:binding/@style}"
						soapaction="{$operation/soap:operation/@soapAction}"
						inputnamespace="{$tns}"
						inputencoding="{$operation/ns:input/soap:body/@encodingStyle}"
						outputnamespace="{$tns}"
						outputencoding="{$operation/ns:output/soap:body/@encodingStyle}"
						returnName="{
						    if ((count($responseMsg/ns:part) ) > 1) then 
						        "multiple-result"
							else if (exists($responseMsg/ns:part/@element)) then 
    								$responseMsg/ns:part/@element
    							        else 
    								$responseMsg/ns:part/@name}"  
						returnType="{
						    if ((count($responseMsg/ns:part) ) > 1) then 
						        "multiple-result"
							else if (exists($responseMsg/ns:part/@element)) then 
            							               ""
    							        else 
    								$responseMsg/ns:part/@type    								
						}">
					{
						for $param in $requestMsg/ns:part
						return
							if (exists($param/@type)) then (:the parameter has a type:)							                     
								<param name="{$param/@name}" type="{$param/@type}" />
							else if (exists($param/@element)) then (:the parameter is an element:)							            
								<param name="{$param/@element}" type="noType" />
							else         
							                <param name="" type=""/>								
					}
					</function>
		}
		</service>
        else
	for $service in $wsdl/ns:definitions/ns:service/ns:port
	let $tns := $wsdl/ns:definitions/@targetNamespace
	where $service[@name = $endpoint]
	return
		<service address="{$service/soap:address/@location}">
		{
			let $binding := $wsdl/ns:definitions/ns:binding[@name eq fn:local-name-from-QName(fn:QName("http://schemas.xmlsoap.org/wsdl/",$service/@binding))]
			let $portType := $wsdl/ns:definitions/ns:portType[@name eq fn:local-name-from-QName(fn:QName("http://schemas.xmlsoap.org/wsdl/",$binding/@type))]
			for $operation in $binding/ns:operation
			return
				let $portOperation := $portType/ns:operation[@name eq $operation/@name]
				let $requestMsg := $wsdl/ns:definitions/ns:message[@name eq fn:local-name-from-QName(fn:QName("http://schemas.xmlsoap.org/wsdl/",$portOperation/ns:input/@message))]
				let $responseMsg := $wsdl/ns:definitions/ns:message[@name eq fn:local-name-from-QName(fn:QName("http://schemas.xmlsoap.org/wsdl/",$portOperation/ns:output/@message))]
				return
					<function
						name="{$operation/@name}"
						style ="{if (fn:exists($operation/soap:operation/@style)) then $operation/soap:operation/@style else $binding/soap:binding/@style}"
						soapaction="{$operation/soap:operation/@soapAction}"
						inputnamespace="{$tns}"
						inputencoding="{$operation/ns:input/soap:body/@encodingStyle}"
						outputnamespace="{$tns}"
						outputencoding="{$operation/ns:output/soap:body/@encodingStyle}"
						returnName="{
						    if ((count($responseMsg/ns:part) ) > 1) then 
						        "multiple-result"
							else if (exists($responseMsg/ns:part/@element)) then 
    								$responseMsg/ns:part/@element
    							        else 
    								$responseMsg/ns:part/@name}"  
						returnType="{
						    if ((count($responseMsg/ns:part) ) > 1) then 
						        "multiple-result"
							else if (exists($responseMsg/ns:part/@element)) then 
            							               ""
    							        else 
    								$responseMsg/ns:part/@type    								
						}">
					{
						for $param in $requestMsg/ns:part
						return
							if (exists($param/@type)) then (:the parameter has a type:)							                     
								<param name="{$param/@name}" type="{$param/@type}" />
							else if (exists($param/@element)) then (:the parameter is an element:)							            
								<param name="{$param/@element}" type="noType" />
							else         
							                <param name="" type=""/>								
					}
					</function>
		}
		</service>	
 else 
     if ($endpoint ="" and count($wsdl/ns:definitions/ns:service[@name = $servicename]/ns:port/soap:address) > 1) then
        (: case 3: the service name has been specified by user, but it has more than one available endpoints and he forgot to specify one of them :)
        "Error2: please specify the endpoint"
    else if ($endpoint = "") then
	for $service in $wsdl/ns:definitions/ns:service[@name = $servicename]/ns:port
	let $tns := $wsdl/ns:definitions/@targetNamespace
	where $service/soap:address
	return
		<service address="{$service/soap:address/@location}">
		{
			let $binding := $wsdl/ns:definitions/ns:binding[@name eq fn:local-name-from-QName(fn:QName("http://schemas.xmlsoap.org/wsdl/",$service/@binding))]
			let $portType := $wsdl/ns:definitions/ns:portType[@name eq fn:local-name-from-QName(fn:QName("http://schemas.xmlsoap.org/wsdl/",$binding/@type))]
			for $operation in $binding/ns:operation
			return
				let $portOperation := $portType/ns:operation[@name eq $operation/@name]
				let $requestMsg := $wsdl/ns:definitions/ns:message[@name eq fn:local-name-from-QName(fn:QName("http://schemas.xmlsoap.org/wsdl/",$portOperation/ns:input/@message))]
				let $responseMsg := $wsdl/ns:definitions/ns:message[@name eq fn:local-name-from-QName(fn:QName("http://schemas.xmlsoap.org/wsdl/",$portOperation/ns:output/@message))]
				return
					<function
						name="{$operation/@name}"
						style ="{if (fn:exists($operation/soap:operation/@style)) then $operation/soap:operation/@style else $binding/soap:binding/@style}"
						soapaction="{$operation/soap:operation/@soapAction}"
						inputnamespace="{$tns}"
						inputencoding="{$operation/ns:input/soap:body/@encodingStyle}"
						outputnamespace="{$tns}"
						outputencoding="{$operation/ns:output/soap:body/@encodingStyle}"
						returnName="{
						    if ((count($responseMsg/ns:part) ) > 1) then 
						        "multiple-result"
							else if (exists($responseMsg/ns:part/@element)) then 
    								$responseMsg/ns:part/@element
    							        else 
    								$responseMsg/ns:part/@name}"  
						returnType="{
						    if ((count($responseMsg/ns:part) ) > 1) then 
						        "multiple-result"
							else if (exists($responseMsg/ns:part/@element)) then 
            							               ""
    							        else 
    								$responseMsg/ns:part/@type    								
						}">
					{
						for $param in $requestMsg/ns:part
						return
							if (exists($param/@type)) then (:the parameter has a type:)							                     
								<param name="{$param/@name}" type="{$param/@type}" />
							else if (exists($param/@element)) then (:the parameter is an element:)							            
								<param name="{$param/@element}" type="noType" />
							else         
							                <param name="" type=""/>								
					}
					</function>
		}
		</service>
        else
	for $service in $wsdl/ns:definitions/ns:service[@name = $servicename]/ns:port
	let $tns := $wsdl/ns:definitions/@targetNamespace
	where $service[@name = $endpoint]
	return
		<service address="{$service/soap:address/@location}">
		{
			let $binding := $wsdl/ns:definitions/ns:binding[@name eq fn:local-name-from-QName(fn:QName("http://schemas.xmlsoap.org/wsdl/",$service/@binding))]
			let $portType := $wsdl/ns:definitions/ns:portType[@name eq fn:local-name-from-QName(fn:QName("http://schemas.xmlsoap.org/wsdl/",$binding/@type))]
			for $operation in $binding/ns:operation
			return
				let $portOperation := $portType/ns:operation[@name eq $operation/@name]
				let $requestMsg := $wsdl/ns:definitions/ns:message[@name eq fn:local-name-from-QName(fn:QName("http://schemas.xmlsoap.org/wsdl/",$portOperation/ns:input/@message))]
				let $responseMsg := $wsdl/ns:definitions/ns:message[@name eq fn:local-name-from-QName(fn:QName("http://schemas.xmlsoap.org/wsdl/",$portOperation/ns:output/@message))]
				return
					<function
						name="{$operation/@name}"
						style ="{if (fn:exists($operation/soap:operation/@style)) then $operation/soap:operation/@style else $binding/soap:binding/@style}"
						soapaction="{$operation/soap:operation/@soapAction}"
						inputnamespace="{$tns}"
						inputencoding="{$operation/ns:input/soap:body/@encodingStyle}"
						outputnamespace="{$tns}"
						outputencoding="{$operation/ns:output/soap:body/@encodingStyle}"
						returnName="{
						    if ((count($responseMsg/ns:part) ) > 1) then 
						        "multiple-result"
							else if (exists($responseMsg/ns:part/@element)) then 
    								$responseMsg/ns:part/@element
    							        else 
    								$responseMsg/ns:part/@name}"  
						returnType="{
						    if ((count($responseMsg/ns:part) ) > 1) then 
						        "multiple-result"
							else if (exists($responseMsg/ns:part/@element)) then 
            							               ""
    							        else 
    								$responseMsg/ns:part/@type    								
						}">
					{
						for $param in $requestMsg/ns:part
						return
							if (exists($param/@type)) then (:the parameter has a type:)							                     
								<param name="{$param/@name}" type="{$param/@type}" />
							else if (exists($param/@element)) then (:the parameter is an element:)							            
								<param name="{$param/@element}" type="noType" />
							else         
							                <param name="" type=""/>								
					}
					</function>
		}
		</service>
}
</services>