window.addEvent('domready', function() {
	
	 var fileList = Array();
	 var tmpFileCount = 0;
	 var format;
	 
	/*TODO  Don't keep this list hard-coded, retrieve it from the server*/
	 var filter={'(*.shp, *.shx, *.dbf)':'*.shp; *.shx; *.dbf;'};
	 
	 
	
	// our uploader instance 
	var up = new FancyUpload2($('demo-status'), $('demo-list'), { // options object
		// we console.log infos, remove that in production!!
		verbose: true,
		
		// url is read from the form, so you just have to change one place
		url: $('form-demo').action,
		
		// path to the SWF file
		path: 'components/com_easysdi_publish/js/fancyupload/Swiff.Uploader.swf',
		
		// remove that line to select all files, or edit it, add more items
		//typeFilter: filter
		//,
		
		// this is our browse button, *target* is overlayed with the Flash movie
		target: 'demo-browse',
		
		// graceful degradation, onLoad is only called if all went well with Flash
		onLoad: function() {
			$('demo-status').removeClass('hide'); // we show the actual UI
			$('demo-fallback').destroy(); // ... and hide the plain form
			
			// We relay the interactions with the overlayed flash to the link
			this.target.addEvents({
				click: function() {
					return false;
				},
				mouseenter: function() {
					this.addClass('hover');
				},
				mouseleave: function() {
					this.removeClass('hover');
					this.blur();
				},
				mousedown: function() {
					this.focus();
				}
			});

			// Interactions for the 2 other buttons
			
			$('demo-clear').addEvent('click', function() {
				up.remove(); // remove all files
				//remove options from Dataset list:
				 var datasets = $('datasets');
  			 for (var i = datasets.length - 1; i>=0; i--) {
  			   if (datasets.options[i].value != 0) {
  			     datasets.remove(i);
  			   }
  			 }

				
				
				
				
				return false;
			});
			
			//Init the file list
			$('fileList').value = "";
			//trigger the upload
			tmpFileCount = 0;
			
			/*
			$('demo-upload').addEvent('click', function() {
				if($('maxFileSize').value < up.size){	
					return false;
				}else{
					up.start(); // start upload
					return false;
				}
			});
		 	*/
		 	
		 	//Disable create/update button
			$('validateFs').disabled = true;
			$('validateFs').className = 'buttonDisabled';
			//Disable upload action and clear list
			$('demo-clear').className = 'fileActionDisabled';
			//$('demo-upload').className = 'fileActionDisabled';
		 	
		},
		
		onBrowse: function() {
			format = $('transfFormatId').options[$('transfFormatId').selectedIndex].text;
		},
		
		//Activate menus if files are selected
		onSelect: function(files){
			tmpFileCount+=files.length;
			if(files.length > 0){
					$('demo-browse').className = '';
		 			$('demo-clear').className = '';
		 			//$('demo-upload').className = '';
		 			
		 			//trigger the upload automagically
		 			if($('maxFileSize').value >= this.size){
		 				up.start(); 
		 			}
		 			// start upload
					//return false;
		 	}
			//handle if total size exceeded for user
			if($('maxFileSize').value < this.size){
				$('validateFs').disabled = true;
				$('validateFs').className = 'buttonDisabled';
				$('demo-browse').className = 'fileActionDisabled';
				//$('demo-upload').className = 'fileActionDisabled';
				$('errorMsg').style.display = 'block';
				$('errorMsg').style.visibilty = 'visible';
				$('errorMsg').set('html', 'Max size of '+($('maxFileSize').value/1024/1024)+'MB exceeded.');
			}
		},
		
		// Edit the following lines, it is your custom event handling
		
		/**
		 * Is called when files were not added, "files" is an array of invalid File classes.
		 * 
		 * This example creates a list of error elements directly in the file list, which
		 * hide on click.
		 */ 
		onSelectFail: function(files) {
			files.each(function(file) {
				new Element('li', {
					'class': 'validation-error',
					html: file.validationErrorMessage || file.validationError,
					title: MooTools.lang.get('FancyUpload', 'removeTitle'),
					events: {
						click: function() {
							this.destroy();
						}
					}
				}).inject(this.list, 'top');
			}, this);
		},
		
		/**
		 * catch if a file is removed from the list.
		 */
		onFileRemove: function(file) {
		 tempList = Array();
		 tmpFileCount--;
		 j = 0;
		 for (var i = 0, l = fileList.length; i < l; i++)
		 {
			tmp = fileList[i].split("/");
			tmpName =  tmp[tmp.length - 1];
		 	if(file.name != tmpName){
		 		tempList[j] = fileList[i];
		 		j++;
		 	}
		 }
		 
		 fileList = tempList;
		 $('fileList').value = '';
		 for (var i = 0; i < fileList.length; i++){
			 if($('fileList').value != '')
				 $('fileList').value += ",";
		 	 $('fileList').value += fileList[i];
		 }
		 
		 
		 //handle if total size exceeded for user
		 if($('maxFileSize').value >= this.size){
			$('demo-browse').className = '';
			//$('demo-upload').className = '';
			$('validateFs').disabled = false;
			$('validateFs').className = '';
			$('errorMsg').style.display = 'none';
			$('errorMsg').style.visibilty = 'hidden';
			$('errorMsg').set('html', '');
		 }
		 
		 //Disable button create/update if fileList is empty
		 if(fileList.length == 0 && tmpFileCount == 0){
			$('validateFs').disabled = true;
			$('validateFs').className = 'buttonDisabled';
		 	$('demo-clear').className = 'fileActionDisabled';
			//$('demo-upload').className = 'fileActionDisabled';
		 }
		 
		 //reset list
		 	var datasets = $('datasets');
  			 for (var i = datasets.length - 1; i>=0; i--) {
  			   if (datasets.options[i].value != 0) {
  			     datasets.remove(i);
  			   }
  			 }
		 
		 
		 if(fileList.length != 0 && tmpFileCount != 0){
		 	//list available datasets
		 	searchds_click();
		 }
		 
	  },
	  
		
		/**
		 * This one was directly in FancyUpload2 before, the event makes it
		 * easier for you, to add your own response handling (you probably want
		 * to send something else than JSON or different items).
		 */
		onFileSuccess: function(file, response) {
			var json = new Hash(JSON.decode(response, true) || {});
			
			if (json.get('status') == '1') {
				file.element.addClass('file-success');
				file.info.set('html', '<strong>File was uploaded successfully</strong>');
				
				//add file to the hidden input of the other form
				fileList.push(json.get('src'));
				if($('fileList').value != "")
					$('fileList').value += ",";
				$('fileList').value += json.get('src');
				//enable create / update button if fileList is not empty
				//enable upload and clear list
				if(fileList.length > 0){
		 			$('validateFs').disabled = false;
					$('validateFs').className = '';
		 		}
		 		
		 		//try to fetch the datasets from the uploaded files
		 		//when the last one is uploaded
		 		
		 		if(fileList.length == tmpFileCount)
		 			searchds_click();
		 		
				
				
				
			} else {
				file.element.addClass('file-failed');
				file.info.set('html', '<strong>An error occured:</strong> ' + (json.get('error') ? (json.get('error') + ' #' + json.get('code')) : response));
			}
		},
		
		/**
		 * onFail is called when the Flash movie got bashed by some browser plugin
		 * like Adblock or Flashblock.
		 */
		onFail: function(error) {
			switch (error) {
				case 'hidden': // works after enabling the movie and clicking refresh
					alert('To enable the embedded uploader, unblock it in your browser and refresh (see Adblock).');
					break;
				case 'blocked': // This no *full* fail, it works after the user clicks the button
					alert('To enable the embedded uploader, enable the blocked Flash movie (see Flashblock).');
					break;
				case 'empty': // Oh oh, wrong path
					alert('A required file was not found, please be patient and we fix this.');
					break;
				case 'flash': // no flash 9+ :(
					alert('To enable the embedded uploader, install the latest Adobe Flash plugin.')
			}
		}
		
	});
});