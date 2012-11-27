var jcFadeTimer; /*handler to the fade timer*/
var jcTextareHeight = 65;

function jcResizeComment(d)
{
var el = jax.$("jc_comment");
var h = parseInt(el.style.height);
if(!h && el.offsetHeight)
h = el.offsetHeight;
if(d > 0)
h += 15;
else
h -= 15;
if(h < 20)
h = 30;
el.style.height = h+"px";

if(jcTextareHeight > 0){
	jcTextareHeight = jcTextareHeight - 15;
	setTimeout ('jcResizeComment('+ d + ')', 3);
} else {
	jcTextareHeight = 65
}
} 

function jc_showBusyImage(){
    clearTimeout(jcFadeTimer);
    jcOpacity('jc_busyDiv', 0, 100,0);
    jax.$("jc_busyDiv").innerHTML = "";
    var newImg = document.createElement('img');
    newImg.setAttribute('src', jc_livesite_busyImg);
    newImg.setAttribute('id',"jc_busyImg");
    jax.$("jc_busyDiv").appendChild(newImg);
}

function jc_removeBusyImage(){
    var t = jax.$("jc_busyImg");
    if(t != null){
        t.parentNode.removeChild(t);
    }
}

function jc_toggle_visibility(id) {
       var e = jax.$(id);
       if(e.style.display == 'block')
          e.style.display = 'none';
       else
          e.style.display = 'block';
    }
    
    function jc_hide_block(id){
    	var e = jax.$(id);
    	if(e != null)
        	e.style.display = 'none';
	}

function jcRandomString() {
	var chars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXTZabcdefghiklmnopqrstuvwxyz";
	var string_length = 12;
	var randomstring = '';
	for (var i=0; i<string_length; i++) {
		var rnum = Math.floor(Math.random() * chars.length);
		randomstring += chars.substring(rnum,rnum+1);
	}
	
	return randomstring;
}

function previewComment(){
	jax.call('jomcomment','jcxPreviewComment', jax.$('jc_comment').value);
	return;
}

function addComments(){
    jc_showBusyImage();
    jax.call('jomcomment', 'jcxAddComment', jax.getFormValues('jc_commentForm'));
    
    if(jax.$("jc_name")){
    	if(jax.$("jc_name").value){
			jc_createCookie('jc_name', jax.$("jc_name").value, 14);
		}
	}
	
    if(jax.$("jc_email")){
    	if(jax.$("jc_email").value){
    		jc_createCookie('jc_email', jax.$("jc_email").value, 14);
    	}
	}
	   
    if(jax.$("jc_website")){
    	if(jax.$("jc_website").value){
			jc_createCookie('jc_website', jax.$("jc_website").value, 14);
		}
	}
    
    var rememberMe = true;
    if(jax.$("jc_rememberInfo"))    rememberMe = jax.$("jc_rememberInfo").checked;
    if(!rememberMe){
        jc_eraseCookie("jc_name");
        jc_eraseCookie("jc_email");
        jc_eraseCookie("jc_website");
    }
    return;
}

   
function jc_update(){
    if(jax.$("jc_contentid")!= null & jax.$("jc_option") != null & jax.$("jc_numComment")){
        jax.call('jomcomment','jcxUpdateComment', jax.$("jc_contentid").value, jax.$("jc_option").value, jax.$("jc_numComment").innerHTML);
    }
}

function jc_unpublishPost(postId, opt, id){
    jax.call('jomcomment','jcxUnpublish', postId, opt);
    if(jax.$(postId) != "undefined"){
        jax.$(postId).style.border = "medium solid #FF0000";
        jcOpacity(postId,  100, 50, 200);
    } 
}

function jc_commentAreaToggle(){
    if(typeof(jc_commentArea) != "undefined"){
        jc_commentArea.toggle();
        var img = jax.$("toggleAreaImg");
        if(img != null){
            var imgSrc = img.src;
            if (jc_commentArea.el.offsetHeight > 0){
                imgSrc = imgSrc.replace("min.gif", "max.gif");
            } else{
                imgSrc = imgSrc.replace("max.gif", "min.gif");
            }
            img.src = imgSrc;
        }
    }
}

function jc_commentFormToggle(){
    if(typeof(jc_commentFormArea) != "undefined"){
        jc_commentFormArea.toggle();
        var img = jax.$("toggleFormImg");
        if(img != null){
            var imgSrc = img.src;
            if (jc_commentFormArea.el.offsetHeight > 0){
                imgSrc = imgSrc.replace("min.gif", "max.gif");
            } else{
                imgSrc = imgSrc.replace("max.gif", "min.gif");
            }
            img.src = imgSrc;
        }
    }
}

function jc_disableForm(){
    if(jax.$("jc_name")) jax.$("jc_name").disabled = true;
    if(jax.$("jc_email")) jax.$("jc_email").disabled = true; 
    if(jax.$("jc_website")) jax.$("jc_website").disabled = true; 
    if(jax.$("jc_title")) jax.$("jc_title").disabled = true; 
    if(jax.$("jc_comment")) jax.$("jc_comment").disabled = true; 
    if(jax.$("jc_password")) jax.$("jc_password").disabled = true; 
    if(jax.$("jc_submit")) jax.$("jc_submit").disabled = true; 
}

function jc_enableForm(){
    if(jax.$("jc_name")) jax.$("jc_name").disabled = false;
    if(jax.$("jc_email")) jax.$("jc_email").disabled = false; 
    if(jax.$("jc_website")) jax.$("jc_website").disabled = false; 
    if(jax.$("jc_title")) jax.$("jc_title").disabled = false; 
    if(jax.$("jc_comment")) jax.$("jc_comment").disabled = false; 
    if(jax.$("jc_password")) jax.$("jc_password").disabled = false; 
    if(jax.$("jc_submit")) jax.$("jc_submit").disabled = false; 
}
function jc_showComment(entry, id){
	var newCmt = document.createElement('div');
    newCmt.innerHTML = entry;
    jax.$(id).innerHTML = newCmt.firstChild.innerHTML;
}
function jc_insertNewEntry(entry, id){
    var comment_div = jax.$("jc_commentsDiv");
    var newCmt = document.createElement('div');
    newCmt.innerHTML = entry;
    if(navigator.userAgent.indexOf('Safari') == -1)
		jcChangeStyleOpac(0, newCmt.style);
		
    if(jc_orderBy == "0"){
        comment_div.insertBefore(newCmt,comment_div.firstChild);
    } else {
        comment_div.appendChild(newCmt);
    }
    newCmt.setAttribute("id", id);
    
	if(navigator.userAgent.indexOf('Safari') == -1)
    	jcOpacity(id, 0, 100, 500); 
}

function jc_fadeMessage(){
    clearTimeout(jcFadeTimer);
    jcFadeTimer = setTimeout("jcOpacity('jc_busyDiv', 100, 0,1000)", 3000);
}


function jcOpacity(id, opacStart, opacEnd, millisec) {
    var speed = Math.round(millisec / 100);
    var timer = 0;

    //determine the direction for the blending, if start and end are the same nothing happens
    if(opacStart > opacEnd) {
        for(i = opacStart; i >= opacEnd; i--) {
            setTimeout("jcChangeOpac(" + i + ",'" + id + "')",(timer * speed));
            timer++;
        }
    } else if(opacStart < opacEnd) {
        for(i = opacStart; i <= opacEnd; i++){
            setTimeout("jcChangeOpac(" + i + ",'" + id + "')",(timer * speed));
            timer++;
        }
    }
}

//change the opacity for different browsers
function jcChangeOpac(opacity, id) {
    var object = jax.$(id).style;
    object.opacity = (opacity / 100);
    object.MozOpacity = (opacity / 100);
    object.KhtmlOpacity = (opacity / 100);
    object.filter = "alpha(opacity=" + opacity + ")";
} 

function jcChangeStyleOpac(opacity, object) {
    object.opacity = (opacity / 100);
    object.MozOpacity = (opacity / 100);
    object.KhtmlOpacity = (opacity / 100);
    object.filter = "alpha(opacity=" + opacity + ")";
} 

function jc_createCookie(name, value, days){
    var expires = "";
    
	if (days) {
        var date = new Date();
        date.setTime(date.getTime()+(days*24*60*60*1000));
        var expires = "; expires="+date.toGMTString();
    }
    
	document.cookie = name+"="+value+expires+"; path=/";
}

function jc_readCookie(name){
    var ca = document.cookie.split(';');
    var nameEQ = name + "=";
    for(var i=0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1, c.length); //delete spaces
            if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
    }
    return "";
}

function jc_eraseCookie(name){
  jc_createCookie(name, "", -1);
}

////////////////////////////////////////////////////////////////////////////////
function jc_init(){
    if((typeof jc_update_period != "undefined")  && jc_autoUpdate){
        setTimeout("jc_update()", jc_update_period);
    }
}

function jc_addText(text, textarea){
	 var comment = jax.$(textarea).value;
	 comment = comment + text;
	 jax.$(textarea).value = comment;
}

// Surrounds the selected text with text1 and text2.
function jc_encloseText(text1, text2, textarea)
{
	// Can a text range be created?
	if (typeof(textarea.caretPos) != "undefined" && textarea.createTextRange)
	{
		var caretPos = textarea.caretPos, temp_length = caretPos.text.length;
		caretPos.text = caretPos.text.charAt(caretPos.text.length - 1) == ' ' ? text1 + caretPos.text + text2 + ' ' : text1 + caretPos.text + text2;

		if (temp_length == 0)
		{
			caretPos.moveStart("character", -text2.length);
			caretPos.moveEnd("character", -text2.length);
			caretPos.select();
		}
		else
			textarea.focus(caretPos);
	}
	// Mozilla text range wrap.
	else if (typeof(textarea.selectionStart) != "undefined")
	{
		var begin = textarea.value.substr(0, textarea.selectionStart);
		var selection = textarea.value.substr(textarea.selectionStart, textarea.selectionEnd - textarea.selectionStart);
		var end = textarea.value.substr(textarea.selectionEnd);
		var newCursorPos = textarea.selectionStart;
		var scrollPos = textarea.scrollTop;

		textarea.value = begin + text1 + selection + text2 + end;

		if (textarea.setSelectionRange)
		{
			if (selection.length == 0)
				textarea.setSelectionRange(newCursorPos + text1.length, newCursorPos + text1.length);
			else
				textarea.setSelectionRange(newCursorPos, newCursorPos + text1.length + selection.length + text2.length);
			textarea.focus();
		}
		textarea.scrollTop = scrollPos;
	}
	// Just put them on the end, then.
	else
	{
		textarea.value += text1 + text2;
		textarea.focus(textarea.value.length - 1);
	}
}

// function slideContent(divName,direction)
// {
// 	
// 	var obj =jax.$(divName);
// 	//var contentObj = jax.$('dhtmlgoodies_ac' + inputId);
// 	height = obj.clientHeight;
// 	if(height==0)height = obj.offsetHeight;
// 	height = height + direction;
// 	rerunFunction = true;
// 	//if(height>contentObj.offsetHeight){
// 	//	height = contentObj.offsetHeight;
// 	//	rerunFunction = false;
// 	//}
// 	if(height<=1){
// 		height = 1;
// 		rerunFunction = false;
// 	}
// 
// 	obj.style.height = height + 'px';
// 	//var topPos = height - contentObj.offsetHeight;
// 	//if(topPos>0)topPos=0;
// 	//contentObj.style.top = topPos + 'px';
// 	
// 	if(rerunFunction){
// 		setTimeout('slideContent(\'' + divName + '\',' + direction + ')', 100);
// 	}else{
// 		if(height<=1){
// 			obj.style.display='none'; 
// 			
// 		}else{
// 			//dhtmlgoodies_activeId = inputId;
// 			//dhtmlgoodies_slideInProgress = false;
// 		}
// 	}
// }



function jc_toggleDiv(divName){
	var e = jax.$(divName);
	
	if(!e.style.display || e.style.display=='none'){
		e.style.display='block';
		e.style.visibility = 'visible';
	} else {
		e.style.display='none';
		e.style.visibility = 'hidden';
	}
}
setTimeout("jc_init()", 20000);

function azrulWindowInit(anchor, width, height){
	Obj = jax.$("jcAzrulWindow");

	if(!Obj){
		Obj  = document.createElement('div');
		Obj.setAttribute("id", "jcAzrulWindow");
		Obj.setAttribute("style", "left:10px;top:10px;position:absolute;visibility:hidden;");
		
		var html  = '<table id="jcAzrulWindowtable"cellpadding="0" cellspacing="0" width="100%"><tr>';
		html  += '<td colspan="2" id="jcAzrulWindowContent">';
		html  += '<div class="dialog_header">Loading</div>';
		html  += '		 <div class="dialog_content">';
		html  += '			<div id="dialog_body" class="dialog_body">';
		html  += '				Please wait';
		html  += '			</div>';
		html  += '		 </div>';
		html  += '</td></tr>';
		html  += '</table>';
		
		Obj.innerHTML = html;
		document.body.appendChild(Obj);
	}

	var myWidth = 0, myHeight = 0;
	if( typeof( window.innerWidth ) == 'number' ) {
		//Non-IE
		myWidth = window.innerWidth;
		myHeight = window.innerHeight;
	} else if( document.documentElement && ( document.documentElement.clientWidth || document.documentElement.clientHeight ) ) {
		//IE 6+ in 'standards compliant mode'
		myWidth = document.documentElement.clientWidth;
		myHeight = document.documentElement.clientHeight;
	} else if( document.body && ( document.body.clientWidth || document.body.clientHeight ) ) {
		//IE 4 compatible
		myWidth = document.body.clientWidth;
		myHeight = document.body.clientHeight;
	}

	var yPos;
	if (window.innerHeight != null){
		yPos = window.innerHeight;
	} else if (document.documentElement && document.documentElement.clientHeight){
		yPos = document.documentElement.clientHeight;
	} else{
		yPos = document.body.clientHeight;
	}
	yPos = yPos-120;
	
	var iframe = jax.$("jcAzrulWindow");
	iframe.style.height = 'auto';
	iframe.style.width = width + 'px';

	var curWin  = jax.$(anchor);
	
	var h = window.pageYOffset ||
           document.body.scrollTop ||
           document.documentElement.scrollTop;
    
	iframe.style.top  = (h +(myHeight - iframe.offsetHeight)/2 - 50) + 'px';
	iframe.style.left = (jcGetPosX(curWin) + ((curWin.offsetWidth - iframe.offsetWidth)/2)) + 'px';
	
	iframe.style.position = 'absolute';
}

function azrulShowWindow(anchor, content, width, height){
	if(!width) width = 350;
	if(!height) height = 300;
	
	// Initialize current window
	azrulWindowInit(anchor, width, height);
	jax.$('jcAzrulWindow').style.visibility = 'visible';
	

	if(content){
		jax.$('jcAzrulWindowContent').innerHTML = content;
	}
	
	jax.$('jcAzrulWindow').style.zIndex = jcGetZIndexMax() + 1;
}

function azrulHideWindow(){
	jcOpacity('jcAzrulWindow', 100, 0,500);
	setTimeout('document.body.removeChild(jax.$(\'jcAzrulWindow\'))', 1000);
}

function jcGetZIndexMax(){
	var allElems = document.getElementsByTagName?
	document.getElementsByTagName("*"):
	document.all; // or test for that too
	var maxZIndex = 0;

	for(var i=0;i<allElems.length;i++) {
		var elem = allElems[i];
		var cStyle = null;
		if (elem.currentStyle) {cStyle = elem.currentStyle;}
		else if (document.defaultView && document.defaultView.getComputedStyle) {
			cStyle = document.defaultView.getComputedStyle(elem,"");
		}
		var sNum;
		if (cStyle) {
			sNum = Number(cStyle.zIndex);
		} else {
			sNum = Number(elem.style.zIndex);
		}
		if (!isNaN(sNum)) {
			maxZIndex = Math.max(maxZIndex,sNum);
		}
	}
	return maxZIndex;
}

function jcFindPos(obj) {
	var curleft = curtop = 0;
	if (obj.offsetParent) {
		curleft = obj.offsetLeft;
		curtop = obj.offsetTop;
		while (obj = obj.offsetParent) {
			curleft += obj.offsetLeft;
			curtop += obj.offsetTop;
		}
	}
	return [curleft,curtop];
}

function jcGetPosX(elem){
	var pos = jcFindPos(elem);
	return pos[0];
}

function jcGetPosY(elem){
	var pos = jcFindPos(elem);
	return pos[1];
}

function jcRpt(id){
jax.call('jomcomment', 'jcxReport', id, 'com_content',  window.location + '#comment-' + id);
}

function jcVt(id, v){
jax.call('jomcomment', 'jcxVote', v, id, 'com_content');
}
