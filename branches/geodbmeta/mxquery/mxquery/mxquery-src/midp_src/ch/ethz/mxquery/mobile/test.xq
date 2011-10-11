import service namespace ws="www.namespace.ch" from "http://api.google.com/GoogleSearch.wsdl";

declare execution sequential;

declare variable $result;
declare variable $query;

set $query := "David Graf";
set $result := ws:doGoogleSearch("oIqddkdQFHIlwHMXPerc1KlNm+FDcPUf", $query, 0, 10, fn:true(), "", fn:false(), "", "UTF-8", "UTF-8");

<results query="{$query}">
{
	for $url in $result/resultElements/item/URL
	return data($url)
}
</results>