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

EasySDI_Mon.drawResponseTimeGraph = function (container, aStores, logRes, tickInterval, jobRecord){
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
			return '<b>'+ this.series.name +'</b><br/>'+
			new Date(this.x).format('d-m-Y') +': '+ this.y +EasySDI_Mon.lang.getLocal('ms suffix');
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
			series.name = storeName+'[h24]';
			for ( var i=0; i< aRec.length; i++ )
			{   
				if(logRes == 'aggLogs')
					series.data.push([aRec[i].get('date').getTime(), Math.round(aRec[i].get('h24MeanRespTime') * 1000)]);
				else{
					var status = aRec[i].get('status');
					var color;
					switch (status){
                    case EasySDI_Mon.lang.getLocal('enum_status_available'):
						color = '#7dff9c';
						break;
                    case EasySDI_Mon.lang.getLocal('enum_status_failure'):
						color = '#e2ff1d';
						break;
                    case EasySDI_Mon.lang.getLocal('enum_status_unavailable'):
						color = '#ff7f7f';
						break;
                    case EasySDI_Mon.lang.getLocal('enum_status_untested'):
						color = '#b3b3b3';
						break;
					default: 
						color = '#b3b3b3';;
						break;
					}

					var point = {
							x: aRec[i].get('time').getTime(),
							y: Math.round(aRec[i].get('delay') * 1000),
							marker: {
						fillColor: color
					}
					};

					series.data.push(point);

					//series.data.push([aRec[i].get('time').getTime(), Math.round(aRec[i].get('delay') * 1000)]);
				}
			}
			//push this serie
			options.series.push(series);

			//add also sla response time for aggregate logs only
			if(logRes == 'aggLogs'){
				series = {data: []};
				series.name = storeName+EasySDI_Mon.lang.getLocal('sla suffix');
				for ( var i=0; i< aRec.length; i++ )
				{   
					series.data.push([aRec[i].get('date').getTime(), Math.round(aRec[i].get('slaMeanRespTime') * 1000)]);
				}
				options.series.push(series);
			}

		}
	}

	//Output the graph
	chart = new Highcharts.Chart(options);
	return chart;
};