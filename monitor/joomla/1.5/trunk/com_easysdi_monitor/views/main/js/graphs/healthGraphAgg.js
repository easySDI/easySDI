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

EasySDI_Mon.drawHealthGraphAgg = function (container, aStores, logRes){
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
             
	     //push categories
	     for ( var storeName in aStores)
             {
		 if(typeof aStores[storeName] != 'function'){
		    options.xAxis.categories.push(storeName+EasySDI_Mon.lang.getLocal('h24 suffix'));
		    options.xAxis.categories.push(storeName+EasySDI_Mon.lang.getLocal('sla suffix'));
		 }
	     }
	     
	     var avCountH24;
	     var avCountSLA;
	     //push series
             for ( var storeName in aStores)
             {
		 if(typeof aStores[storeName] != 'function'){
	            var aRec = aStores[storeName].getRange();
		    avCountH24 = 0;
		    avCountSLA = 0;
                    //push percentiles
                    for ( var i=0; i< aRec.length; i++ )
                    {   
			avCountH24 += aRec[i].get('h24Availability');
			avCountSLA += aRec[i].get('slaAvalabilty');
		    }
		    avSeries.data.push(Math.round(avCountH24/aRec.length));
		    avSeries.data.push(Math.round(avCountSLA/aRec.length));
		 }
	    }
	     
	    options.series.push(avSeries);
	    
	    //Output the graph
	    chart = new Highcharts.Chart(options);
	    return chart;
	  };