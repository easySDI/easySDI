/**
 * EasySDI, a solution to implement easily any spatial data infrastructure
 * Copyright (C) EasySDI Community 
 * For more information : www.easysdi.org
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see http://www.gnu.org/licenses/gpl.html.
 */

Ext.namespace("EasySDI_Mon");

EasySDI_Mon.drawResponseTimeGraph = function (container, aStores, logRes, tickInterval, jobRecord,useSla,showInspireGraph){
	//Prepare graph options
	var options = {
		colors: [
			         '#000000', 
			         '#414141', 
			         '#7b7b7b', 
			         '#874900', 
			         '#855720', 
			         '#7e5e39', 
			         '#7e6c57'
			         ],
	    chart: {
			renderTo: container,
			marginRight: 130,
			zoomType: 'x'
			//,
			//defaultSeriesType: 'spline'
		},
		title: {
			text: EasySDI_Mon.lang.getLocal('response time graph title'),
			x: -20 //center
		},
		xAxis: {
			title: EasySDI_Mon.lang.getLocal('dateTime'),
			type: 'datetime',
			maxZoom: tickInterval / 10,
			// one day interval
			tickInterval: tickInterval
		},
		yAxis: {
			title: {
			text:EasySDI_Mon.lang.getLocal('response time')+' '+EasySDI_Mon.lang.getLocal('ms suffix')
		}
		,
		min: 0, 
		//Sets the maxY to 4/3 the timeout
		max: jobRecord.get('timeout')*1000*1.3333333,
		minorGridLineWidth: 0, 
		gridLineWidth: 0,
		alternateGridColor: null,
		plotBands: [{ // Available
			from: 0,
			//set a colored area for the timout value
			to: jobRecord.get('timeout')*1000,
			color: 'rgba(68, 170, 213, 0.1)'
		}]},
		labels: {
			items: [{
				html: EasySDI_Mon.lang.getLocal('area within timeout'),
				style: {
				left: '10px',
				top: '240px'
			}
			}]
		},
		tooltip: {
			formatter: function() {
				var tip =  
					"<b>"+ this.series.name +"</b><br/>"+
					new Date(this.x).format('d-m-Y H:i:s') +" -> "+ this.y + EasySDI_Mon.lang.getLocal('ms suffix')+" <br/>";
					
					if(this.point.log == "aggLogs")
					{
						if(useSla)
						{
							if(this.point.normalGraph)
							{
								tip+= "<b>"+EasySDI_Mon.lang.getLocal('tooltip AVAILABILITY')+":</b> "+(Math.round(this.point.data.data.h1Availability *100)/100)+"%<br/>";
								tip+= "<b>"+EasySDI_Mon.lang.getLocal('tooltip UNAVAILABILITY')+":</b> "+(Math.round(this.point.data.data.h1Unavailability * 100)/100)+"%<br/>";
								tip+= "<b>"+EasySDI_Mon.lang.getLocal('tooltip FAILURE')+":</b> "+(Math.round(this.point.data.data.h1Failure * 100)/100)+"%<br/>";
								tip+= "<b>"+EasySDI_Mon.lang.getLocal('tooltip UNTESTED')+":</b> "+(Math.round(this.point.data.data.h1Untested * 100)/100)+"%<br/>";
								tip+= "<b>"+EasySDI_Mon.lang.getLocal('tooltip NB_CONN_ERRORS')+":</b> "+this.point.data.data.h1NbConnErrors+"<br/>";
								tip+= "<b>"+EasySDI_Mon.lang.getLocal('tooltip NB_BIZ_ERRORS')+":</b> "+this.point.data.data.h1NbBizErrors+"<br/>";
								tip+= "<b>"+EasySDI_Mon.lang.getLocal('tooltip MaxRespTime')+"</b> -> "+Math.round(this.point.data.data.h1MaxRespTime * 1000) + EasySDI_Mon.lang.getLocal('ms suffix')+"<br/>";
								tip+= "<b>"+EasySDI_Mon.lang.getLocal('tooltip MinRespTime')+"</b> -> "+Math.round(this.point.data.data.h1MinRespTime * 1000) + EasySDI_Mon.lang.getLocal('ms suffix')+"<br/>";
							}else
							{
								tip+= "<b>"+EasySDI_Mon.lang.getLocal('tooltip AVAILABILITY')+":</b> "+(Math.round(this.point.data.data.inspireAvailability * 100)/100)+"%<br/>";
								tip+= "<b>"+EasySDI_Mon.lang.getLocal('tooltip UNAVAILABILITY')+":</b> "+(Math.round(this.point.data.data.inspireUnavailability * 100)/100)+"%<br/>";
								tip+= "<b>"+EasySDI_Mon.lang.getLocal('tooltip FAILURE')+":</b> "+(Math.round(this.point.data.data.inspireFailure * 100)/100)+"%<br/>";
								tip+= "<b>"+EasySDI_Mon.lang.getLocal('tooltip UNTESTED')+":</b> "+(Math.round(this.point.data.data.inspireUntested * 100)/100)+"%<br/>";
								tip+= "<b>"+EasySDI_Mon.lang.getLocal('tooltip NB_CONN_ERRORS')+":</b> "+this.point.data.data.inspireNbConnErrors+"<br/>";
								tip+= "<b>"+EasySDI_Mon.lang.getLocal('tooltip NB_BIZ_ERRORS')+":</b> "+this.point.data.data.inspireNbBizErrors+"<br/>";
								tip+= "<b>"+EasySDI_Mon.lang.getLocal('tooltip MaxRespTime')+"</b> -> "+Math.round(this.point.data.data.inspireMaxRespTime * 1000) + EasySDI_Mon.lang.getLocal('ms suffix')+"<br/>";
								tip+= "<b>"+EasySDI_Mon.lang.getLocal('tooltip MinRespTime')+"</b> -> "+Math.round(this.point.data.data.inspireMinRespTime * 1000) + EasySDI_Mon.lang.getLocal('ms suffix')+"<br/>";	
							}
						}
						else
						{
							if(this.point.normalGraph)
							{
								tip+= "<b>"+EasySDI_Mon.lang.getLocal('tooltip AVAILABILITY')+":</b> "+(Math.round(this.point.data.data.h24Availability *100)/100)+"%<br/>";
								tip+= "<b>"+EasySDI_Mon.lang.getLocal('tooltip UNAVAILABILITY')+":</b> "+(Math.round(this.point.data.data.h24Unavailability *100)/100)+"%<br/>";
								tip+= "<b>"+EasySDI_Mon.lang.getLocal('tooltip FAILURE')+":</b> "+(Math.round(this.point.data.data.h24Failure * 100)/100)+"%<br/>";
								tip+= "<b>"+EasySDI_Mon.lang.getLocal('tooltip UNTESTED')+":</b> "+(Math.round(this.point.data.data.h24Untested * 100)/100)+"%<br/>";
								tip+= "<b>"+EasySDI_Mon.lang.getLocal('tooltip NB_CONN_ERRORS')+":</b> "+this.point.data.data.h24NbConnErrors+"<br/>";
								tip+= "<b>"+EasySDI_Mon.lang.getLocal('tooltip NB_BIZ_ERRORS')+":</b> "+this.point.data.data.h24NbBizErrors+"<br/>";
								tip+= "<b>"+EasySDI_Mon.lang.getLocal('tooltip MaxRespTime')+"</b> -> "+Math.round(this.point.data.data.h24MaxRespTime * 1000) + EasySDI_Mon.lang.getLocal('ms suffix')+"<br/>";
								tip+= "<b>"+EasySDI_Mon.lang.getLocal('tooltip MinRespTime')+"</b> -> "+Math.round(this.point.data.data.h24MinRespTime * 1000) + EasySDI_Mon.lang.getLocal('ms suffix')+"<br/>";		
							}else
							{
								tip+= "<b>"+EasySDI_Mon.lang.getLocal('tooltip AVAILABILITY')+":</b> "+(Math.round(this.point.data.data.slaAvailability *100)/100)+"%<br/>";
								tip+= "<b>"+EasySDI_Mon.lang.getLocal('tooltip UNAVAILABILITY')+":</b> "+(Math.round(this.point.data.data.slaUnavailability *100)/100)+"%<br/>";
								tip+= "<b>"+EasySDI_Mon.lang.getLocal('tooltip FAILURE')+":</b> "+(Math.round(this.point.data.data.slaFailure *100)/100)+"%<br/>";
								tip+= "<b>"+EasySDI_Mon.lang.getLocal('tooltip UNTESTED')+":</b> "+(Math.round(this.point.data.data.slaUntested *100)/100)+"%<br/>";
								tip+= "<b>"+EasySDI_Mon.lang.getLocal('tooltip NB_CONN_ERRORS')+":</b> "+this.point.data.data.slaNbConnErrors+"<br/>";
								tip+= "<b>"+EasySDI_Mon.lang.getLocal('tooltip NB_BIZ_ERRORS')+":</b> "+this.point.data.data.slaNbBizErrors+"<br/>";
								tip+= "<b>"+EasySDI_Mon.lang.getLocal('tooltip MaxRespTime')+"</b> -> "+Math.round(this.point.data.data.slaMaxRespTime * 1000) + EasySDI_Mon.lang.getLocal('ms suffix')+"<br/>";
								tip+= "<b>"+EasySDI_Mon.lang.getLocal('tooltip MinRespTime')+"</b> -> "+Math.round(this.point.data.data.slaMinRespTime * 1000) + EasySDI_Mon.lang.getLocal('ms suffix')+"<br/>";	
				
							}
						}
					}else
					{
						tip+= "<b>"+EasySDI_Mon.lang.getLocal('tooltip response size')+"</b>: "+Math.round(this.point.data.data.size)+" bytes<br/>";
						// Test for summmary
						if(this.point.data.data.maxTime)
						{
						 	tip+= "<b>"+EasySDI_Mon.lang.getLocal('tooltip MaxRespTime')+"</b>: "+ Math.round(this.point.data.data.maxTime * 1000) + EasySDI_Mon.lang.getLocal('ms suffix')+"<br/>";	
						}else
						{
							if(this.point.data.data.statusCode.toLowerCase() == "unavailable")
							{
								tip+= "<b>"+EasySDI_Mon.lang.getLocal('tooltip http statuscode')+"</b>: "+ this.point.data.data.httpCode+"<br/>";
								tip+= "<b>"+EasySDI_Mon.lang.getLocal('tooltip response message')+"</b>: "+ this.point.data.data.message+"<br/>";
							}
							if(this.point.data.data.statusCode.toLowerCase() == "out_of_order")// failed
							{
								tip+= "<b>"+EasySDI_Mon.lang.getLocal('tooltip response message')+"</b>: "+ this.point.data.data.message;
							}
						}
					}
				return tip;
			}
		},
		legend: {
			layout: 'vertical',
			align: 'right',
			verticalAlign: 'top',
			x: -10,
			y: 100,
			borderWidth: 0
		},
		series: []
	};

	//prepare graph data
	var series;
	for ( var storeName in aStores)
	{
		if(typeof aStores[storeName] != 'function'){
			var aRec = aStores[storeName].getRange();
			series = {data: []};
			//add h24 or delay response time
			if(useSla)
			{
				series.name = storeName;//+EasySDI_Mon.lang.getLocal('h1 suffix');
			}else
			{
				series.name = storeName+'[h24]';
			}
			
			for ( var i=0; i< aRec.length; i++ )
			{   
				if(logRes == 'aggLogs')
				{
					var point;
					if(useSla)
					{
						point = {
								x: aRec[i].get('date').getTime(),
								y: Math.round(aRec[i].get('h1MeanRespTime') * 1000) != -1 ? Math.round(aRec[i].get('h1MeanRespTime') * 1000) : 0,
								data: aRec[i],
								log: logRes,
								normalGraph: true
							};
					}else
					{
						point = {
								x: aRec[i].get('date').getTime(),
								y: Math.round(aRec[i].get('h24MeanRespTime') * 1000) != -1 ? Math.round(aRec[i].get('h24MeanRespTime') * 1000) : 0,
								data: aRec[i],
								log: logRes,
								normalGraph: true
							};
					}
					if(useSla && showInspireGraph)
					{
						// No need to push this graph
					}else
					{
						series.data.push(point);
					}
					
				
				}
				else{
					var status = aRec[i].get('statusCode');
					var color;
					switch (status){
                    case 'AVAILABLE':
						color = '#7dff9c';
						break;
                    case 'OUT_OF_ORDER':
						color = '#e2ff1d';
						break;
                    case 'UNAVAILABLE':
						color = '#ff7f7f';
						break;
                    case 'NOT_TESTED':
						color = '#b3b3b3';
						break;
					default: 
						color = '#b3b3b3';
						break;
					}
					var point = {
							x: aRec[i].get('time').getTime(),
							y: Math.round(aRec[i].get('delay') * 1000) != -1 ? Math.round(aRec[i].get('delay') * 1000) : 0,
							marker: {
								fillColor: color
							},
							data: aRec[i], // record info for tooltip
							log: logRes,
							normalGraph: true
					};
					series.data.push(point);
					//series.data.push([aRec[i].get('time').getTime(), Math.round(aRec[i].get('delay') * 1000)]);
				}
			}
			//push this serie
			if(useSla && showInspireGraph && logRes == 'aggLogs')
			{
				// No need to push this graph
			}else
			{
				options.series.push(series);
			}
			
			if(logRes == 'aggLogs'){
				series = {data: []};
				if(useSla)
				{
					series.name = storeName; // +EasySDI_Mon.lang.getLocal('inspire suffix');
				}else
				{
					series.name = storeName+EasySDI_Mon.lang.getLocal('sla suffix');
				}
				
				for ( var i=0; i< aRec.length; i++ )
				{   
					var point;
					if(useSla)
					{
						point = {
								x: aRec[i].get('date').getTime(),
								y: Math.round(aRec[i].get('inspireMeanRespTime') * 1000) != -1 ? Math.round(aRec[i].get('inspireMeanRespTime') * 1000) : 0,
								data: aRec[i],
								log: logRes,
								normalGraph: false
						};
					}else
					{
						point = {
								x: aRec[i].get('date').getTime(),
								y: Math.round(aRec[i].get('slaMeanRespTime') * 1000) != -1 ? Math.round(aRec[i].get('slaMeanRespTime') * 1000) : 0,
								data: aRec[i],
								log: logRes,
								normalGraph: false
						};
					}
					if(useSla && !showInspireGraph)
					{
						// No need to push this graph
					}else
					{
						series.data.push(point);
					}
									
					//series.data.push([aRec[i].get('date').getTime(), Math.round(aRec[i].get('slaMeanRespTime') * 1000)]);
				}
				if(useSla && !showInspireGraph)
				{
					// No need to push this graph
				}else{
					options.series.push(series);
				}
				
			}

		}
	}

	//Output the graph
	var chart = new Highcharts.Chart(options);
	return chart;
};