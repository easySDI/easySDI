// <?php !! This fools phpdocumentor into parsing this file
/**
* @version		$Id: joomla.javascript.js 10389 2008-06-03 11:27:38Z pasamio $
* @package		Joomla
* @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
* @license		GNU/GPL
* Joomla! is Free Software
*/

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
