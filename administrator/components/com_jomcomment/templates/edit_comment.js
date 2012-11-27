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

	// Show it
	var sc = getScrollXY();
	var w = 400;
	var h = 400;
	w += 32;
	h += 96;
	var wleft = 310;
  	var wtop = (screen.height - h) / 2 + sc[1];

    

	jQuery('#popupWindowEditable').html(data);
	jQuery('#popupWindowContainer').css('width', w+ 'px');
	jQuery('#popupWindowContainer').css('height', h+ 'px');
	jQuery('#popupWindowContainer').css('top', wtop + 'px');
	jQuery('#popupWindowContainer').css('left', wleft + 'px');
	jQuery('#popupWindowContainer').draggable();
	jQuery('#popupWindowContainer').css('visibility','visible');
}