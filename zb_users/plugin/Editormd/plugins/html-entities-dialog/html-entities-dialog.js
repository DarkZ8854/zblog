(function(){var e=function(e){var t=jQuery,i="html-entities-dialog",n=[],o=[];e.fn.htmlEntitiesDialog=function(){var a,l=this,d=this.cm,s=l.lang,r=l.settings,c=r.pluginPath+i+"/",h=this.editor,f=(d.getCursor(),d.getSelection(),l.classPrefix),g=f+"dialog-"+i,u=s.dialog.htmlEntities,m=['<div class="'+f+'html-entities-box" style="width: 760px;height: 334px;margin-bottom: 8px;overflow: hidden;overflow-y: auto;">','<div class="'+f+'grid-table">',"</div>","</div>"].join("\r\n");d.focus(),h.find("."+g).length>0?(a=h.find("."+g),n=[],a.find("a").removeClass("selected"),this.dialogShowMask(a),this.dialogLockScreen(),a.show()):a=this.createDialog({name:g,title:u.title,width:800,height:475,mask:r.dialogShowMask,drag:r.dialogDraggable,content:m,lockScreen:r.dialogLockScreen,maskStyle:{opacity:r.dialogMaskOpacity,backgroundColor:r.dialogMaskBgColor},buttons:{enter:[s.buttons.enter,function(){return d.replaceSelection(n.join(" ")),this.hide().lockScreen(!1).hideMask(),!1}],cancel:[s.buttons.cancel,function(){return this.hide().lockScreen(!1).hideMask(),!1}]}});var p=a.find("."+f+"grid-table"),v=function(){if(!(o.length<1)){var i=20,l=Math.ceil(o.length/i);p.html("");for(var d=0;d<l;d++){for(var s='<div class="'+f+'grid-table-row">',r=0;r<i;r++){var c=o[d*i+r];if("undefined"!=typeof c){var h=c.name.replace("&amp;","&");s+='<a href="javascript:;" value="'+c.name+'" title="'+h+'" class="'+f+'html-entity-btn">'+h+"</a>"}}s+="</div>",p.append(s)}a.find("."+f+"html-entity-btn").bind(e.mouseOrTouch("click","touchend"),function(){t(this).toggleClass("selected"),t(this).hasClass("selected")&&n.push(t(this).attr("value"))})}};o.length<1?("function"==typeof a.loading&&a.loading(!0),t.getJSON(c+i.replace("-dialog","")+".json",function(e){"function"==typeof a.loading&&a.loading(!1),o=e,v()})):v()}};"function"==typeof require&&"object"==typeof exports&&"object"==typeof module?module.exports=e:"function"==typeof define?define.amd?define(["editormd"],function(t){e(t)}):define(function(t){var i=t("./../../editormd");e(i)}):e(window.editormd)})();