// JavaScript Document

//-------------------------------------------------------------------
// isBlank(string)
//   Returns false if there is a non-blank character in the string
//-------------------------------------------------------------------
function isBlank(val) {
	if (val == null || val == "") return true;
	for(var i=0;i<val.length;i++) {
		if ((val.charAt(i)!=' ')&&(val.charAt(i)!="\t")&&(val.charAt(i)!="\n")&&(val.charAt(i)!="\r")){return false;}
		}
	return true;
		
}

// Power Plan validation

function processPage2Reason(form, errmsg) {
	// alert ('processPage2Reason');
	// for (var i=0; i < form.reasonopt.length-1; i++){
	for (var i=1; i < form.reasonopt.length; i++){
		if (form.reasonopt[i].checked) return true;
	}
	// write-in option selected (or none)
	if ( isBlank(form.reason.value) ) {
	// Check that two boxes have been checked
		alert( errmsg);
		return false;
	}
	
	return true;
}

/* Are these still used ? */
function checkTextGoal() {
	reasontext= document.getElementById("reasontext");
	reasontext.checked = true;
	// alert("checkTextGoal");
}

function checkTextStrategy() {
	strategycheck= document.getElementById("strategycheck");
	strategycheck.checked = true;
}

function checkboxTextStrategy() {
	strategycheck= document.getElementById("strategycheck");
	if ( strategycheck.checked == false) {
		strategytext= document.getElementById("strategytext");
		strategytext.value = '';
	}
}

function processPage4ChooseBeh(form, errmsg) {
	// Check that one box has been checked
	var checked = false;
	for(i=0;i<form['groupID[]'].length ;i++)
	{
	if( form['groupID[]'][i].checked)
	 {
		checked = true;
	 }
	}
	if ( checked == false) {
		alert( errmsg);
		return false;
	}	
	
	return true;
}

function processPage5Support(form, errmsg) {
	// Check that one box has been checked
	if (form.support.options[form.support.selectedIndex].value == '') {
		alert( errmsg);
		return false;
	}	
	
	return true;
}


function processPage8BehBarrier(form, errmsg) {
	// Check that two boxes have been checked
	var total = 0;
	for(i=0;i<form['barrierID[]'].length ;i++)
	{
	if( form['barrierID[]'][i].checked)
	 {
		total++;
	 }
	}
	if ((total < 1 ) || (total > 2)) {
		alert( errmsg);
		return false;
	}	
	
	return true;
}

function processPage8BehStrategy(form, errmsg) {
	// Check that two boxes have been checked
	var total = 0;
	// for(i=0;i<form['groupID[]'].length ;i++)
	
	// Check only non-text ?
	for(i=0;i< (form['strategyID[]'].length) ;i++)
	{
	if( form['strategyID[]'][i].checked)
	 {
		total++;
	 }
	}
	// alert( 'total = ' + total);
	
	if ( isBlank(form.strategy.value) ) {
		// two boxes must be checked
		if ( total != 2){
			alert( errmsg);
			return false;
		}	
	}	
	else {

		// Check that one other box has been checked
		if ( total != 1 ){
			alert( errmsg);
			return false;
		}	
	}

	// write-in option selected (or none)

	
	return true;
}


