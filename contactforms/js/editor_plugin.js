tinyMCE.importPluginLanguagePack('cforms');var TinyMCE_cformsscript={getInfo:function(){return{longname:'cforms',author:'Oliver Seidel',authorurl:'http://www.deliciousdays.com',infourl:'http://www.deliciousdays.com',version:"6.0"}},initInstance:function(inst){tinyMCE.importCSS(inst.getDoc(),purl+"insertdialog.css")},getControlHTML:function(cn){switch(cn){case"cforms":return tinyMCE.getButtonHTML(cn,'lang_cforms_desc','{$pluginurl}/../images/button.gif','mcecforms')}return""},execCommand:function(editor_id,element,command,user_interface,value){var inst=tinyMCE.getInstanceById(editor_id);var focusElm=inst.getFocusElement();var doc=inst.getDoc();function getAttrib(elm,name){return elm.getAttribute(name)?elm.getAttribute(name):""}switch(command){case"mcecforms":if(!user_interface){var cf_box=new Array();cf_box['file']=purl+'insertdialog.php';cf_box['width']=380;cf_box['height']=115+(tinyMCE.isNS7?20:0)+(tinyMCE.isMSIE?0:0);tinyMCE.openWindow(cf_box,{editor_id:editor_id,inline:"yes"});tinyMCE.triggerNodeChange(false);return true}}return false},handleEvent:function(e){return this._moveSelection(e,tinyMCE.selectedInstance)},cleanup:function(type,content){switch(type){case"insert_to_editor":var startPos=0;while((startPos=content.indexOf('<!--cforms',startPos))!=-1){var endPos=content.indexOf('-->',startPos)+3;var no=content.substring(startPos+10,endPos-3);if(no=='')no=1;var contentAfter=content.substring(endPos);content=content.substring(0,startPos);content+='<p id="cf'+no+'" class="mce_plugin_cforms_img">'+formnames[no-1]+'</p>';content+=contentAfter;startPos++}if(tinyMCE.isMSIE||tinyMCE.isOpera){var editClass=tinyMCE.getParam("noneditable_editable_class","mceItemEditable");var nonEditClass='mce_plugin_cforms_img';content=content.replace(new RegExp("class=\"(.*)("+editClass+")([^\"]*)\"","gi"),'class="$1$2$3" contenteditable="true"');content=content.replace(new RegExp("class=\"(.*)("+nonEditClass+")([^\"]*)\"","gi"),'class="$1$2$3" contenteditable="false"')}break;case"get_from_editor":var startPos=-1;while((startPos=content.indexOf('<p',startPos+1))!=-1){var endPos=content.indexOf('/p>',startPos);var attribs=this._parseAttributes(content.substring(startPos+2,endPos));if(attribs['class']=="mce_plugin_cforms_img"){endPos+=3;if(attribs['id']!='cf'){var no=attribs['id'].substr(2);no=(no==1)?'':no}else no='';var embedHTML='<!--cforms'+no+'-->';chunkBefore=content.substring(0,startPos);chunkAfter=content.substring(endPos);content=chunkBefore+embedHTML+chunkAfter}}break;case"insert_to_editor_dom":var nodes=tinyMCE.getNodeTree(content,new Array(),1);var editClass=tinyMCE.getParam("noneditable_editable_class","mceItemEditable");var nonEditClass='mce_plugin_cforms_img';for(var i=0;i<nodes.length;i++){var elm=nodes[i];var editable=tinyMCE.getAttrib(elm,"contenteditable");if(new RegExp("true|false","gi").test(editable))TinyMCE_cformsscript._setEditable(elm,editable=="true");if(tinyMCE.isMSIE){var className=elm.className?elm.className:"";if(className.indexOf(editClass)!=-1)elm.contentEditable=true;if(className.indexOf(nonEditClass)!=-1)elm.contentEditable=false}}break;case"get_from_editor_dom":if(tinyMCE.getParam("noneditable_leave_contenteditable",false)){var nodes=tinyMCE.getNodeTree(content,new Array(),1);for(var i=0;i<nodes.length;i++)nodes[i].removeAttribute("contenteditable")}break}return content},handleNodeChange:function(editor_id,node,undo_index,undo_levels,visual_aid,any_selection){tinyMCE.switchClass(editor_id+'_cforms','mceButtonNormal');if(node==null)return;do{if(node.nodeName.toLowerCase()=="input"&&tinyMCE.getAttrib(node,'class').indexOf('mce_plugin_cforms_img')==0)tinyMCE.switchClass(editor_id+'_cforms','mceButtonSelected')}while((node=node.parentNode));return true},_moveSelection:function(e,inst){var s,r,sc,ec,el,c='mce_plugin_cforms_img';if(!inst)return true;if(tinyMCE.isGecko||tinyMCE.isOpera){s=inst.selection.getSel();r=s.getRangeAt(0);sc=tinyMCE.getParentNode(r.startContainer,function(n){return tinyMCE.hasCSSClass(n,c)});ec=tinyMCE.getParentNode(r.endContainer,function(n){return tinyMCE.hasCSSClass(n,c)});sc&&r.setStartBefore(sc);ec&&r.setEndAfter(ec);if(sc||ec){if(e.type=='keypress'&&e.keyCode==39){el=sc||ec}s.removeAllRanges();s.addRange(r);return tinyMCE.cancelEvent(e)}}return true},_setEditable:function(elm,state){var editClass=tinyMCE.getParam("noneditable_editable_class","mceItemEditable");var nonEditClass='mce_plugin_cforms_img';var className=elm.className?elm.className:"";if(className.indexOf(editClass)!=-1||className.indexOf(nonEditClass)!=-1)return;if((className=tinyMCE.getAttrib(elm,"class"))!="")className+=" ";className+=state?editClass:nonEditClass;elm.setAttribute("class",className);elm.className=className},_parseAttributes:function(attribute_string){var attributeName="";var attributeValue="";var withInName;var withInValue;var attributes=new Array();var whiteSpaceRegExp=new RegExp('^[ \n\r\t]+','g');if(attribute_string==null||attribute_string.length<2)return null;withInName=withInValue=false;for(var i=0;i<attribute_string.length;i++){var chr=attribute_string.charAt(i);if((chr=='"'||chr=="'")&&!withInValue)withInValue=true;else if((chr=='"'||chr=="'")&&withInValue){withInValue=false;var pos=attributeName.lastIndexOf(' ');if(pos!=-1)attributeName=attributeName.substring(pos+1);attributes[attributeName.toLowerCase()]=attributeValue.substring(1);attributeName="";attributeValue=""}else if(!whiteSpaceRegExp.test(chr)&&!withInName&&!withInValue)withInName=true;if(chr=='='&&withInName)withInName=false;if(withInName)attributeName+=chr;if(withInValue)attributeValue+=chr}return attributes}};tinyMCE.addPlugin("cforms",TinyMCE_cformsscript);
