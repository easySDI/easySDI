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

EasySDI_Mon.drawHealthLineGraph = function(container, aStores, logRes, tickInterval){
	//Prepare graph options
	var options = {
		chart: {
				renderTo: container,
				marginRight: 130,
				zoomType: 'x'
			//,
			//defaultSeriesType: 'spline'
		},
		title: {
			text: EasySDI_Mon.lang.getLocal('service health'),
			x: -20 //center
		},
		xAxis: {
			title: EasySDI_Mon.lang.getLocal('grid header dateTime'),
			type: 'datetime',
			maxZoom: tickInterval / 10,
			// one day interval
			tickInterval: tickInterval
		},
		yAxis: {
			title: {
			text:EasySDI_Mon.lang.getLocal('percentage')
		}
		},
		tooltip: {
			formatter: function() {
			return '<b>'+ this.series.name +'</b><br/>'+
			new Date(this.x).format('d-m-Y') +': '+ this.y +'[%]';
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
			//H24
			series.name = storeName+EasySDI_Mon.lang.getLocal('h24 suffix');
			for ( var i=0; i< aRec.length; i++ )
			{   
				series.data.push([aRec[i].get('date').getTime(), Math.round(aRec[i].get('h24Availability'))]);
			}
			options.series.push(series);

			//SLA
			series = {data: []};
			series.name = storeName+EasySDI_Mon.lang.getLocal('sla suffix');
			for ( var i=0; i< aRec.length; i++ )
			{   
				series.data.push([aRec[i].get('date').getTime(), Math.round(aRec[i].get('slaAvalabilty'))]);
			}
			options.series.push(series);

		}
	}

	//Output the graph
	var chart = new Highcharts.Chart(options);
	return chart;

};