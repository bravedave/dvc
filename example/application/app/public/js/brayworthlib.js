(function($){var $w=$(window);$.fn.visible=function(partial,hidden,direction,container){if(this.length<1)
return;direction=direction||'both';var $t=this.length>1?this.eq(0):this,isContained=typeof container!=='undefined'&&container!==null,$c=isContained?$(container):$w,wPosition=isContained?$c.position():0,t=$t.get(0),vpWidth=$c.outerWidth(),vpHeight=$c.outerHeight(),clientSize=hidden===!0?t.offsetWidth*t.offsetHeight:!0;if(typeof t.getBoundingClientRect==='function'){var rec=t.getBoundingClientRect(),tViz=isContained?rec.top-wPosition.top>=0&&rec.top<vpHeight+wPosition.top:rec.top>=0&&rec.top<vpHeight,bViz=isContained?rec.bottom-wPosition.top>0&&rec.bottom<=vpHeight+wPosition.top:rec.bottom>0&&rec.bottom<=vpHeight,lViz=isContained?rec.left-wPosition.left>=0&&rec.left<vpWidth+wPosition.left:rec.left>=0&&rec.left<vpWidth,rViz=isContained?rec.right-wPosition.left>0&&rec.right<vpWidth+wPosition.left:rec.right>0&&rec.right<=vpWidth,vVisible=partial?tViz||bViz:tViz&&bViz,hVisible=partial?lViz||rViz:lViz&&rViz,vVisible=(rec.top<0&&rec.bottom>vpHeight)?!0:vVisible,hVisible=(rec.left<0&&rec.right>vpWidth)?!0:hVisible;if(direction==='both')
return clientSize&&vVisible&&hVisible;else if(direction==='vertical')
return clientSize&&vVisible;else if(direction==='horizontal')
return clientSize&&hVisible}else{var viewTop=isContained?0:wPosition,viewBottom=viewTop+vpHeight,viewLeft=$c.scrollLeft(),viewRight=viewLeft+vpWidth,position=$t.position(),_top=position.top,_bottom=_top+$t.height(),_left=position.left,_right=_left+$t.width(),compareTop=partial===!0?_bottom:_top,compareBottom=partial===!0?_top:_bottom,compareLeft=partial===!0?_right:_left,compareRight=partial===!0?_left:_right;if(direction==='both')
return!!clientSize&&((compareBottom<=viewBottom)&&(compareTop>=viewTop))&&((compareRight<=viewRight)&&(compareLeft>=viewLeft));else if(direction==='vertical')
return!!clientSize&&((compareBottom<=viewBottom)&&(compareTop>=viewTop));else if(direction==='horizontal')
return!!clientSize&&((compareRight<=viewRight)&&(compareLeft>=viewLeft))}}})(jQuery);(function(){return;if('serviceWorker' in navigator){if(/^https:/.test(window.location.href)){navigator.serviceWorker.register('/js/service-worker.js').then(function(){})}}})();if(typeof _brayworth_=='undefined')
var _brayworth_={};_brayworth_.browser={}
_brayworth_.browser.isIPhone=navigator.userAgent.toLowerCase().indexOf('iphone')>-1;_brayworth_.browser.isIPad=navigator.userAgent.toLowerCase().indexOf('ipad')>-1;_brayworth_.browser.isChromeOniOS=_brayworth_.browser.isIPhone&&navigator.userAgent.toLowerCase().indexOf('CriOS')>-1;_brayworth_.browser.isMobileDevice=_brayworth_.browser.isIPhone||_brayworth_.browser.isIPad;_brayworth_.InitHRefs=function(){$('[data-href]').each(function(i,el){$(el).css('cursor','pointer').off('click').on('click',function(evt){if(/^(a)$/i.test(evt.target.nodeName))
return;evt.stopPropagation();evt.preventDefault();if($(evt.target).closest('[data-role="contextmenu"]').length>0)
_brayworth_.hideContext($(evt.target).closest('[data-role="contextmenu"]')[0]);var target=$(this).data('target');if(target==''||target==undefined)
window.location.href=$(this).data('href');else window.open($(this).data('href'),target)})})};_brayworth_.bootstrapModalPop=function(params){if(/string/.test(typeof params)){var modal=$(this).data('modal');if(/close/i.test(params)){modal.close();return}}
var options={title:'',width:!1,autoOpen:!0,buttons:{},headButtons:{},}
$.extend(options,params);var header=$('<div class="modal-header"><i class="fa fa-times close"></i><h1></h1></div>');var body=$('<div class="modal-body"></div>');body.append(this);var footer=$('<div class="modal-footer text-right"></div>');var modal=$('<div class="modal"></div>');var wrapper=$('<div class="modal-content"></div>');if(options.width)
wrapper.css({'width':'300px'});else wrapper.addClass('modal-content-600');wrapper.append(header).append(body).appendTo(modal);var _el=$(this)
var s=_el.attr('title');$('h1',header).html('').append(s);if(Object.keys(options.buttons).length>0){$.each(options.buttons,function(i,el){var b=$('<button class="button button-raised"></button>')
b.html(i);b.on('click',function(e){el.click.call(modal,e)})
footer.append(b)})
wrapper.append(footer)}
if(Object.keys(options.headButtons).length>0){$.each(options.headButtons,function(i,el){if(!!el.icon)
var b=$('<i class="fa fa-fw pull-right" style="margin-right: 3px; padding-right: 12px; cursor: pointer;"></i>').addClass(el.icon);else var b=$('<button class="button button-raised pull-right"></button>').html(i);if(!!el.title)
b.attr('title',el.title)
b.on('click',function(e){el.click.call(modal,e)})
header.prepend(b)})
header.prepend($('.close',header))}
modal.appendTo('body');$(this).data('modal',modal.modalDialog({afterClose:function(){modal.remove();if(!!options.afterClose&&/function/.test(typeof options.afterClose))
options.afterClose.call(modal)},}))};_brayworth_.initDatePickers=function(parent){if($.fn.datepicker){if(!parent)
parent='body';$('.datepicker',parent).each(function(i,el){var bootstrap=(typeof $().scrollspy=='function');var df=$(el).data('dateformat');if(df==undefined){if(bootstrap)
df='yyyy-mm-dd';else if(jQuery.ui)
df='yy-mm-dd'}
if(bootstrap)
$(el).datepicker({format:df});else if(jQuery.ui)
$(el).datepicker({dateFormat:df})})}}
$(document).ready(function(){_brayworth_.InitHRefs();_brayworth_.initDatePickers();$('[data-role="back-button"]').each(function(i,el){$(el).css('cursor','pointer').on('click',function(evt){evt.stopPropagation();evt.preventDefault();window.history.back()})})
$('[data-role="visibility-toggle"]').each(function(i,el){var o=$(el);var target=o.data('target');var oT=$('#'+target);if(oT){o.css('cursor','pointer').on('click',function(evt){evt.stopPropagation();evt.preventDefault();if(oT.hasClass('hidden'))
oT.removeClass('hidden');else oT.addClass('hidden')})}})
$('a[href*="#"]:not([href="#"] , .carousel-control, .ui-tabs-anchor)').click(function(){if(location.pathname.replace(/^\//,'')==this.pathname.replace(/^\//,'')&&location.hostname==this.hostname){var target=$(this.hash);target=target.length?target:$('[name='+this.hash.slice(1)+']');if(target.length){if(/nav/i.test(target.prop('tagName')))
return;var tTop=target.offset().top;var nav=$('body>nav');if(nav.length)
tTop-=(nav.height()+20);tTop=Math.max(20,tTop);$('html, body').animate({scrollTop:tTop},1000);return!1}}});$('[role="print-page"]').each(function(i,el){$(el).on('click',function(e){e.preventDefault();window.print()})})});(function($){$.fn.serializeFormJSON=function(){var o={};var a=this.serializeArray();$.each(a,function(){if(o[this.name]){if(!o[this.name].push)
o[this.name]=[o[this.name]];o[this.name].push(this.value||'')}
else o[this.name]=this.value||''});return o};$.fn.growlSuccess=function(params){var options={growlClass:'success'}
if(/object/.test(typeof params))
$.extend(options,params);else if(/string/i.test(typeof params))
options.text=params;$(this).growl(options)}
$.fn.growlError=function(params){var options={growlClass:'error'}
if(/object/.test(typeof params))
$.extend(options,params);else if(/string/i.test(typeof params))
options.text=params;$(this).growl(options)}
$.fn.growlAjax=function(j){var options={growlClass:'error',text:'no description'}
if(!!j.response){if(j.response=='ack')
options.growlClass='success'}
if(!!j.description)
options.text=j.description;if(!!j.timeout)
options.timeout=j.timeout;$(this).growl(options)}
var growlers=[]
$.fn.growl=function(params){var me=$(this);var options={top:60,right:20,text:'',title:'',timeout:3000,growlClass:'information',}
if(/object/.test(typeof params))
$.extend(options,params);else if(/string/i.test(typeof params))
options.text=params;if(options.title==''&&options.text=='')
return;var growler=$('<div class="growler"></div>');var growlerIndex=-1
$.each(growlers,function(i,e){if(!e){growlerIndex=i;growlers[growlerIndex]=growler;return(!1)}});if(growlerIndex<0){growlerIndex=growlers.length;growlers[growlerIndex]=growler}
options.top*=growlerIndex;var title=$('<h3></h3>');var content=$('<div></div>');if(options.title!='')
title.html(options.title).appendTo(growler);else content.css('padding-top','5px');if(options.text!='')
content.html(options.text).appendTo(growler);growler.css({'position':'absolute','top':options.top,'right':options.right}).addClass(options.growlClass).appendTo(this);setTimeout(function(){growlers[growlerIndex]=!1;growler.remove()},options.timeout)}
$.fn.swipeOn=function(params){var options={left:function(){},right:function(){},up:function(){},down:function(){},}
$.extend(options,params);var down=!1;var touchEvent=function(e){var _touchEvent=function(x,y){return({'x':x,'y':y})}
var evt=e.originalEvent;try{if('undefined'!==typeof evt.pageX){return(_touchEvent(evt.pageX,evt.pageY))}
else if('undefined'!==typeof evt.touches){if(evt.touches.length>0)
return(_touchEvent(evt.touches[0].pageX,evt.touches[0].pageY));else return(_touchEvent(evt.changedTouches[0].pageX,evt.changedTouches[0].pageY))}}
catch(e){console.warn(e)}
return(_touchEvent(0,0))}
var swipeEvent=function(down,up){var j={'direction':'',x:up.x-down.x,y:up.y-down.y}
if(j.x>70)
j.direction='right'
else if(j.x<-70)
j.direction='left'
return(j)}
$(this).on('mousedown touchstart',function(e){if(/^(input|textarea|img|a|select)$/i.test(e.target.nodeName))
return;down=touchEvent(e)}).on('mouseup touchend',function(e){if(down){var sEvt=swipeEvent(down,touchEvent(e));down=!1;if(sEvt.direction=='left')
options.left();else if(sEvt.direction=='right')
options.right()}})}
$.fn.swipeOff=function(){$(this).off('mousedown touchstart').off('mouseup touchend')}
String.prototype.isEmail=function(){if(this.length<3)
return(!1);var emailReg=/^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;return emailReg.test(this)}})(jQuery);if(typeof _brayworth_=='undefined')
var _brayworth_={};_brayworth_.hideContext=function(el){var _el=$(el);if(!!_el.data('hide')){if(_el.data('hide')=='hide')
$(el).addClass('hidden');else $(el).remove()}
else{$(el).remove()}}
_brayworth_.hideContexts=function(){$('[data-role="contextmenu"]').each(function(i,el){_brayworth_.hideContext(el)})}
_brayworth_.context=function(){return({root:$('<ul class="menu menu-contextmenu" data-role="contextmenu"></ul>'),detachOnHide:!0,create:function(item){return($('<li></li>').append(item).appendTo(this.root))},append:function(item){this.create(item);return(this)},open:function(evt){var css={position:'absolute',top:10,left:$(document).width()-140,}
if(!!evt.pageY)
css.top=Math.max(evt.pageY-10,0);if(!!evt.pageX)
css.left=Math.max(evt.pageX-40,0);if(this.detachOnHide){this.root.css(css).appendTo('body').data('hide','detach')}
else{if(this.root.parent().length<1)
this.root.appendTo('body').data('hide','hide');this.root.css(css).removeClass('hidden')}
var offset=this.root.offset();if(offset.left+this.root.width()>$(window).width()){var l=$(window).width()-this.root.width()-5;this.root.css('left',Math.max(l,2));offset=this.root.offset()}
if(offset.top+this.root.height()>$(window).height()){var t=$(window).height()-this.root.height()-5;this.root.css('top',Math.max(t,$(window).scrollTop()+2));offset=this.root.offset()}
if(offset.left>($(window).width()-(this.root.width()*2)))
this.root.addClass('menu-contextmenu-right');else this.root.removeClass('menu-contextmenu-right');return(this)},close:function(){if(this.detachOnHide){this.root.remove()}
else{this.root.addClass('hidden')}
return(this)},remove:function(){return(this.close())},attachTo:function(parent){var _me=this;$(parent).off('click.removeContexts').on('click.removeContexts',function(evt){if($(evt.target).closest('[data-role="contextmenu"]').length>0){if(/^(a)$/i.test(evt.target.nodeName))
return}
_brayworth_.hideContexts()}).on('contextmenu',function(evt){if($(evt.target).closest('[data-role="contextmenu"]').length)
return;_brayworth_.hideContexts();if(evt.shiftKey)
return;if(/^(input|textarea|img|a|select)$/i.test(evt.target.nodeName))
return;if($(evt.target).closest('table').data('nocontextmenu')=='yes')
return;if($(evt.target).hasClass('modal')||$(evt.target).closest('.modal').length>0)
return;if($(evt.target).hasClass('ui-widget-overlay')||$(evt.target).closest('.ui-dialog').length>0)
return;if(typeof window.getSelection!="undefined"){var sel=window.getSelection();if(sel.rangeCount){if(sel.anchorNode.parentNode==evt.target){var frag=sel.getRangeAt(0).cloneContents();var text=frag.textContent;if(text.length>0)
return}}}
evt.preventDefault();_me.open(evt)});return(_me)}})};if(typeof _brayworth_=='undefined')
var _brayworth_={};_brayworth_.modal=function(params){if(/string/.test(typeof params)){var modal=$(this).data('modal');if('close'==params)
modal.close();return}
var options={title:'',width:!1,fullScreen:_brayworth_.browser.isIPhone,autoOpen:!0,buttons:{},headButtons:{},}
$.extend(options,params);var modal=$('<div class="modal"></div>');var wrapper=$('<div class="modal-content" role="dialog" aria-labelledby="modal-header-title"></div>').appendTo(modal);var header=$('<div class="modal-header"><i class="fa fa-times close"></i></div>').appendTo(wrapper);var headerH1=$('<h1 id="modal-header-title"></h1>').appendTo(header);var body=$('<div class="modal-body"></div>').append(this).appendTo(wrapper);var footer=$('<div class="modal-footer text-right"></div>');if(!!options.width)
wrapper.css({'width':options.width});var _el=(this instanceof jQuery?this:$(this));var s=_el.attr('title');headerH1.html('').append(s);if(Object.keys(options.buttons).length>0){$.each(options.buttons,function(i,el){var b=$('<button class="button button-raised"></button>')
b.html(i);b.on('click',function(e){el.click.call(modal,e)})
footer.append(b)})
wrapper.append(footer)}
if(Object.keys(options.headButtons).length>0){$.each(options.headButtons,function(i,el){if(!!el.icon)
var b=$('<i class="fa fa-fw pull-right" style="margin-right: 3px; padding-right: 12px; cursor: pointer;"></i>').addClass(el.icon);else var b=$('<button class="button button-raised pull-right"></button>').html(i);if(!!el.title)
b.attr('title',el.title)
b.on('click',function(e){el.click.call(modal,e)})
header.prepend(b)})
header.prepend($('.close',header))}
var bodyElements=[];if(options.fullScreen){$('body > *').each(function(i,el){var _el=$(el);if(!_el.hasClass('hidden')){_el.addClass('hidden');bodyElements.push(_el)}})
wrapper.css({'width':'auto','margin':0})}
var previousElement=document.activeElement;modal.appendTo('body');$(this).data('modal',_brayworth_.modalDialog.call(modal,{afterClose:function(){modal.remove();if(!!options.afterClose&&/function/.test(typeof options.afterClose))
options.afterClose.call(modal);$.each(bodyElements,function(i,el){var _el=$(el);_el.removeClass('hidden')})
previousElement.focus()},}))}
if(typeof _brayworth_=='undefined')
var _brayworth_={};$.fn.modalDialog=_brayworth_.modalDialog=function(_options){if(/string/.test(typeof(_options))){if(_options=='close'){var modal=this.data('modal');modal.close();return(modal)}}
var modal=this;var options={beforeClose:function(){},afterClose:function(){},onEnter:function(){}};$.extend(options,_options);var close=$('.close',this);modal.close=function(){options.beforeClose.call(modal);modal.css('display','none');$(window).off('click');options.afterClose.call(modal);modal=!1;$(document).unbind('keyup.modal');$(document).unbind('keypress.modal')}
modal.css('display','block').data('modal',modal);$(document).on('keyup.modal',function(e){if(e.keyCode==27){if(modal)
modal.close()}}).on('keypress.modal',function(e){if(e.keyCode==13)
options.onEnter.call(modal,e)})
close.off('click').css({cursor:'pointer'}).on('click',function(e){modal.close()});return(modal)};(function(factory){var registeredInModuleLoader=!1;if(typeof define==='function'&&define.amd){define(factory);registeredInModuleLoader=!0}
if(typeof exports==='object'){module.exports=factory();registeredInModuleLoader=!0}
if(!registeredInModuleLoader){var OldCookies=window.Cookies;var api=window.Cookies=factory();api.noConflict=function(){window.Cookies=OldCookies;return api}}}(function(){function extend(){var i=0;var result={};for(;i<arguments.length;i++){var attributes=arguments[i];for(var key in attributes){result[key]=attributes[key]}}
return result}
function init(converter){function api(key,value,attributes){var result;if(typeof document==='undefined'){return}
if(arguments.length>1){attributes=extend({path:'/'},api.defaults,attributes);if(typeof attributes.expires==='number'){var expires=new Date();expires.setMilliseconds(expires.getMilliseconds()+attributes.expires*864e+5);attributes.expires=expires}
try{result=JSON.stringify(value);if(/^[\{\[]/.test(result)){value=result}}catch(e){}
if(!converter.write){value=encodeURIComponent(String(value)).replace(/%(23|24|26|2B|3A|3C|3E|3D|2F|3F|40|5B|5D|5E|60|7B|7D|7C)/g,decodeURIComponent)}else{value=converter.write(value,key)}
key=encodeURIComponent(String(key));key=key.replace(/%(23|24|26|2B|5E|60|7C)/g,decodeURIComponent);key=key.replace(/[\(\)]/g,escape);return(document.cookie=[key,'=',value,attributes.expires?'; expires='+attributes.expires.toUTCString():'',attributes.path?'; path='+attributes.path:'',attributes.domain?'; domain='+attributes.domain:'',attributes.secure?'; secure':''].join(''))}
if(!key){result={}}
var cookies=document.cookie?document.cookie.split('; '):[];var rdecode=/(%[0-9A-Z]{2})+/g;var i=0;for(;i<cookies.length;i++){var parts=cookies[i].split('=');var cookie=parts.slice(1).join('=');if(cookie.charAt(0)==='"'){cookie=cookie.slice(1,-1)}
try{var name=parts[0].replace(rdecode,decodeURIComponent);cookie=converter.read?converter.read(cookie,name):converter(cookie,name)||cookie.replace(rdecode,decodeURIComponent);if(this.json){try{cookie=JSON.parse(cookie)}catch(e){}}
if(key===name){result=cookie;break}
if(!key){result[name]=cookie}}catch(e){}}
return result}
api.set=api;api.get=function(key){return api.call(api,key)};api.getJSON=function(){return api.apply({json:!0},[].slice.call(arguments))};api.defaults={};api.remove=function(key,attributes){api(key,'',extend(attributes,{expires:-1}))};api.withConverter=init;return api}
return init(function(){})}))