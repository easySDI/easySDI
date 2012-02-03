service namespace mxqws="http://www.mxquery.org/webservice/" port:2002;

import service namespace ws1="namespace1" from "http://n.ethz.ch/~dagraf/download/wsdl/authWSDL.xml";
import service namespace ws2="namespace2" from "http://n.ethz.ch/~dagraf/download/wsdl/addrfinderWSLD.xml";
import service namespace ws3="namespace3" from "http://n.ethz.ch/~dagraf/download/wsdl/routefinderWSLD.xml";


declare execution sequential;

declare namespace tns = "http://www.arcwebservices.com/v2006_1";
declare namespace xns2 = "http://www.arcwebservices.com/v2006_1/com.esri.aws.dto/";
declare namespace xns3 = "http://www.arcwebservices.com/v2006_1/com.esri.aws.dto.geom/";
declare namespace xns4 = "http://www.arcwebservices.com/v2006_1/com.esri.aws.dto.routefinder/";
declare namespace xns5 = "http://www.arcwebservices.com/v2006_1/com.esri.aws.dto.mapimage/";

declare function mxqws:findRoute
	($street1, $nr1, $city1, $zip1, $country1, $street2, $nr2, $city2, $zip2, $country2) {
		declare $token;
	
	declare $addr1;
	declare $addr2;
	declare $addrOpt;
	declare $coord1;
	declare $coord2;
	
	declare $routeStops;
	declare $routeOpt;
	declare $route;
	declare $distance;
	declare $distanceUnits;
	declare $time;
	declare $mapURL;
	
	set $token := ws1:getToken((<tns:username>vanquish</tns:username>,<tns:password>erdlifresser</tns:password>))/tns:token;
	
	set $addr1 := <tns:address xmlns:ns2="http://www.arcwebservices.com/v2006_1/com.esri.aws.dto/">
	                <ns2:city>{data($city1)}</ns2:city>
	                <ns2:country>{data($country1)}</ns2:country>
	                <ns2:houseNumber>{data($nr1)}</ns2:houseNumber>
	                <ns2:intersection xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
	                    xsi:nil="true"/>
	                <ns2:postalCode>{data($zip1)}</ns2:postalCode>
	                <ns2:stateProvince xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
	                    xsi:nil="true"/>
	                <ns2:street>{data($street1)}</ns2:street>
	            </tns:address>;
	set $addr2 := <tns:address xmlns:ns2="http://www.arcwebservices.com/v2006_1/com.esri.aws.dto/">
	                <ns2:city>{data($city2)}</ns2:city>
	                <ns2:country>{data($country2)}</ns2:country>
	                <ns2:houseNumber>{data($nr2)}</ns2:houseNumber>
	                <ns2:intersection xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
	                    xsi:nil="true"/>
	                <ns2:postalCode>{data($zip2)}</ns2:postalCode>
	                <ns2:stateProvince xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
	                    xsi:nil="true"/>
	                <ns2:street>{data($street2)}</ns2:street>
	            </tns:address>;

	set $addrOpt := <tns:addressFinderOptions xmlns:ns3="http://www.arcwebservices.com/v2006_1/com.esri.aws.dto.addressfinder/">
		                <ns3:dataSource>ArcWeb:TA.Streets.EU</ns3:dataSource>
		                <ns3:extendedPostalCode>false</ns3:extendedPostalCode>
		                <ns3:partialAddress>false</ns3:partialAddress>
		                <ns3:resultSetRange xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
		                    xsi:nil="true"/>
		                <ns3:snapType xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:nil="true"/>
	            	</tns:addressFinderOptions>;
	            	
	set $coord1 := ws2:findLocationByAddress(($addr1,$addrOpt,$token))/tns:geocodeInfo/xns2:candidates/xns2:GeocodeCandidate/xns2:point;
	set $coord2 := ws2:findLocationByAddress(($addr2,$addrOpt,$token))/tns:geocodeInfo/xns2:candidates/xns2:GeocodeCandidate/xns2:point;
	
	set $routeStops := <tns:routeStops xmlns:ns1="http://www.arcwebservices.com/v2006_1/com.esri.aws.dto.routefinder/"
	    				  	 xmlns:ns2="http://www.arcwebservices.com/v2006_1/com.esri.aws.dto.geom/">
	                <ns1:RouteStop>
	                    <ns1:desc>start</ns1:desc>
	                    <ns1:point>
	                        <ns2:coordSys xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
	                            xsi:nil="true"/>
	                        <ns2:x>{data($coord1/xns3:x)}</ns2:x>
	                        <ns2:y>{data($coord1/xns3:y)}</ns2:y>
	                    </ns1:point>
	                </ns1:RouteStop>
	                <ns1:RouteStop>
	                    <ns1:desc>end</ns1:desc>
	                    <ns1:point>
	                        <ns2:coordSys xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
	                            xsi:nil="true"/>
	                        <ns2:x>{data($coord2/xns3:x)}</ns2:x>
	                        <ns2:y>{data($coord2/xns3:y)}</ns2:y>
	                    </ns1:point>
	                </ns1:RouteStop>
	            </tns:routeStops>;  
	         
	set $routeOpt := <tns:routeFinderOptions  xmlns:ns1="http://www.arcwebservices.com/v2006_1/com.esri.aws.dto.routefinder/"
									 xmlns:ns4="http://www.arcwebservices.com/v2006_1/com.esri.aws.dto.mapimage/">
	                <ns1:avoidTraffic>false</ns1:avoidTraffic>
	                <ns1:dataSource>ArcWeb:TA.Streets.EU</ns1:dataSource>
	                <ns1:language xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:nil="true"/>
	                <ns1:returnDirections>true</ns1:returnDirections>
	                <ns1:returnGeometry>false</ns1:returnGeometry>
	                <ns1:returnMap>true</ns1:returnMap>
	                <ns1:returnTurnByTurnMaps>false</ns1:returnTurnByTurnMaps>
	                <ns1:routeDisplayOptions xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
	                    xsi:nil="true"/>
	                <ns1:routeMapOptions>
	                    <ns4:backgroundColor xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
	                        xsi:nil="true"/>
	                    <ns4:circles xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
	                        xsi:nil="true"/>
	                    <ns4:dataSource xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
	                        xsi:nil="true"/>
	                    <ns4:displayLayers xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
	                        xsi:nil="true"/>
	                    <ns4:lines xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:nil="true"/>
	                    <ns4:mapImageFormat>png</ns4:mapImageFormat>
	                    <ns4:mapImageSize>
	                        <ns4:height>1000</ns4:height>
	                        <ns4:width>1000</ns4:width>
	                    </ns4:mapImageSize>
	                    <ns4:mapLegend xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
	                        xsi:nil="true"/>
	                    <ns4:markers xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
	                        xsi:nil="true"/>
	                    <ns4:outputCoordSys xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
	                        xsi:nil="true"/>
	                    <ns4:polygons xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
	                        xsi:nil="true"/>
	                    <ns4:returnLayers>false</ns4:returnLayers>
	                    <ns4:scaleBars xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
	                        xsi:nil="true"/>
	                    <ns4:styleSheet xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
	                        xsi:nil="true"/>
	                </ns1:routeMapOptions>
	                <ns1:routeOptions xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
	                    xsi:nil="true"/>
	                <ns1:trafficDataSource xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
	                    xsi:nil="true"/>
	                <ns1:trafficSeverity>0</ns1:trafficSeverity>
	                <ns1:turnByTurnMapOptions xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
	                    xsi:nil="true"/>
	                <ns1:units xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:nil="true"/>
	            </tns:routeFinderOptions>;
	
	
	set $route := ws3:findRoute(($routeStops,$routeOpt,$token));
	
	set $distance := $route/tns:out/xns4:totalDesc/xns4:numericDistance;
	set $distanceUnits := $route/tns:out/xns4:totalDesc/xns4:distanceUnits;
	set $time := $route/tns:out/xns4:totalDesc/xns4:totalTime;
	set $mapURL := $route/tns:out/xns4:routeMap/xns5:mapURL;
	
	<way distance="{concat(data($distance),data($distanceUnits))}" time="{data($time)}" mapurl="{data($mapURL)}">
	{
		for $descriptiveDirections in $route/tns:out/xns4:segmentDescs/xns4:SegmentDesc/xns4:descriptiveDirections
		return
			<step>{data($descriptiveDirections)}</step>
	}
	</way>
};