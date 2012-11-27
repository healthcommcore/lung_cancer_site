var openedRow = -1;

function showBusyImage(divid, note){
    var newImg = document.createElement('img');
    newImg.setAttribute('src', "components/com_jomcomment/busy.gif");
    newImg.setAttribute('id',"jc_busyImg");
    newImg.setAttribute('hspace',"16");
    newImg.setAttribute('align',"absmiddle");
    $(divid).innerHTML ="";
    $(divid).appendChild(newImg);
    $(divid).innerHTML += "<div align='left' style='display:inline'><b>" + note + "</b></div>";
}

function hideBusyImage(){
  var t = $("jc_busyImg");
    if(t){
        t.parentNode.removeChild(t);
    }
    t = $("ajaxInfo");
    t.innerHTML ="";
}

function $() {
	var elements = new Array();
	for (var i = 0; i < arguments.length; i++) {
		var element = arguments[i];
		if (typeof element == 'string')
			element = document.getElementById(element);
		if (arguments.length == 1)
			return element;
		elements.push(element);
	}
	return elements;
}

function XMLHttpRequestObject(){
    var http_request = false;

   if (window.XMLHttpRequest) { // Mozilla, Safari,...
       http_request = new XMLHttpRequest();
   } else if (window.ActiveXObject) { // IE
        var msxmlhttp = new Array(
				'Msxml2.XMLHTTP.5.0',
				'Msxml2.XMLHTTP.4.0',
				'Msxml2.XMLHTTP.3.0',
				'Msxml2.XMLHTTP',
				'Microsoft.XMLHTTP');
        
        for (var i = 0; i < msxmlhttp.length; i++) {
			try {
				http_request = new ActiveXObject(msxmlhttp[i]);
			} catch (e) {
				http_request = null;
			}
		}
   }

   if (!http_request) {
       alert('Unfortunatelly you browser doesn\'t support this feature.');
       return false;
   }
   
   return http_request;
}

////////////////////////////////////////////////////////////////////////////////

function submitTask(id, com, task, postData, responseFunc){

    var xmlhttp =  new XMLHttpRequestObject();
    var targetUrl = "index2.php";

    xmlhttp.open('POST', targetUrl, true);
    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4) {
            if (xmlhttp.status == 200){
                //alert(xmlhttp.responseText);
                if(responseFunc){
                    eval(responseFunc + "(xmlhttp.responseText);");
                    //alert("ok");
                } else {
                    
                    var result = eval("(" + xmlhttp.responseText + ")");
                    switch(result.action){
                        case 'changeImg':
                            var img =  $(result.target);
                            img.setAttribute('src', result.imgsrc);
                            break;
                        case 'innerHTML':
                            var div =  $(result.target);
                            div.innerHTML = result.data;
                            break;
                    }
                    clear_notification();
                    hideBusyImage();
                }
            }else {
                // warning ajax fails
            }
        }
    }
    
    var xmlReq = ''; 
    xmlReq += '&option='+ com;
    xmlReq += '&no_html=1';
    xmlReq += '&hidemainmenu=1';
    xmlReq += '&task=ajax';
    xmlReq += '&xtask=' + task; 
    xmlReq += '&id=' + id;
    if(postData){
        xmlReq += postData;
    }

    xmlhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    //alert(xmlReq);
    xmlhttp.send(xmlReq);
}

function jc_editComment(id){
    return;
    showBusyImage('ajaxInfo', "Loading...");
    submitTask(id, 'com_jomcomment', 'getEditForm', null, null );
}

function jc_languageLoaded(responseText){
    var result = eval("(" + responseText + ")");
    display_notification('ajaxInfo', result.message );
    clear_notification();
    
    $('editLangTextArea').value = result.data;
    $('currentFile').value = result.filename;
}

function jc_editLanguage(langFile){
    showBusyImage('ajaxInfo', "Loading...");
    submitTask(langFile, 'com_jomcomment', 'loadLangFile', null, 'jc_languageLoaded' );
}

function jc_languageSaved(responseText){
    //alert(responseText);
    var result = eval("(" + responseText + ")");
    display_notification('ajaxInfo', result.message );
    clear_notification();
}

function jc_saveLanguage(){
    showBusyImage('ajaxInfo', "Saving...");
    var xmlReq = '&filename=' + $('currentFile').value;
    xmlReq += '&content=' + encodeURI($('editLangTextArea').value);
    //alert(encodeURI($('editLangTextArea').value));
    submitTask($('currentFile').value, 'com_jomcomment', 'saveLangFile', xmlReq, 'jc_languageSaved' );
}

function display_notification(divName, theMessage){
    var message = "<div class=\"infolevel1\"></div>";
    message += "<div class=\"infolevel2\"></div>";
    message += "<div class=\"infolevel3\">" +theMessage+ "</div>";
    message += "<div class=\"infolevel2\"></div>";
    message += "<div class=\"infolevel1\"></div>";
          
    var d = $(divName);
    if(d != null){
        d.innerHTML = message;
    }
}
    
function clear_notification(){
  setTimeout(function(){
  var d = $('ajaxInfo');
        if(d != null){
            d.innerHTML = "";
        }
        }, 5000);
}

function jc_commentSaved(responseText){
    var result = eval("(" + responseText + ")");
    display_notification('ajaxInfo', result.message );
    clear_notification();
    
    // We need to update the displayed data
    $('name-'+result.id).innerHTML = result.name;
    $('email-'+result.id).innerHTML = result.email;
    $('website-'+result.id).innerHTML = result.website;
    $('comment-'+result.id).innerHTML = result.comment;
    $('content-'+result.id).innerHTML = result.content;
}

function jc_getEditFormValues(formid)
{
    var values = [
    ['date', $('date-edit-' + formid).value],
    ['id', $('id-edit-' + formid).value],
    ['name',$('name-edit-' + formid).value],
    ['email',$('email-edit-' + formid).value],
    ['title',$('title-edit-' + formid).value],
    ['website',$('website-edit-' + formid).value],
    ['comment',$('comment-edit-' + formid).value],
    ['contentid',$('contentid-edit-' + formid).value]];
    
    return values;
}

function jc_saveComment(id){
    return;
    
    showBusyImage('ajaxInfo', "Saving...");

    var xmlReq = '&name=' + $('name').value;
    xmlReq += '&email=' + $('email').value;
    xmlReq += '&website=' + $('website').value;
    xmlReq += '&title=' + $('title').value;
    xmlReq += '&comment=' + $('comment').value;
    if($('contentid').value != 0){
        xmlReq += '&contentid=' + $('contentid').value;
    }
    
    submitTask(id, 'com_jomcomment', 'save', xmlReq, 'jc_commentSaved' );
    toggleRowDisplay(id);
}

function jc_togglePublish(){
}

function jc_closeEdit(id){
    toggleRowDisplay(id);
}

function toggleRowDisplay(id) {
    var body=$(id);
    if (body) {
        if (body.style.display == 'none') {
            var rem = $('c' + openedRow);
            if(rem != null){
                rem.removeChild(rem.childNodes[0]);
            }
            try {
                body.style.display='table-row';
                // All is ok, display the edit forms
                // we must delete previosu DOM object
                jc_editComment(id);
                if(openedRow != -1){
                    body=$(openedRow);
                    body.style.display = 'none';
                }                
                openedRow = id;
            } catch(e) {
                body.style.display = 'block';
            }
        }
        else {
            body.style.display = 'none';
            if(openedRow == id){
              openedRow = -1;
            }
            
            var rem = $('c' + id);
            if(rem != null){
                rem.removeChild(rem.childNodes[0]);
            }
        }
    }
}

function opacity(id, opacStart, opacEnd, millisec) {
    //speed for each frame
    var speed = Math.round(millisec / 100);
    var timer = 0;

    //determine the direction for the blending, if start and end are the same nothing happens
    if(opacStart > opacEnd) {
        for(i = opacStart; i >= opacEnd; i--) {
            setTimeout("changeOpac(" + i + ",'" + id + "')",(timer * speed));
            timer++;
        }
    } else if(opacStart < opacEnd) {
        for(i = opacStart; i <= opacEnd; i++)
            {
            setTimeout("changeOpac(" + i + ",'" + id + "')",(timer * speed));
            timer++;
        }
    }
}

//change the opacity for different browsers
function changeOpac(opacity, id) {
    var object = $(id).style;
    object.opacity = (opacity / 100);
    object.MozOpacity = (opacity / 100);
    object.KhtmlOpacity = (opacity / 100);
    object.filter = "alpha(opacity=" + opacity + ")";
} 

function changeStyleOpac(opacity, object) {
    object.opacity = (opacity / 100);
    object.MozOpacity = (opacity / 100);
    object.KhtmlOpacity = (opacity / 100);
    object.filter = "alpha(opacity=" + opacity + ")";
} 
