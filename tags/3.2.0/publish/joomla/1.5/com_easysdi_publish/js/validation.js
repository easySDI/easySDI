// If the element's string matches the regular expression it is numbers and letters
function isAlphanumeric(alphane)
{
	var numaric = alphane;
	for(var j=0; j<numaric.length; j++)
		{
		  var alphaa = numaric.charAt(j);
		  var hh = alphaa.charCodeAt(0);
		  if((hh > 47 && hh<58) || (hh > 64 && hh<91) || (hh > 96 && hh<123))
		  {
		  }
		else	{
			 return false;
		  }
 		}
 return true;
}


/*
function isAlphanumeric(elem){
	var alphaExp = /^[0-9a-zA-Z_-]+$/;
	if(elem.value.match(alphaExp)){
		return true;
	}else{
		return false;
	}
}
*/


// If the element's string matches the regular expression it is all letters
function isAlphabet(elem, helperMsg){
	var alphaExp = /^[a-zA-Z]+$/;
	if(elem.value.match(alphaExp)){
		return true;
	}else{
		alert(helperMsg);
		elem.focus();
		return false;
	}
}

// If the element's string matches the regular expression it is all numbers
function isNumeric(elem, helperMsg){
	var numericExpression = /^[0-9]+$/;
	if(elem.value.match(numericExpression)){
		return true;
	}else{
		alert(helperMsg);
		elem.focus();
		return false;
	}
}

// If the length of the element's string is 0 then display helper message
function notEmpty(elem, helperMsg){
	if(elem.value.length == 0){
		alert(helperMsg);
		elem.focus(); // set the focus to this input
		return false;
	}
	return true;
}


//Length restriction
function lengthRestriction(elem, min, max){
	var uInput = elem.value;
	if(uInput.length >= min && uInput.length <= max){
		return true;
	}else{
		return false;
	}
}

//Checks that a FS or Layer name does not exist

function checkNameUnique(name){
	var isUnique = true;
	var arrNames = $('existingNames').value.split(",");
	for(i=0; i<arrNames.length; i++){
		if(name.toUpperCase() == arrNames[i].toUpperCase())
			isUnique = false;
	}
	return isUnique;
}