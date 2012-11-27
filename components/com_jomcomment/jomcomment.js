// JavaScript Document
function loading(divId, loadingImgSrc)
{
	var el=document.getElementById(divId);
	if(el)
		el.innerHTML = '<img src="'+loadingImgSrc+'" />';
}

String.prototype.trim = function() {
	a = this.replace(/^\s+/, '');
	return a.replace(/\s+$/, '');
};

function getScrollXY() {
  var scrOfX = 0, scrOfY = 0;
  if( typeof( window.pageYOffset ) == 'number' ) {
    //Netscape compliant
    scrOfY = window.pageYOffset;
    scrOfX = window.pageXOffset;
  } else if( document.body && ( document.body.scrollLeft || document.body.scrollTop ) ) {
    //DOM compliant
    scrOfY = document.body.scrollTop;
    scrOfX = document.body.scrollLeft;
  } else if( document.documentElement && ( document.documentElement.scrollLeft || document.documentElement.scrollTop ) ) {
    //IE6 standards compliant mode
    scrOfY = document.documentElement.scrollTop;
    scrOfX = document.documentElement.scrollLeft;
  }
  return [ scrOfX, scrOfY ];
}

// The data is JSON string
function showFloatingDialog(data){
	var html = "";
	// Attach onclick event to capture click outside the window
	 
	 
	// Fill up container with the main window
	//eval("html = \"" + data + "\";");
	//$('popupWindowContainer').innerHTML = html;
	
	// Show it
	var sc = getScrollXY();
	var w = 400;
	var h = 400;
	w += 32;
	h += 96;
	var wleft = 310; //((860 - w) / 2) + $('mainListingTable').getLeft();
  	var wtop = (screen.height - h) / 2 + sc[1];
	jax.$('popupWindowEditable').innerHTML = data;
	//$('popupWindowContainer').setStyle('background-color', '#F8F8F3');
	jax.$('popupWindowContainer').style.width =  w +'px';
	jax.$('popupWindowContainer').style.height =  h +'px';
	jax.$('popupWindowContainer').style.top = wtop +'px';
	jax.$('popupWindowContainer').style.left = wleft +'px';
// 	jax.$('popupWindowContainer').makeDraggable({
//     	handle: 'popupWindowHandle'
// 	});
	jax.$('popupWindowContainer').style.visibility = "visible";
}
