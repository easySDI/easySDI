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
		var ADeplacer = new Option(objOrigine.options[i].text, objOrigine.options[i].value);
		objDestination.options[i]=ADeplacer;
		objOrigine.options[i]=null;
	}
}

//Transfert une ligne de la liste Origine à la liste Destination
function Transfert(idOrigine, idDestination)
{	var objOrigine = document.getElementById(idOrigine);
	var objDestination = document.getElementById(idDestination);
	if (objOrigine.options.selectedIndex<0) return false;
	if (VerifValeurDansListe(idDestination, objOrigine.options[objOrigine.options.selectedIndex].value, true)) return false;
	var ADeplacer = new Option(objOrigine.options[objOrigine.options.selectedIndex].text, objOrigine.options[objOrigine.options.selectedIndex].value);
	objDestination.options[objDestination.length]=ADeplacer;
	objOrigine.options[objOrigine.options.selectedIndex]=null;
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

function profileChange(pressbutton)
{
	var profile = document.getElementById('profile_id');

}

function maxlength(text,length) 
{
	if(text.value.length>length) 
		text.value=text.value.substr(0,length);
	//text.innerText pour IE à la place de text.value 
} 

function addEntry(d, id, parent, length, nodevalue, isocode)
{
	alert("id: "+id+"; parent: "+parent+"; length: "+length+"; nodevalue: "+nodevalue+"; isocode: "+isocode);
	for(i=0; i<d.aNodes.length; i++)
	{
		if (d.aNodes[i].id == 11)
		{
			alert(d.aNodes[i].id);		
		}
	}
	d.add(id, parent, '<textarea cols="50" rows="2" name ="' + id + '" onkeypress="javascript:maxlength(this,' + length+ '); ">'+ nodevalue +'</textarea>', '', 'isocode', '', '', '', true );
	document.write(d);
	//d.aNodes.splice(i, 0, new Node(id, parent, '<textarea cols="50" rows="2" name ="' + id + '" onkeypress="javascript:maxlength(this,' + length+ '); ">'+ nodevalue +'</textarea>', '', 'isocode', '', '', '', true));
}

function deleteEntry(d, id)
{
	for(i=0; i<d.aNodes.length; i++)
	{
		if (d.aNodes[i].id == 11)
		{
			alert(d.aNodes[i].id);
			d.aNodes.splice(i, 1);
			document.write(d);
		}	
	}
}
</script>