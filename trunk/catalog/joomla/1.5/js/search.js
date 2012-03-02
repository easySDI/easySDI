/*
* Register event handlers
*/
window.addEvent('domready', function() 
{
	//initialize the page
	init();
			
	// Toggle the state of the advanced search
	$('advSearchRadio').addEvent('click', function() {
		toggleAdvancedSearch($('advSearchRadio').checked);
	});
	
	//Handler for the clear button
	$('easysdi_clear_button').addEvent('click', function() {
		easysdiClearButton_click();
	});
	
	//Handler for the search button
	$('simple_search_button').addEvent('click', function() {
		easysdiSearchButton_click();
	});
	
});

/*
* Hide advanced search
*/	
function init(){
	toggleAdvancedSearch($('advancedSrch').value);
	/*if($('advancedSrch').value == 1){
		$('divAdvancedSearch').style.visibility = 'visible';
		$('divAdvancedSearch').style.display = 'block';
		$('advSearchRadio').checked = true;
		$('advancedSrch').value=1;
	}else{
		$('divAdvancedSearch').style.visibility = 'hidden';
		$('divAdvancedSearch').style.display = 'none';
		$('advSearchRadio').checked = false;
		$('advancedSrch').value=0;
	}*/
}

/*
* Clear all field selections
*/	
function easysdiClearButton_click(){
	clearBasicSearch();
	if(document.getElementById('advancedSrch') == '1')
		clearAdvancedSearch();
}

/*
* Search
*/	
function easysdiSearchButton_click(){
	document.getElementById('tabIndex').value = '0';
	document.getElementById('catalog_search_form').submit();
}

/*
* Show/Hide advanced tab
*/	
function toggleAdvancedSearch(isVisible){
	if(isVisible == true){
		$('divAdvancedSearch').style.visibility = 'visible';
		$('divAdvancedSearch').style.display = 'block';
		$('advSearchRadio').checked = true;
		$('advancedSrch').value=1;
	}else{
		$('divAdvancedSearch').style.visibility = 'hidden';
		$('divAdvancedSearch').style.display = 'none';
		$('advSearchRadio').checked = false;
		$('advancedSrch').value=0;
		//Do not keep data in a hidden table
		//clearAdvancedSearch();
	}
}

/*
* Clear all basic search fields
*/	
function clearBasicSearch ()
{
	// Lister tous les champs qui sont dans le div divSimpleSearch
	var divSimpleSearch;
	divSimpleSearch = document.getElementById('divSimpleSearch');
	var fields;
	fields = divSimpleSearch.getElementsByTagName('input');
	
	for (var i = 0; i < fields.length; i++)
	{
		if (fields.item(i).type == "checkbox") // Les checkbox de l'objecttype
			fields.item(i).checked = "";
		else if (fields.item(i).type == "radio") // Les radios de la version
			fields.item(i).checked = "";
		else 
			fields.item(i).value = "";
	}

	fields = divSimpleSearch.getElementsByTagName('select');
	
	for (var i = 0; i < fields.length; i++)
	{
		fields.item(i).value = "";
	}
}

/*
* Clear all advanced fields
*/	
function clearAdvancedSearch ()
{
	// Lister tous les champs qui sont dans le div divAdvancedSearch
	var divAdvancedSearch;
	divAdvancedSearch = document.getElementById('divAdvancedSearch');
	var fields;
	fields = divAdvancedSearch.getElementsByTagName('input');
	
	for (var i = 0; i < fields.length; i++)
	{
		if (fields.item(i).type == "checkbox") // Les checkbox de l'objecttype
			fields.item(i).checked = "";
		else if (fields.item(i).type == "radio") // Les radios de la version
			fields.item(i).checked = "";
		else 
			fields.item(i).value = "";
	}

	fields = divAdvancedSearch.getElementsByTagName('select');
	
	for (var i = 0; i < fields.length; i++)
	{
		fields.item(i).value = "";
	}
}