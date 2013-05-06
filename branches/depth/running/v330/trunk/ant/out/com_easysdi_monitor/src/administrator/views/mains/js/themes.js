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
 *
 * @author Torstein Hønsi
 */


Ext.namespace("EasySDI_Mon");

jQuery(function() {
	jQuery('#demo-menu a').each(function() { // highlight active menu
		var linkedExample = /[?&]example=([^&#]*)/.exec(this.href)[1];
		if (linkedExample == example) this.parentNode.className = 'active';
	});
	jQuery('#styleswitcher a').each(function() { // highlight active style
		var linkedTheme = /[?&]theme=([^&#]*)/.exec(this.href)[1];
		if (linkedTheme == EasySDI_Mon.theme) this.parentNode.className = 'active';
	});
	
	// key listeners for the previous and next arrows
	jQuery(document).keydown(function (e) {
		var anchor;
		if (e.keyCode == 39) {
			anchor = document.getElementById('next-example');
			
		}
		else 
			if (e.keyCode == 37) {
			anchor = document.getElementById('previous-example');
		}
		
			if (anchor) 
				location.href = anchor.href;
		
	})
});
EasySDI_Mon.viewOptions = function (btn, example) {
	var options = demo[example].options, 
		s = '';
		
	function clean(str) {
		return str.replace(/</g, '&lt;').replace(/>/g, '&gt;');
	}
	
	function doLevel(level, obj) {
		jQuery.each(obj, function(member, value) {
			// compute indentation
			var indent = '';
			for (var j = 0; j < level; j++) indent += '	';
			
			if (typeof value == 'string')
				s += indent + member +": '"+ clean(value) +"',\n";
				
			else if (typeof value == 'number')
				s += indent + member +": "+ value +",\n";
				
			else if (typeof value == 'function')
				s += indent + member +": "+ clean(value.toString()) +",\n";
				
			else if (jQuery.isArray(value)) {
				s += indent + member +": [";
				$.each(value, function(member, value) {
					if (typeof value == 'string')
						s += "'"+ clean(value) +"', ";
						
					else if (typeof value == 'number')
						s += value +", ";
					
					else if (typeof value == 'object') {
						s += indent +"{\n";
						doLevel(level + 1, value);
						s += indent +"}, ";
					}
					
				});
				s = s.replace(/, $/, '');
				s += "],\n";
			}
				
			else if (typeof value == 'object') {
				s += indent + member +": {\n";
				doLevel(level + 1, value);
				s += indent +"},\n";
			}
			
		});
		// strip out stray commas
		//s = s.replace(/,([\s]?)$/, '\n$1}');
	};
	
	doLevel(0, options);
	
	// strip out stray commas
	s = s.replace(/,\n([\s]?)}/g, '\n$1}');
	s = s.replace(/,\n$/, '');
	
	// pop up the Highslide
	hs.htmlExpand(btn, { 
		width: 1000,
		align: 'center',
		dimmingOpacity: .1,
		allowWidthReduction: true,  
		headingText: 'Configuration options',
		wrapperClassName: 'titlebar',
		maincontentText: '<pre style="margin: 0">'+ s +'</pre>'
	});
};
/**
 * Predefines styles
 */
EasySDI_Mon.oldDefault = {
	colors: ['#058DC7', '#50B432', '#ED561B', '#DDDF00', '#24CBE5', '#64E572', '#FF9655', '#FFF263', '#6AF9C4'],
	chart: {
		backgroundColor: {
			linearGradient: [0, 0, 500, 500],
			stops: [
				[0, 'rgb(255, 255, 255)'],
				[1, 'rgb(240, 240, 255)']
			]
		}
,
		borderWidth: 2,
		plotBackgroundColor: 'rgba(255, 255, 255, .9)',
		plotShadow: true,
		plotBorderWidth: 1
	},
	title: {
		style: { 
			color: '#000',
			font: 'bold 16px "Trebuchet MS", Verdana, sans-serif'
		}
	},
	subtitle: {
		style: { 
			color: '#666666',
			font: 'bold 12px "Trebuchet MS", Verdana, sans-serif'
		}
	},
	xAxis: {
		gridLineWidth: 1,
		lineColor: '#000',
		tickColor: '#000',
		labels: {
			style: {
				color: '#000',
				font: '11px Trebuchet MS, Verdana, sans-serif'
			}
		},
		title: {
			style: {
				color: '#333',
				fontWeight: 'bold',
				fontSize: '12px',
				fontFamily: 'Trebuchet MS, Verdana, sans-serif'

			}				
		}
	},
	yAxis: {
		alternateGridColor: null,
		minorTickInterval: 'auto',
		lineColor: '#000',
		lineWidth: 1,
		tickWidth: 1,
		tickColor: '#000',
		labels: {
			style: {
				color: '#000',
				font: '11px Trebuchet MS, Verdana, sans-serif'
			}
		},
		title: {
			style: {
				color: '#333',
				fontWeight: 'bold',
				fontSize: '12px',
				fontFamily: 'Trebuchet MS, Verdana, sans-serif'
			}				
		}
	},
	legend: {
		itemStyle: {			
			font: '9pt Trebuchet MS, Verdana, sans-serif',
			color: 'black'

		},
		itemHoverStyle: {
			color: '#039'
		},
		itemHiddenStyle: {
			color: 'gray'
		}
	},
	labels: {
		style: {
			color: '#99b'
		}
	}
};
EasySDI_Mon.themes = {
'default': {

},
skies: jQuery.extend(true, null, EasySDI_Mon.oldDefault, {
	colors: ["#514F78", "#42A07B", "#9B5E4A", "#72727F", "#1F949A", "#82914E", "#86777F", "#42A07B"],
	chart: {
		className: 'skies',
		backgroundColor: null,
		borderWidth: 0,
		plotBackgroundImage: '/demo/gfx/skies.jpg',
		plotBackgroundColor: {
			linearGradient: [0, 0, 250, 500],
			stops: [
				[0, 'rgba(255, 255, 255, 1)'],
				[1, 'rgba(255, 255, 255, 0)']
			]
		}
	},
	title: {
		style: { 
			color: '#3E576F',
			font: '16px Lucida Grande, Lucida Sans Unicode, Verdana, Arial, Helvetica, sans-serif'
		}
	},
	subtitle: {
		style: { 
			color: '#6D869F',
			font: '12px Lucida Grande, Lucida Sans Unicode, Verdana, Arial, Helvetica, sans-serif'
		}
	},
	xAxis: {
		gridLineWidth: 0,
		lineColor: '#C0D0E0',
		tickColor: '#C0D0E0',
		labels: {
			style: {
				color: '#666',
				fontWeight: 'bold'
			}
		},
		title: {
			style: {
				color: '#666',
				font: '12px Lucida Grande, Lucida Sans Unicode, Verdana, Arial, Helvetica, sans-serif'
			}				
		}
	},
	yAxis: {
		alternateGridColor: 'rgba(255, 255, 255, .5)',
		minorTickInterval: null,
		lineColor: '#C0D0E0',
		tickColor: '#C0D0E0',
		labels: {
			style: {
				color: '#666',
				fontWeight: 'bold'
			}
		},
		title: {
			style: {
				color: '#666',
				font: '12px Lucida Grande, Lucida Sans Unicode, Verdana, Arial, Helvetica, sans-serif'
			}				
		}
	},
	legend: {
		itemStyle: {
			color: '#3E576F'
		},
		itemHoverStyle: {
			color: 'black'
		},
		itemHiddenStyle: {
			color: 'silver'
		}
	},
	labels: {
		style: {
			color: '#3E576F'
		}
	}
}),
/*'pink-floral': $.extend(true, null, EasySDI_Mon.oldDefault, {
	colors: ["#6C4B6A", "#529CA0", "#A57972", "#5D7C9B", "#72727F", "#DFA09B", "#7C3A49", "808AA9"],
	chart: {
		className: 'pink-floral',
		backgroundColor: null,
		borderWidth: 1,
		borderColor: '#b7748c',
		borderRadius: 20,
		style: {
			backgroundImage: 'url(/demo/gfx/pink-floral-background.png)'
		},
		plotBackgroundColor: {
			linearGradient: [0, 0, 250, 500],
			stops: [
				[0, 'rgba(255, 255, 255, .75)'],
				[1, 'rgba(252, 221, 217, .75)']
			]
		},
		plotBorderColor: '#b7748c', 
		plotShadow: false
	},
	title: {
		style: { 
			color: '#7F3753',
			font: '16px bold Lucida Grande, Lucida Sans Unicode, Verdana, Arial, Helvetica, sans-serif'
		}
	},
	subtitle: {
		style: { 
			color: '#b7748c',
			font: '12px Lucida Grande, Lucida Sans Unicode, Verdana, Arial, Helvetica, sans-serif'
		}
	},
	xAxis: {
		gridLineWidth: 0,
		lineColor: '#b7748c',
		tickColor: '#b7748c',
		labels: {
			style: {
				color: '#b7748c',
				fontWeight: 'bold'
			}
		},
		title: {
			style: {
				color: '#b7748c',
				font: 'bold 12px Lucida Grande, Lucida Sans Unicode, Verdana, Arial, Helvetica, sans-serif'
			}				
		}
	},
	yAxis: {
		alternateGridColor: 'rgba(255, 255, 255, .5)',
		minorTickInterval: null,
		lineColor: '#b7748c',
		tickColor: '#b7748c',
		labels: {
			style: {
				color: '#b7748c',
				fontWeight: 'bold'
			}
		},
		title: {
			style: {
				color: '#b7748c',
				font: 'bold 12px Lucida Grande, Lucida Sans Unicode, Verdana, Arial, Helvetica, sans-serif'
			}				
		}
	},
	legend: {
		itemStyle: {
			color: '#7F3753'
		},
		itemHoverStyle: {
			color: '#3E576F'
		},
		itemHiddenStyle: {
			color: 'silver'
		}
	},
	labels: {
		style: {
			color: '#3E576F'
		}
	}
}),*/
grid: EasySDI_Mon.oldDefault,
minimal: {
	colors: ["#4572A7", "#AA4643", "#89A54E", "#80699B", "#3D96AE", "#DB843D", "#92A8CD", "#A47D7C", "#B5CA92"],
	chart: {
		backgroundColor: null,
		borderWidth: 0,
		plotBackgroundColor: null,
		plotShadow: false,
		plotBorderWidth: 0
	},
	title: {
		style: { 
			color: '#3E576F',
			font: '16px Lucida Grande, Lucida Sans Unicode, Verdana, Arial, Helvetica, sans-serif'
		}
	},
	subtitle: {
		style: { 
			color: '#6D869F',
			font: '12px Lucida Grande, Lucida Sans Unicode, Verdana, Arial, Helvetica, sans-serif'
		}
	},
	xAxis: {
		gridLineWidth: 0,
		lineColor: '#C0D0E0',
		tickColor: '#C0D0E0',
		labels: {
			style: {
				color: '#666',
				fontWeight: 'bold'
			}
		},
		title: {
			style: {
				color: '#6D869F',
				font: 'bold 12px Lucida Grande, Lucida Sans Unicode, Verdana, Arial, Helvetica, sans-serif'
			}				
		}
	},
	yAxis: {
		alternateGridColor: null,
		minorTickInterval: null,
		lineWidth: 0,
		tickWidth: 0,
		labels: {
			style: {
				color: '#666',
				fontWeight: 'bold'
			}
		},
		title: {
			style: {
				color: '#6D869F',
				font: 'bold 12px Lucida Grande, Lucida Sans Unicode, Verdana, Arial, Helvetica, sans-serif'
			}				
		}
	},
	legend: {
		itemStyle: {
			color: '#3E576F'
		},
		itemHoverStyle: {
			color: 'black'
		},
		itemHiddenStyle: {
			color: 'silver'
		}
	},
	labels: {
		style: {
			color: '#3E576F'
		}
	}
},
gray: {
	colors: ["#DDDF0D", "#7798BF", "#55BF3B", "#DF5353", "#aaeeee", "#ff0066", "#eeaaee", 
		"#55BF3B", "#DF5353", "#7798BF", "#aaeeee"],
	chart: {
		backgroundColor: {
			linearGradient: [0, 0, 0, 400],
			stops: [
				[0, 'rgb(96, 96, 96)'],
				[1, 'rgb(16, 16, 16)']
			]
		},
		borderWidth: 0,
		borderRadius: 15,
		plotBackgroundColor: null,
		plotShadow: false,
		plotBorderWidth: 0
	},
	title: {
		style: { 
			color: '#FFF',
			font: '16px Lucida Grande, Lucida Sans Unicode, Verdana, Arial, Helvetica, sans-serif'
		}
	},
	subtitle: {
		style: { 
			color: '#DDD',
			font: '12px Lucida Grande, Lucida Sans Unicode, Verdana, Arial, Helvetica, sans-serif'
		}
	},
	xAxis: {
		gridLineWidth: 0,
		lineColor: '#999',
		tickColor: '#999',
		labels: {
			style: {
				color: '#999',
				fontWeight: 'bold'
			}
		},
		title: {
			style: {
				color: '#AAA',
				font: 'bold 12px Lucida Grande, Lucida Sans Unicode, Verdana, Arial, Helvetica, sans-serif'
			}				
		}
	},
	yAxis: {
		alternateGridColor: null,
		minorTickInterval: null,
		gridLineColor: 'rgba(255, 255, 255, .1)',
		lineWidth: 0,
		tickWidth: 0,
		labels: {
			style: {
				color: '#999',
				fontWeight: 'bold'
			}
		},
		title: {
			style: {
				color: '#AAA',
				font: 'bold 12px Lucida Grande, Lucida Sans Unicode, Verdana, Arial, Helvetica, sans-serif'
			}				
		}
	},
	legend: {
		itemStyle: {
			color: '#CCC'
		},
		itemHoverStyle: {
			color: '#FFF'
		},
		itemHiddenStyle: {
			color: '#333'
		}
	},
	labels: {
		style: {
			color: '#CCC'
		}
	},
	tooltip: {
		backgroundColor: {
			linearGradient: [0, 0, 0, 50],
			stops: [
				[0, 'rgba(96, 96, 96, .8)'],
				[1, 'rgba(16, 16, 16, .8)']
			]
		},
		borderWidth: 0,
		style: {
			color: '#FFF'
		}
	},
	
	
	plotOptions: {
		line: {
			dataLabels: {
				color: '#CCC'
			},
			marker: {
				lineColor: '#333'
			}
		},
		spline: {
			marker: {
				lineColor: '#333'
			}
		},
		scatter: {
			marker: {
				lineColor: '#333'
			}
		}
	},
	
	toolbar: {
		itemStyle: {
			color: '#CCC'
		}
	},
	
	navigation: {
		buttonOptions: {
			backgroundColor: {
				linearGradient: [0, 0, 0, 20],
				stops: [
					[0.4, '#606060'],
					[0.6, '#333333']
				]
			},
			borderColor: '#000000',
			symbolStroke: '#C0C0C0',
			hoverSymbolStroke: '#FFFFFF'
		}
	},
	
	exporting: {
		buttons: {
			exportButton: {
				symbolFill: '#55BE3B'
			},
			printButton: {
				symbolFill: '#7797BE'
			}
		}
	},	
	
	// special colors for some of the
	legendBackgroundColor: 'rgba(48, 48, 48, 0.8)',
	legendBackgroundColorSolid: 'rgb(70, 70, 70)',
	dataLabelsColor: '#444',
	maskColor: 'rgba(255,255,255,0.3)'
},
'dark-blue': jQuery.extend(true, null, EasySDI_Mon.oldDefault, {
	colors: ["#DDDF0D", "#55BF3B", "#DF5353", "#7798BF", "#aaeeee", "#ff0066", "#eeaaee", 
		"#55BF3B", "#DF5353", "#7798BF", "#aaeeee"],
	chart: {
		backgroundColor: {
			linearGradient: [0, 0, 250, 500],
			stops: [
				[0, 'rgb(48, 48, 96)'],
				[1, 'rgb(0, 0, 0)']
			]
		},
		borderColor: '#000000',
		className: 'dark-container',
		plotBackgroundColor: 'rgba(255, 255, 255, .1)',
		plotBorderColor: '#CCCCCC',
		plotShadow: false
	},
	title: {
		style: {
			color: '#C0C0C0'
		}

	},
	xAxis: {
		gridLineColor: '#333333',
		labels: {
			style: {
				color: '#A0A0A0'
			}
		},
		lineColor: '#A0A0A0',
		tickColor: '#A0A0A0',
		title: {
			style: {
				color: '#C0C0C0'
			}
		}
	},
	yAxis: {
		gridLineColor: '#333333',
		labels: {
			style: {
				color: '#A0A0A0'
			}
		},
		lineColor: '#A0A0A0',
		minorTickInterval: null,
		tickColor: '#A0A0A0',
		title: {
			style: {
				color: '#C0C0C0'
			}
		}
	},
	legend: {
		style: {
			color: '#A0A0A0'
		}
	},
	tooltip: {
		backgroundColor: 'rgba(0, 0, 0, 0.75)',
		style: {
			color: '#F0F0F0'
		}
	},
	toolbar: {
		itemStyle: { 
			color: 'silver'
		}
	},
	plotOptions: {
		line: {
			dataLabels: {
				color: '#CCC'
			},
			marker: {
				lineColor: '#333'
			}
		},
		spline: {
			marker: {
				lineColor: '#333'
			}
		},
		scatter: {
			marker: {
				lineColor: '#333'
			}
		}
	},		
	legend: {
		itemStyle: {
			color: '#CCC'
		},
		itemHoverStyle: {
			color: '#FFF'
		},
		itemHiddenStyle: {
			color: '#444'
		}
	},
	credits: {
		style: {
			color: '#666'
		}
	},
	labels: {
		style: {
			color: '#CCC'
		}
	},
	
	navigation: {
		buttonOptions: {
			backgroundColor: {
				linearGradient: [0, 0, 0, 20],
				stops: [
					[0.4, '#606060'],
					[0.6, '#333333']
				]
			},
			borderColor: '#000000',
			symbolStroke: '#C0C0C0',
			hoverSymbolStroke: '#FFFFFF'
		}
	},
	
	exporting: {
		buttons: {
			exportButton: {
				symbolFill: '#55BE3B'
			},
			printButton: {
				symbolFill: '#7797BE'
			}
		}
	},
	
	// special colors for some of the
	legendBackgroundColor: 'rgba(0, 0, 0, 0.5)',
	legendBackgroundColorSolid: 'rgb(35, 35, 70)',
	dataLabelsColor: '#444',
	maskColor: 'rgba(255,255,255,0.3)'
})

}; // end themes

EasySDI_Mon.themes['dark-green'] = jQuery.extend(true, null, EasySDI_Mon.themes['dark-blue'], {
	chart: {
		backgroundColor: {
			linearGradient: [0, 0, 250, 500],
			stops: [
				[0, 'rgb(48, 96, 48)'],
				[1, 'rgb(0, 0, 0)']
			]
		}
	}
});


// Set the options
EasySDI_Mon.highchartsOptions = Highcharts.setOptions(EasySDI_Mon.themes[EasySDI_Mon.theme]);

