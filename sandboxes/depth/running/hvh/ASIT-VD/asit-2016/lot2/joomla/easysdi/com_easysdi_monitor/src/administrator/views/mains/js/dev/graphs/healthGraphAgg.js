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

EasySDI_Mon.drawHealthGraphAgg = function (container, aStores, logRes,useSla,showInspireGraph){
	//Prepare graph options
	var options = {
		chart: {
			renderTo: container,
			defaultSeriesType: 'column'
		},
		title: {
			text: EasySDI_Mon.lang.getLocal('service health summary')
		},
		xAxis: {
			categories: []
		},
		yAxis: {
			min: 0,
			max: 100,
			title: {
			text: EasySDI_Mon.lang.getLocal('percentage')
		}
		},
		legend: {
			backgroundColor: '#FFFFFF',
			reversed: true
		},
		tooltip: {
			formatter: function() {
			return ''+
			this.series.name +': '+ this.y +'%';
		}
		},
		plotOptions: {
			series: {
			stacking: 'normal'
		}
		},
		series: []
	};

	//prepare graph data

	var avSeries = {
			name: EasySDI_Mon.lang.getLocal('available'),
			data: [],
			color: '#7dff9c'
	};
    var unavSeries = {
            name: EasySDI_Mon.lang.getLocal('unavailable'),
            data: [],
            color: '#ff7f7f'
     };
     var fSeries = {
        name: EasySDI_Mon.lang.getLocal('failure'),
        data: [],
        color: '#e2ff1d'
     };
     var otherSeries = {
        name: EasySDI_Mon.lang.getLocal('untested-unknown'),
        data: [],
        color: '#b3b3b3'
     };

	//push categories
	for ( var storeName in aStores)
	{
		if(typeof aStores[storeName] != 'function'){
			if(useSla)
			{
				if(!showInspireGraph)
				{
					options.xAxis.categories.push(storeName);//+EasySDI_Mon.lang.getLocal('h1 suffix'));
				}else
				{
					options.xAxis.categories.push(storeName);//+EasySDI_Mon.lang.getLocal('inspire suffix'));
				}
			}else
			{
				options.xAxis.categories.push(storeName+EasySDI_Mon.lang.getLocal('h24 suffix'));
				options.xAxis.categories.push(storeName+EasySDI_Mon.lang.getLocal('sla suffix'));
			}
		}
	}

	var avCountH24;
	var avCountSLA;
	var unavCount;
	var unavCountSP;
	var fCount;
	var fCountSP
	var otherCount;
	var otherCountSP;
	if(useSla)
	{
		//push series
		for ( var storeName in aStores)
		{
			if(typeof aStores[storeName] != 'function'){
				var aRec = aStores[storeName].getRange();
				avCountH24 = 0;
				avCountSLA = 0;
				unavCount = 0;
				unavCountSP = 0;
				fCount = 0;
				fCountSP = 0;
				otherCount = 0;
				otherCountSP = 0;
				//push percentiles
				for ( var i=0; i< aRec.length; i++ )
				{   
					avCountH24 += aRec[i].get('h1Availability');
					avCountSLA += aRec[i].get('inspireAvailability');
					
					unavCount += aRec[i].get('h1Unavailability');
					unavCountSP += aRec[i].get('inspireUnavailability');
					fCount += aRec[i].get('h1Failure');
					fCountSP += aRec[i].get('inspireFailure');
					otherCount += aRec[i].get('h1Untested');
					otherCountSP += aRec[i].get('inspireUntested');
				}
		      
				if(!showInspireGraph)
				{
					avSeries.data.push(Math.round((avCountH24/aRec.length) * 100)/100);
					unavSeries.data.push(Math.round((unavCount/aRec.length) * 100)/100);
					fSeries.data.push(Math.round((fCount/aRec.length) * 100)/100);
					otherSeries.data.push(Math.round((otherCount/aRec.length) * 100)/100);
				}else{
					avSeries.data.push(Math.round((avCountSLA/aRec.length) * 100)/100);
					unavSeries.data.push(Math.round((unavCountSP/aRec.length) * 100)/100);
					fSeries.data.push(Math.round((fCountSP/aRec.length) * 100)/100);
					otherSeries.data.push(Math.round((otherCountSP/aRec.length) * 100)/100);
				}
			}
		}
	}else
	{
				//push series
				for ( var storeName in aStores)
				{
					if(typeof aStores[storeName] != 'function'){
						var aRec = aStores[storeName].getRange();
						avCountH24 = 0;
						avCountSLA = 0;
						unavCount = 0;
						unavCountSP = 0;
						fCount = 0;
						fCountSP = 0;
						otherCount = 0;
						otherCountSP = 0;
						//push percentiles
						for ( var i=0; i< aRec.length; i++ )
						{   
							avCountH24 += aRec[i].get('h24Availability');
							avCountSLA += aRec[i].get('slaAvalabilty');
							unavCount += aRec[i].get('h24Unavailability');
							unavCountSP += aRec[i].get('slaUnavailability');
							fCount += aRec[i].get('h24Failure');
							fCountSP += aRec[i].get('slaFailure');
							otherCount += aRec[i].get('h24Untested');
							otherCountSP += aRec[i].get('slaUntested');
						}
						
						avSeries.data.push(Math.round( (avCountH24/aRec.length) * 100)/100);
						avSeries.data.push(Math.round((avCountSLA/aRec.length) * 100)/100);
						unavSeries.data.push(Math.round((unavCount/aRec.length) * 100)/100);
						unavSeries.data.push(Math.round((unavCountSP/aRec.length) * 100)/100);
						fSeries.data.push(Math.round((fCount/aRec.length) * 100)/100);
						fSeries.data.push(Math.round((fCountSP/aRec.length) * 100)/100);
						otherSeries.data.push(Math.round((otherCount/aRec.length) * 100)/100);
						otherSeries.data.push(Math.round((otherCountSP/aRec.length) * 100)/100);
					}
				}
	}

	options.series.push(otherSeries);
	options.series.push(unavSeries);
	options.series.push(fSeries);
	options.series.push(avSeries);
	//Output the graph
	var chart = new Highcharts.Chart(options);
	return chart;
};