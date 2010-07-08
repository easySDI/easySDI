<?php

defined('_JEXEC') or die('Restricted access');
?>
<script>

//Transfert toutes les lignes de la liste Origine à la liste Destination
function TransfertAll(idOrigine, idDestination)
{	var objOrigine = document.getElementById(idOrigine);
	var objDestination = document.getElementById(idDestination);

	for (i=objOrigine.length-1;i>=0;i--)
	{
		//selectedOne = objOrigine.options[i];
	    var ADeplacer = new Option(objOrigine.options[i].text, objOrigine.options[i].value);
    	objDestination.options[objDestination.length]=ADeplacer;
    	objOrigine.options[i]=null;    			
	 }
	 
	sortArray(idDestination);
	/*for (i=objOrigine.length-1;i>=0;i--) 
	{
		//console.log(i);
		var ADeplacer = new Option(objOrigine.options[i].text, objOrigine.options[i].value);
		objDestination.options[i]=ADeplacer;
		objOrigine.options[i]=null;
	}*/
}

//Transfert une ligne de la liste Origine à la liste Destination
function Transfert(idOrigine, idDestination)
{	var objOrigine = document.getElementById(idOrigine);
	var objDestination = document.getElementById(idDestination);

	for (var i = 0; i < objOrigine.length; i++)
	{
		selectedOne = objOrigine.options[i];
	    if (selectedOne.selected == true){
	    	if (VerifValeurDansListe(idDestination, selectedOne.value, true)) 
		    	return false;

	    	var ADeplacer = new Option(objOrigine.options[i].text, objOrigine.options[i].value);
	    	objDestination.options[objDestination.length]=ADeplacer;
	    	objOrigine.options[i].selected == false;
	    	objOrigine.options[i]=null; 
	    }
	 }

	sortArray(idDestination);
}

//Vérifie la présence de Valeur dans IdListe
function VerifValeurDansListe(IdListe, Valeur, blnAlerte) 
{
	var objListe = document.getElementById(IdListe);
	for (i=objListe.length-1;i>=0;i--) if (objListe.options[i].value == Valeur) {if (blnAlerte) alert('Deja present.'); return true;}
	return false;
}

function PostSelect(form_name, idListe)
{
	// On compte le nombre d'item de la liste select
  	obj=document.getElementById(idListe);
  	NbOption=obj.length;
  
	// On lance une boucle pour selectionner tous les items
  	for(i=0; i < NbOption; i++)
	{
    	obj.options[i].selected = true;
  	}
  
  	// On modifie l'ID  du champ select pour que PHP traite cette dernière comme un array
  	obj.name = "selected[]";
	
  	// On soumet le formulaire
  	obj_form=document.getElementById(form_name);
  	obj_form.submit();
}

function Pre_Post(form_name, idListe, type)
{
	// On compte le nombre d'item de la liste select
  	obj=document.getElementById(idListe);
  	NbOption=obj.length;

    // On lance une boucle pour selectionner tous les items
  	for(i=0; i < NbOption; i++)
	{
    	obj.options[i].selected = true;
  	}
  	
  	// On modifie l'ID  du champ select pour que PHP traite cette dernière comme un array
  	obj.name = "selected_" + type + "[]";
}

function updateSelect2(childClasses, rootClasses, packId) {
	var selbox = document.getElementById("class_id");
	selbox.options.length = 1;

	var rootId = 0;
	for (i=0; i<rootClasses.length;i++)
	{
		var r = rootClasses[i];

		if (r.pack == packId)
			rootId=r.rootId;
	}
	var usefullClasses = new Array();
	for (i=0; i<childClasses.length;i++)
	{
		var c = childClasses[i];
		
		if (c.parent == rootId)
			selbox.options[selbox.options.length] = new Option(c.text,c.value);
	}
}


function maxlength(text,length) 
{
	//console.log(navigator.appCodeName);
	if (navigator.appCodeName == "Mozilla")
	{
		if(text.value.length>length) 
			text.value=text.value.substr(0,length);
	}
	else
	{
		if(text.innerText.length>length) 
			text.innerText=text.innerText.substr(0,length);
	}
} 

function changeVisibility(attribute)
{
	var attributeTypes = document.getElementById("attributetypes");
  	//console.log(attributeTypes);
  	//console.log(attribute);
	
	for(var i=0; i < attributeTypes.length; i++)
	{
  		//console.log(attributeTypes[i]['value']);
  		if (attributeTypes[i]['value'] == attribute)
  			attributeType = attributeTypes.options[i].text;
  	}
  
  	 
	 // Valeur par defaut
	 var defaultVal_textbox = document.getElementById("div_defaultVal_textbox");
	 var defaultVal_textarea = document.getElementById("div_defaultVal_textarea");
	 var defaultVal_List = document.getElementById("div_defaultVal_list");
	 var defaultVal_Choicelist = document.getElementById("div_defaultVal_choicelist");
	 var defaultVal_Radio = document.getElementById("div_defaultVal_radio");
	 var defaultDate = document.getElementById("defaultDate");
	 var defaultDate_Radio = document.getElementById("defaultDate_Radio");
	 var defaultVal_Locale_textbox = document.getElementById("div_defaultVal_locale_textbox");
	 var defaultVal_Locale_textarea = document.getElementById("div_defaultVal_locale_textarea");

	 if (attributeType == 0 || attributeType == 1 || attributeType == 7) // empty, guid, link 
	 {
		 defaultVal_textbox.style.display = "none";
		 defaultVal_textarea.style.display = "none";
		 defaultVal_Radio.style.display = "none";
		 defaultVal_Locale_textbox.style.display = "none";
		 defaultVal_Locale_textarea.style.display = "none";
		 defaultVal_List.style.display = "none";
		 defaultVal_Choicelist.style.display = "none";
	 }  
     else if (attributeType == 3) // Locale
     {
    	 defaultVal_textbox.style.display = "none";
         defaultVal_Radio.style.display = "none";
         defaultVal_Locale_textbox.style.display = "";
 		 defaultVal_List.style.display = "none";
 		defaultVal_Choicelist.style.display = "none";
     }
     else if (attributeType == 5) // date
     {
    	 defaultVal_textbox.style.display = "none";
         defaultVal_Radio.style.display = "";
         defaultVal_Locale_textbox.style.display = "none";
 		 defaultVal_List.style.display = "none";
 		defaultVal_Choicelist.style.display = "none";
     }
     else if (attributeType == 6) // List
     {
    	 defaultVal_textbox.style.display = "none";
         defaultVal_Radio.style.display = "none";
         defaultVal_Locale_textbox.style.display = "none";
 		 defaultVal_List.style.display = "";
 		defaultVal_Choicelist.style.display = "none";
     }
     else if (attributeType == 9 || attributeType == 10) // Choicelist
     {
    	 defaultVal_textbox.style.display = "none";
         defaultVal_Radio.style.display = "none";
         defaultVal_Locale_textbox.style.display = "none";
 		 defaultVal_List.style.display = "none";
 		defaultVal_Choicelist.style.display = "";
     }
     else // others
     {
    	 defaultVal_textbox.style.display = "";
         defaultVal_Radio.style.display = "none";
         defaultVal_Locale_textbox.style.display = "none";
 		 defaultVal_List.style.display = "none";
 		defaultVal_Choicelist.style.display = "none";
     }
}

function changeAttributeListVisibility(attributeType)
{
	// Isocode du type
	var isocodeType = document.getElementById("div_isocodeType");
	
	if(attributeType == 6) 
	    isocodeType.style.display = "";  
    else 
   		isocodeType.style.display = "none";

}

function changeDateVisibility(currentVal)
{
	 // Valeur par defaut
	 var defaultDate = document.getElementById("defaultDate");
	 var div_defaultDate = document.getElementById("div_defaultDate");
	 
	 if (currentVal == 0) // Date du jour
 	 {
	 	//defaultDate.disabled=true;
	 	div_defaultDate.style.display = "none";
 	 	defaultDate.value="today";
 	 }
 	 else // Date fixe
 	 {
  	 	//defaultDate.disabled=false;
  	 	div_defaultDate.style.display = "";
  	 	defaultDate.value="";
 	 }
}

function changeBoundsVisibility(relationType)
{
	// Isocode du type
	var bounds = document.getElementById("div_bounds");

	if(relationType == 5) // generalization 
		bounds.style.display = "none";  
     else 
    	 bounds.style.display = "";
	
}

function changeDefaultField(renderType)
{
	if (renderType == 1)
	{
		var attributeId = document.getElementById("attribute_id");
		if (!attributeId)
			attributeId = document.getElementById("attributechild_id");
		var attributeTypes = document.getElementById("attributetypes");
	  	
		for(var i=0; i < attributeTypes.length; i++)
		{
	  		//console.log(attributeTypes[i]['value']);
	  		if (attributeTypes[i]['value'] == attributeId.value)
	  			attributeType = attributeTypes.options[i].text;
	  	}

		// Valeur par defaut
		 var defaultVal_textbox = document.getElementById("div_defaultVal_textbox");
		 var defaultVal_textarea = document.getElementById("div_defaultVal_textarea");
		 var defaultVal_Locale_textbox = document.getElementById("div_defaultVal_locale_textbox");
		 var defaultVal_Locale_textarea = document.getElementById("div_defaultVal_locale_textarea");

		 if (attributeType == 2) //Text
		{
			 defaultVal_textbox.style.display = "none";
			 defaultVal_textarea.style.display = "";
		}
		else if (attributeType == 3) //Locale
		{
			 defaultVal_Locale_textbox.style.display = "none";
			 defaultVal_Locale_textarea.style.display = "";
		}		
	}
	else if (renderType == 5)
	{
		var attributeId = document.getElementById("attribute_id");
		if (!attributeId)
			attributeId = document.getElementById("attributechild_id");
		var attributeTypes = document.getElementById("attributetypes");
	  	
		for(var i=0; i < attributeTypes.length; i++)
		{
	  		//console.log(attributeTypes[i]['value']);
	  		if (attributeTypes[i]['value'] == attributeId.value)
	  			attributeType = attributeTypes.options[i].text;
	  	}

		 // Valeur par defaut
		 var defaultVal_textbox = document.getElementById("div_defaultVal_textbox");
		 var defaultVal_textarea = document.getElementById("div_defaultVal_textarea");
		 var defaultVal_Locale_textbox = document.getElementById("div_defaultVal_locale_textbox");
		 var defaultVal_Locale_textarea = document.getElementById("div_defaultVal_locale_textarea");

		 if (attributeType == 2) //Text
		{
			 defaultVal_textbox.style.display = "";
			 defaultVal_textarea.style.display = "none";
		}
		else if (attributeType == 3) //Locale
		{
			 defaultVal_Locale_textbox.style.display = "";
			 defaultVal_Locale_textarea.style.display = "none";
		}		
	}
}

function updatelist(attributeid) {
	xmlhttp=GetXmlHttpObject();
	if (xmlhttp==null)
	  {
	  alert ("Browser does not support HTTP Request");
	  return;
	  }
	var url="index.php?option=com_easysdi_catalog&task=executequery&no_html=1";
	url=url+"&q="+attributeid;
	//url=url+"&sid="+Math.random();
	xmlhttp.onreadystatechange=stateChanged;
	xmlhttp.open("GET",url,true);
	xmlhttp.send(null);
}

function stateChanged()
{
	if (xmlhttp.readyState==4)
	{
	document.getElementById("txtHint").innerHTML=xmlhttp.responseText;
	}
}

function GetXmlHttpObject()
{
	if (window.XMLHttpRequest)
	  {
	  // code for IE7+, Firefox, Chrome, Opera, Safari
	  return new XMLHttpRequest();
	  }
	if (window.ActiveXObject)
	  {
	  // code for IE6, IE5
	  return new ActiveXObject("Microsoft.XMLHTTP");
	  }
	return null;
		
}

function sortArray(arr)
{
	Liste= new Array();
	Obj= document.getElementById(arr)
	 
	for(i=0;i<Obj.options.length;i++)
	{
		Liste[i]=new Array()
		Liste[i][0]=Obj.options[i].text
		Liste[i][1]=Obj.options[i].value
	}
	Liste=Liste.sort()
	
	for(i=0;i<Obj.options.length;i++)
	{
		Obj.options[i].text=Liste[i][0]
		Obj.options[i].value=Liste[i][1]
	}
	 
}

</script>
