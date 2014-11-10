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

EasySDI_Mon.drawHealthGraphRaw = function(container, aStores, logRes,useSla){
	     //Prepare graph options
	     var options = {
                chart: {
			renderTo: container,
			defaultSeriesType: 'column'
		},
		title: {
			text: EasySDI_Mon.lang.getLocal('service health')
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
	     //contains untested and unknown
	     var otherSeries;
	     //push categories
	     for ( var storeName in aStores)
         {
	    	 if(typeof aStores[storeName] != 'function'){
	    		 options.xAxis.categories.push(storeName);
	    	 }
	     }
	     
	    var avCount = 0;
        var unavCount = 0;
        var fCount = 0;
        var otherCount = 0;
	    
        //push series
        for ( var storeName in aStores)
        {
        	// Reset for each method
        	avCount = 0;
            unavCount = 0;
            fCount = 0;
            otherCount = 0;
            
        	if(typeof aStores[storeName] != 'function'){
	            var aRec = aStores[storeName].getRange();
	        	var summaryCount = 0;
                    //push percentiles
                    for ( var i=0; i< aRec.length; i++ )
                    {   
                    	if(aRec[i].get('avCount') >= 0)
                    	{
                    		avCount+=aRec[i].get('avCount');
                    		fCount+=aRec[i].get('fCount');
                    		unavCount+=aRec[i].get('unavCount');
                    		otherCount+=aRec[i].get('otherCount');
                    		summaryCount = avCount + fCount +unavCount + otherCount;
                    	}else
                    	{
                    		var status = aRec[i].get('statusCode');
	                    	switch (status){
	                             case 'AVAILABLE':
	                                   avCount++;
	                             break;
	                             case 'OUT_OF_ORDER':
	                                   fCount++;
	                             break;
	                             case 'UNAVAILABLE':
	                                   unavCount++;
	                             break;
		                         case 'NOT_TESTED':
	                                   otherCount++;
	                             break;
	                             default: 
	                                   otherCount++;
	                             break;
	                        	}
                    	}
                    }
        			if(summaryCount > 0)
					{
						avSeries.data.push(Math.round((avCount/summaryCount)*10000)/100);
						unavSeries.data.push(Math.round((unavCount/summaryCount)*10000)/100);
						fSeries.data.push(Math.round((fCount/summaryCount)*10000)/100);
						otherSeries.data.push(Math.round((otherCount/summaryCount)*10000)/100);
					}else
					{
						avSeries.data.push(Math.round((avCount/aRec.length)*10000)/100);
						unavSeries.data.push(Math.round((unavCount/aRec.length)*10000)/100);
						fSeries.data.push(Math.round((fCount/aRec.length)*10000)/100);
						otherSeries.data.push(Math.round((otherCount/aRec.length)*10000)/100);
                    }
		    
        	}
	     }
	     
	    //push this series
	    options.series.push(otherSeries);
	    options.series.push(unavSeries);
	    options.series.push(fSeries);
	    options.series.push(avSeries);
	    
	    //Output the graph
	    var chart = new Highcharts.Chart(options);
	    return chart;
	  };