function bp_rate(data)
{
	for (i = 1; i <= data; i++) { 
	document.getElementById("bp_rate_"+i).className='orange_star';
	}
	
}
function bp_rate_rev(data,urate)
{
	for (i = 5; i >= 1; i--) { 
	document.getElementById("bp_rate_"+i).className='blank_star';
	}
	for (k = 1; k <= urate; k++) { 
	document.getElementById("bp_rate_"+k).className='yellow_star';
	}
	
}