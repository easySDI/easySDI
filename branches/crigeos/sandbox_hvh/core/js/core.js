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