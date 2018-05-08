/*
Main Javascript file (main.js)
Table of Contents :
- Top loader - pace 1.0.0 minified (line 8)
- Scrolling - Slim scroll 1.3.1 minified (line 10)
- Custom Code - 1.1 (line 12)
*/
// Pace
(function(){var a,b,c,d,e,f,g,h,i,j,k,l,m,n,o,p,q,r,s,t,u,v,w,x,y,z,A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,W,X=[].slice,Y={}.hasOwnProperty,Z=function(a,b){function c(){this.constructor=a}for(var d in b)Y.call(b,d)&&(a[d]=b[d]);return c.prototype=b.prototype,a.prototype=new c,a.__super__=b.prototype,a},$=[].indexOf||function(a){for(var b=0,c=this.length;c>b;b++)if(b in this&&this[b]===a)return b;return-1};for(u={catchupTime:100,initialRate:.03,minTime:250,ghostTime:100,maxProgressPerFrame:20,easeFactor:1.25,startOnPageLoad:!0,restartOnPushState:!0,restartOnRequestAfter:500,target:"body",elements:{checkInterval:100,selectors:["body"]},eventLag:{minSamples:10,sampleCount:3,lagThreshold:3},ajax:{trackMethods:["GET"],trackWebSockets:!0,ignoreURLs:[]}},C=function(){var a;return null!=(a="undefined"!=typeof performance&&null!==performance&&"function"==typeof performance.now?performance.now():void 0)?a:+new Date},E=window.requestAnimationFrame||window.mozRequestAnimationFrame||window.webkitRequestAnimationFrame||window.msRequestAnimationFrame,t=window.cancelAnimationFrame||window.mozCancelAnimationFrame,null==E&&(E=function(a){return setTimeout(a,50)},t=function(a){return clearTimeout(a)}),G=function(a){var b,c;return b=C(),(c=function(){var d;return d=C()-b,d>=33?(b=C(),a(d,function(){return E(c)})):setTimeout(c,33-d)})()},F=function(){var a,b,c;return c=arguments[0],b=arguments[1],a=3<=arguments.length?X.call(arguments,2):[],"function"==typeof c[b]?c[b].apply(c,a):c[b]},v=function(){var a,b,c,d,e,f,g;for(b=arguments[0],d=2<=arguments.length?X.call(arguments,1):[],f=0,g=d.length;g>f;f++)if(c=d[f])for(a in c)Y.call(c,a)&&(e=c[a],null!=b[a]&&"object"==typeof b[a]&&null!=e&&"object"==typeof e?v(b[a],e):b[a]=e);return b},q=function(a){var b,c,d,e,f;for(c=b=0,e=0,f=a.length;f>e;e++)d=a[e],c+=Math.abs(d),b++;return c/b},x=function(a,b){var c,d,e;if(null==a&&(a="options"),null==b&&(b=!0),e=document.querySelector("[data-pace-"+a+"]")){if(c=e.getAttribute("data-pace-"+a),!b)return c;try{return JSON.parse(c)}catch(f){return d=f,"undefined"!=typeof console&&null!==console?console.error("Error parsing inline pace options",d):void 0}}},g=function(){function a(){}return a.prototype.on=function(a,b,c,d){var e;return null==d&&(d=!1),null==this.bindings&&(this.bindings={}),null==(e=this.bindings)[a]&&(e[a]=[]),this.bindings[a].push({handler:b,ctx:c,once:d})},a.prototype.once=function(a,b,c){return this.on(a,b,c,!0)},a.prototype.off=function(a,b){var c,d,e;if(null!=(null!=(d=this.bindings)?d[a]:void 0)){if(null==b)return delete this.bindings[a];for(c=0,e=[];c<this.bindings[a].length;)e.push(this.bindings[a][c].handler===b?this.bindings[a].splice(c,1):c++);return e}},a.prototype.trigger=function(){var a,b,c,d,e,f,g,h,i;if(c=arguments[0],a=2<=arguments.length?X.call(arguments,1):[],null!=(g=this.bindings)?g[c]:void 0){for(e=0,i=[];e<this.bindings[c].length;)h=this.bindings[c][e],d=h.handler,b=h.ctx,f=h.once,d.apply(null!=b?b:this,a),i.push(f?this.bindings[c].splice(e,1):e++);return i}},a}(),j=window.Pace||{},window.Pace=j,v(j,g.prototype),D=j.options=v({},u,window.paceOptions,x()),U=["ajax","document","eventLag","elements"],Q=0,S=U.length;S>Q;Q++)K=U[Q],D[K]===!0&&(D[K]=u[K]);i=function(a){function b(){return V=b.__super__.constructor.apply(this,arguments)}return Z(b,a),b}(Error),b=function(){function a(){this.progress=0}return a.prototype.getElement=function(){var a;if(null==this.el){if(a=document.querySelector(D.target),!a)throw new i;this.el=document.createElement("div"),this.el.className="pace pace-active",document.body.className=document.body.className.replace(/pace-done/g,""),document.body.className+=" pace-running",this.el.innerHTML='<div class="pace-progress">\n  <div class="pace-progress-inner"></div>\n</div>\n<div class="pace-activity"></div>',null!=a.firstChild?a.insertBefore(this.el,a.firstChild):a.appendChild(this.el)}return this.el},a.prototype.finish=function(){var a;return a=this.getElement(),a.className=a.className.replace("pace-active",""),a.className+=" pace-inactive",document.body.className=document.body.className.replace("pace-running",""),document.body.className+=" pace-done"},a.prototype.update=function(a){return this.progress=a,this.render()},a.prototype.destroy=function(){try{this.getElement().parentNode.removeChild(this.getElement())}catch(a){i=a}return this.el=void 0},a.prototype.render=function(){var a,b,c,d,e,f,g;if(null==document.querySelector(D.target))return!1;for(a=this.getElement(),d="translate3d("+this.progress+"%, 0, 0)",g=["webkitTransform","msTransform","transform"],e=0,f=g.length;f>e;e++)b=g[e],a.children[0].style[b]=d;return(!this.lastRenderedProgress||this.lastRenderedProgress|0!==this.progress|0)&&(a.children[0].setAttribute("data-progress-text",""+(0|this.progress)+"%"),this.progress>=100?c="99":(c=this.progress<10?"0":"",c+=0|this.progress),a.children[0].setAttribute("data-progress",""+c)),this.lastRenderedProgress=this.progress},a.prototype.done=function(){return this.progress>=100},a}(),h=function(){function a(){this.bindings={}}return a.prototype.trigger=function(a,b){var c,d,e,f,g;if(null!=this.bindings[a]){for(f=this.bindings[a],g=[],d=0,e=f.length;e>d;d++)c=f[d],g.push(c.call(this,b));return g}},a.prototype.on=function(a,b){var c;return null==(c=this.bindings)[a]&&(c[a]=[]),this.bindings[a].push(b)},a}(),P=window.XMLHttpRequest,O=window.XDomainRequest,N=window.WebSocket,w=function(a,b){var c,d,e,f;f=[];for(d in b.prototype)try{e=b.prototype[d],f.push(null==a[d]&&"function"!=typeof e?a[d]=e:void 0)}catch(g){c=g}return f},A=[],j.ignore=function(){var a,b,c;return b=arguments[0],a=2<=arguments.length?X.call(arguments,1):[],A.unshift("ignore"),c=b.apply(null,a),A.shift(),c},j.track=function(){var a,b,c;return b=arguments[0],a=2<=arguments.length?X.call(arguments,1):[],A.unshift("track"),c=b.apply(null,a),A.shift(),c},J=function(a){var b;if(null==a&&(a="GET"),"track"===A[0])return"force";if(!A.length&&D.ajax){if("socket"===a&&D.ajax.trackWebSockets)return!0;if(b=a.toUpperCase(),$.call(D.ajax.trackMethods,b)>=0)return!0}return!1},k=function(a){function b(){var a,c=this;b.__super__.constructor.apply(this,arguments),a=function(a){var b;return b=a.open,a.open=function(d,e){return J(d)&&c.trigger("request",{type:d,url:e,request:a}),b.apply(a,arguments)}},window.XMLHttpRequest=function(b){var c;return c=new P(b),a(c),c};try{w(window.XMLHttpRequest,P)}catch(d){}if(null!=O){window.XDomainRequest=function(){var b;return b=new O,a(b),b};try{w(window.XDomainRequest,O)}catch(d){}}if(null!=N&&D.ajax.trackWebSockets){window.WebSocket=function(a,b){var d;return d=null!=b?new N(a,b):new N(a),J("socket")&&c.trigger("request",{type:"socket",url:a,protocols:b,request:d}),d};try{w(window.WebSocket,N)}catch(d){}}}return Z(b,a),b}(h),R=null,y=function(){return null==R&&(R=new k),R},I=function(a){var b,c,d,e;for(e=D.ajax.ignoreURLs,c=0,d=e.length;d>c;c++)if(b=e[c],"string"==typeof b){if(-1!==a.indexOf(b))return!0}else if(b.test(a))return!0;return!1},y().on("request",function(b){var c,d,e,f,g;return f=b.type,e=b.request,g=b.url,I(g)?void 0:j.running||D.restartOnRequestAfter===!1&&"force"!==J(f)?void 0:(d=arguments,c=D.restartOnRequestAfter||0,"boolean"==typeof c&&(c=0),setTimeout(function(){var b,c,g,h,i,k;if(b="socket"===f?e.readyState<2:0<(h=e.readyState)&&4>h){for(j.restart(),i=j.sources,k=[],c=0,g=i.length;g>c;c++){if(K=i[c],K instanceof a){K.watch.apply(K,d);break}k.push(void 0)}return k}},c))}),a=function(){function a(){var a=this;this.elements=[],y().on("request",function(){return a.watch.apply(a,arguments)})}return a.prototype.watch=function(a){var b,c,d,e;return d=a.type,b=a.request,e=a.url,I(e)?void 0:(c="socket"===d?new n(b):new o(b),this.elements.push(c))},a}(),o=function(){function a(a){var b,c,d,e,f,g,h=this;if(this.progress=0,null!=window.ProgressEvent)for(c=null,a.addEventListener("progress",function(a){return h.progress=a.lengthComputable?100*a.loaded/a.total:h.progress+(100-h.progress)/2},!1),g=["load","abort","timeout","error"],d=0,e=g.length;e>d;d++)b=g[d],a.addEventListener(b,function(){return h.progress=100},!1);else f=a.onreadystatechange,a.onreadystatechange=function(){var b;return 0===(b=a.readyState)||4===b?h.progress=100:3===a.readyState&&(h.progress=50),"function"==typeof f?f.apply(null,arguments):void 0}}return a}(),n=function(){function a(a){var b,c,d,e,f=this;for(this.progress=0,e=["error","open"],c=0,d=e.length;d>c;c++)b=e[c],a.addEventListener(b,function(){return f.progress=100},!1)}return a}(),d=function(){function a(a){var b,c,d,f;for(null==a&&(a={}),this.elements=[],null==a.selectors&&(a.selectors=[]),f=a.selectors,c=0,d=f.length;d>c;c++)b=f[c],this.elements.push(new e(b))}return a}(),e=function(){function a(a){this.selector=a,this.progress=0,this.check()}return a.prototype.check=function(){var a=this;return document.querySelector(this.selector)?this.done():setTimeout(function(){return a.check()},D.elements.checkInterval)},a.prototype.done=function(){return this.progress=100},a}(),c=function(){function a(){var a,b,c=this;this.progress=null!=(b=this.states[document.readyState])?b:100,a=document.onreadystatechange,document.onreadystatechange=function(){return null!=c.states[document.readyState]&&(c.progress=c.states[document.readyState]),"function"==typeof a?a.apply(null,arguments):void 0}}return a.prototype.states={loading:0,interactive:50,complete:100},a}(),f=function(){function a(){var a,b,c,d,e,f=this;this.progress=0,a=0,e=[],d=0,c=C(),b=setInterval(function(){var g;return g=C()-c-50,c=C(),e.push(g),e.length>D.eventLag.sampleCount&&e.shift(),a=q(e),++d>=D.eventLag.minSamples&&a<D.eventLag.lagThreshold?(f.progress=100,clearInterval(b)):f.progress=100*(3/(a+3))},50)}return a}(),m=function(){function a(a){this.source=a,this.last=this.sinceLastUpdate=0,this.rate=D.initialRate,this.catchup=0,this.progress=this.lastProgress=0,null!=this.source&&(this.progress=F(this.source,"progress"))}return a.prototype.tick=function(a,b){var c;return null==b&&(b=F(this.source,"progress")),b>=100&&(this.done=!0),b===this.last?this.sinceLastUpdate+=a:(this.sinceLastUpdate&&(this.rate=(b-this.last)/this.sinceLastUpdate),this.catchup=(b-this.progress)/D.catchupTime,this.sinceLastUpdate=0,this.last=b),b>this.progress&&(this.progress+=this.catchup*a),c=1-Math.pow(this.progress/100,D.easeFactor),this.progress+=c*this.rate*a,this.progress=Math.min(this.lastProgress+D.maxProgressPerFrame,this.progress),this.progress=Math.max(0,this.progress),this.progress=Math.min(100,this.progress),this.lastProgress=this.progress,this.progress},a}(),L=null,H=null,r=null,M=null,p=null,s=null,j.running=!1,z=function(){return D.restartOnPushState?j.restart():void 0},null!=window.history.pushState&&(T=window.history.pushState,window.history.pushState=function(){return z(),T.apply(window.history,arguments)}),null!=window.history.replaceState&&(W=window.history.replaceState,window.history.replaceState=function(){return z(),W.apply(window.history,arguments)}),l={ajax:a,elements:d,document:c,eventLag:f},(B=function(){var a,c,d,e,f,g,h,i;for(j.sources=L=[],g=["ajax","elements","document","eventLag"],c=0,e=g.length;e>c;c++)a=g[c],D[a]!==!1&&L.push(new l[a](D[a]));for(i=null!=(h=D.extraSources)?h:[],d=0,f=i.length;f>d;d++)K=i[d],L.push(new K(D));return j.bar=r=new b,H=[],M=new m})(),j.stop=function(){return j.trigger("stop"),j.running=!1,r.destroy(),s=!0,null!=p&&("function"==typeof t&&t(p),p=null),B()},j.restart=function(){return j.trigger("restart"),j.stop(),j.start()},j.go=function(){var a;return j.running=!0,r.render(),a=C(),s=!1,p=G(function(b,c){var d,e,f,g,h,i,k,l,n,o,p,q,t,u,v,w;for(l=100-r.progress,e=p=0,f=!0,i=q=0,u=L.length;u>q;i=++q)for(K=L[i],o=null!=H[i]?H[i]:H[i]=[],h=null!=(w=K.elements)?w:[K],k=t=0,v=h.length;v>t;k=++t)g=h[k],n=null!=o[k]?o[k]:o[k]=new m(g),f&=n.done,n.done||(e++,p+=n.tick(b));return d=p/e,r.update(M.tick(b,d)),r.done()||f||s?(r.update(100),j.trigger("done"),setTimeout(function(){return r.finish(),j.running=!1,j.trigger("hide")},Math.max(D.ghostTime,Math.max(D.minTime-(C()-a),0)))):c()})},j.start=function(a){v(D,a),j.running=!0;try{r.render()}catch(b){i=b}return document.querySelector(".pace")?(j.trigger("start"),j.go()):setTimeout(j.start,50)},"function"==typeof define&&define.amd?define(function(){return j}):"object"==typeof exports?module.exports=j:D.startOnPageLoad&&j.start()}).call(this);
// Slimscroll 
!function(e){jQuery.fn.extend({slimScroll:function(i){var o={width:"auto",height:"250px",size:"7px",color:"#000",position:"right",distance:"1px",start:"top",opacity:.4,alwaysVisible:!1,disableFadeOut:!1,railVisible:!1,railColor:"#333",railOpacity:.2,railDraggable:!0,railClass:"slimScrollRail",barClass:"slimScrollBar",wrapperClass:"slimScrollDiv",allowPageScroll:!1,wheelStep:20,touchScrollStep:200,borderRadius:"7px",railBorderRadius:"7px"},r=e.extend(o,i);return this.each(function(){function o(t){if(h){var t=t||window.event,i=0;t.wheelDelta&&(i=-t.wheelDelta/120),t.detail&&(i=t.detail/3);var o=t.target||t.srcTarget||t.srcElement;e(o).closest("."+r.wrapperClass).is(x.parent())&&s(i,!0),t.preventDefault&&!y&&t.preventDefault(),y||(t.returnValue=!1)}}function s(e,t,i){y=!1;var o=e,s=x.outerHeight()-M.outerHeight();if(t&&(o=parseInt(M.css("top"))+e*parseInt(r.wheelStep)/100*M.outerHeight(),o=Math.min(Math.max(o,0),s),o=e>0?Math.ceil(o):Math.floor(o),M.css({top:o+"px"})),v=parseInt(M.css("top"))/(x.outerHeight()-M.outerHeight()),o=v*(x[0].scrollHeight-x.outerHeight()),i){o=e;var a=o/x[0].scrollHeight*x.outerHeight();a=Math.min(Math.max(a,0),s),M.css({top:a+"px"})}x.scrollTop(o),x.trigger("slimscrolling",~~o),n(),c()}function a(){window.addEventListener?(this.addEventListener("DOMMouseScroll",o,!1),this.addEventListener("mousewheel",o,!1),this.addEventListener("MozMousePixelScroll",o,!1)):document.attachEvent("onmousewheel",o)}function l(){f=Math.max(x.outerHeight()/x[0].scrollHeight*x.outerHeight(),m),M.css({height:f+"px"});var e=f==x.outerHeight()?"none":"block";M.css({display:e})}function n(){if(l(),clearTimeout(p),v==~~v){if(y=r.allowPageScroll,b!=v){var e=0==~~v?"top":"bottom";x.trigger("slimscroll",e)}}else y=!1;return b=v,f>=x.outerHeight()?void(y=!0):(M.stop(!0,!0).fadeIn("fast"),void(r.railVisible&&E.stop(!0,!0).fadeIn("fast")))}function c(){r.alwaysVisible||(p=setTimeout(function(){r.disableFadeOut&&h||u||d||(M.fadeOut("slow"),E.fadeOut("slow"))},1e3))}var h,u,d,p,g,f,v,b,w="<div></div>",m=30,y=!1,x=e(this);if(x.parent().hasClass(r.wrapperClass)){var C=x.scrollTop();if(M=x.parent().find("."+r.barClass),E=x.parent().find("."+r.railClass),l(),e.isPlainObject(i)){if("height"in i&&"auto"==i.height){x.parent().css("height","auto"),x.css("height","auto");var H=x.parent().parent().height();x.parent().css("height",H),x.css("height",H)}if("scrollTo"in i)C=parseInt(r.scrollTo);else if("scrollBy"in i)C+=parseInt(r.scrollBy);else if("destroy"in i)return M.remove(),E.remove(),void x.unwrap();s(C,!1,!0)}}else{r.height="auto"==r.height?x.parent().height():r.height;var S=e(w).addClass(r.wrapperClass).css({position:"relative",overflow:"hidden",width:r.width,height:r.height});x.css({overflow:"hidden",width:r.width,height:r.height});var E=e(w).addClass(r.railClass).css({width:r.size,height:"100%",position:"absolute",top:0,display:r.alwaysVisible&&r.railVisible?"block":"none","border-radius":r.railBorderRadius,background:r.railColor,opacity:r.railOpacity,zIndex:90}),M=e(w).addClass(r.barClass).css({background:r.color,width:r.size,position:"absolute",top:0,opacity:r.opacity,display:r.alwaysVisible?"block":"none","border-radius":r.borderRadius,BorderRadius:r.borderRadius,MozBorderRadius:r.borderRadius,WebkitBorderRadius:r.borderRadius,zIndex:99}),R="right"==r.position?{right:r.distance}:{left:r.distance};E.css(R),M.css(R),x.wrap(S),x.parent().append(M),x.parent().append(E),r.railDraggable&&M.bind("mousedown",function(i){var o=e(document);return d=!0,t=parseFloat(M.css("top")),pageY=i.pageY,o.bind("mousemove.slimscroll",function(e){currTop=t+e.pageY-pageY,M.css("top",currTop),s(0,M.position().top,!1)}),o.bind("mouseup.slimscroll",function(e){d=!1,c(),o.unbind(".slimscroll")}),!1}).bind("selectstart.slimscroll",function(e){return e.stopPropagation(),e.preventDefault(),!1}),E.hover(function(){n()},function(){c()}),M.hover(function(){u=!0},function(){u=!1}),x.hover(function(){h=!0,n(),c()},function(){h=!1,c()}),x.bind("touchstart",function(e,t){e.originalEvent.touches.length&&(g=e.originalEvent.touches[0].pageY)}),x.bind("touchmove",function(e){if(y||e.originalEvent.preventDefault(),e.originalEvent.touches.length){var t=(g-e.originalEvent.touches[0].pageY)/r.touchScrollStep;s(t,!0),g=e.originalEvent.touches[0].pageY}}),l(),"bottom"===r.start?(M.css({top:x.outerHeight()-M.outerHeight()}),s(0,!0)):"top"!==r.start&&(s(e(r.start).position().top,null,!0),r.alwaysVisible||M.hide()),a()}}),this}}),jQuery.fn.extend({slimscroll:jQuery.fn.slimScroll})}(jQuery);
// Custom script code
$(document).ready(function(){
	// Ajax navigation
	( function( $, History, undefined ) {
		if ( !History.enabled ) {
			return false;
		}
		var $wrap = $( "#wrap" );
		$('body').on( "click", "a.smooth", function( event ) {
			event.preventDefault();
			if ( window.location === this.href ) {
				return;
			}
			var pageTitle1 = ( this.title ) ? this.title : this.textContent;
				pageTitle = ( $(this).data('title') ) ? $(this).data('title') : pageTitle1;
				pageTitle = ( this.getAttribute( "rel" ) === "home" ) ? sitename : pageTitle + " | "+sitename;
			History.pushState( null, pageTitle, this.href );
		} );
		History.Adapter.bind( window, "statechange", function() {
			var state = History.getState();
			Pace.track(function(){
			$.get( state.url, function( res ) {
				$.each( $( res ), function( index, elem ) {
					if ( $wrap.selector !== "#" + elem.id ) {
						return;
					}
					$wrap.html( $( elem ).html() ).promise().done( function( res ) {
						$('body,html').animate({scrollTop: 0}, 800);
						if ( typeof ga === "function" && res.length !== 0 ) { 
							// Make sure the new content is added, and the Google Analytics is available.
							ga('set', { page: window.location.pathname, title: state.title});
							ga('send', 'pageview');
						}
					});
				} );
			}).fail(
			function (xhr, ajaxOptions, thrownError){
				// Show 404 Page
				if(xhr.status==404) {
					var res = xhr.responseText;
					$.each( $( res ), function( index, elem ) {
						if ( $wrap.selector !== "#" + elem.id ) {
							return;
						}
						$wrap.html( $( elem ).html() ).promise().done( function( res ) {
							if ( typeof ga === "function" && res.length !== 0 ) { 
								// Make sure the new content is added, and the Google Analytics is available.
								ga('set', { page: window.location.pathname, title: state.title});
								ga('send', 'pageview');
							}
						});
					});
				}
			}
			);
			});
		});
	})( jQuery, window.History );
	// Loader options
	paceOptions = {
	  ajax: true,
	  document: true,
	  eventLag: true,
	  elements: {
		selectors: ['#wrap']
	  }
	};
	// Disable cache
	$.ajaxSetup({ cache: false });
	// Cart products
	function cart(){
		$("#cart-content").html('<div class="loading"></div>');
		$.getJSON('api/cart', function (data){
			$("#cart-header").html(data.header);
			if (data.count > 0){
				if ($(".cart-counter").length) {
					$( ".cart-counter" ).html(data.count);
				} else {
					$( ".toggle-cart" ).append('<span class="cart-counter">'+data.count+'</span>');
				}
				var cart = '';
				$.each(data.products, function(index,elem){
					cart += '<div class="cart-product"><img src="assets/products/'+elem.images+'"><div class="details"><h6>'+elem.title+'<i data-id="'+elem.id+'" class="remove-cart icon-trash"></i></h6><p>'+elem.price+' x '+elem.quantity+'<b>'+elem.total+'</b><br>'+elem.options+'</p></div><div class="clearfix"></div></div>';
				});
				cart += data.coupon;
				cart += '<div class="btn-clear"></div><button class="cart-btn cart-checkout bg">'+checkout+'</button>';
				$("#cart-content").html(cart);
			} else {
				$("#cart-content").html('<div class="empty-cart"><i class="icon-basket"></i><h5>'+empty_cart+'</h5></div>');
				$( ".cart-counter" ).remove();
			}
		});
	};
	cart();
	// Cart show/hide
	$("body").on('click','.toggle-cart',function() {
		$("#cart").toggle("300");
		$("#cart").toggleClass("cart-open");
	});
	// Load cart
	$("body").on('click','.load-cart',function() {
		cart();
	});
	// Cart scrolling
	$('#cart-content').slimScroll({
        height: 'auto',
		scrollTo : 0,
    });
	// Apply coupon code
	$("body").on('click','#apply',function() {
        var code = $("#code").val();
		$("#apply").html('<div class="loading"></div>');
		$.ajax({ 
				url: 'api/coupon?code='+code,
				type: 'get',
				crossDomain: true,
			}).done(function(response) {
				if (response == 'success'){
					cart();
				} else {
					$("#apply").html('Invalid !');
				}
			}).fail(function() {
				$("#apply").html('Failed !');
			});
    });
	// Cart checkout
	$("body").on('click','.cart-checkout',function() {
		$("#cart-content").html('<div class="loading"></div>');
		$.ajax({ 
				url: 'api/checkout',
				type: 'get',
				crossDomain: true,
			}).done(function(response){
				fields = JSON.parse(response);
				$("#cart-header").html(fields.header);
				delete fields.header;
				html = '';
				$.each(fields, function(index,field){
					html += field;
				});
				
				html += '<div class="btn-clear"></div><button class="cart-btn cart-payment bg">'+continue_to_payment+'</button></div>';
				$("#cart-content").html(html);
			}).fail(function() {
				console.log('Failed');
			});
	});
	// Cart payment
	$("body").on('click','.cart-payment',function() {
		$(".cart-payment").html('<div class="loading"></div>');
		$.ajax({ 
				url: 'api/payment',
				type: 'post',
				data: $("#customer").serialize(),
				crossDomain: true,
			}).done(function(response){
				fields = JSON.parse(response);
				if (fields.error == 'true'){
					$('#cart-content').slimScroll({
						height: 'auto',
						scrollTo : 0,
					});
					$("#errors").html('<div class="alert alert-warning">'+fields.message+'</div>');
					$(".cart-payment").html(continue_to_payment);
				}else{
				$("#cart-header").html(fields.header);
				delete fields.header;
				html = '';
				$.each(fields, function(index,field){
					html += field;
				});
				$("#cart-content").html(html);
				}
			}).fail(function() {
				console.log('Failed');
			});
	});
	// Add to cart
	$("body").on('click','.add-cart',function() {
		var id = $(this).data('id');
		var quantity = $(".quantity").val();
		var options = $(".options").serialize();
		$(".add-cart").html('<div class="loading"></div>');
		$.ajax({ 
				url: 'api/add?id='+id+'&q='+quantity+'&'+options,
				type: 'get',
			   crossDomain: true,
			}).done(function(response) {
				if (response == 'unavailable'){
					$(".add-cart").html('Stock unavailable');
				} else if (response == 'updated') {
					$(".add-cart").html('Updated');
					cart();
				} else {
					$(".add-cart").html('Success');
					cart();
				}
			}).fail(function() {
				console.log('Failed');
			});
	});
	// Remove from cart
	$("body").on('click','.remove-cart',function() {
		var id = $(this).data('id');
		$.ajax({ 
				url: 'api/remove?id='+id+'',
				type: 'get',
			   crossDomain: true,
			}).done(function(responseData) {
				cart();
			}).fail(function() {
				console.log('Failed');
			});
	});
	// Product review
	$("body").on('click','#submit-review',function(e) {
		e.preventDefault();
		var btn = $("#submit-review").html();
		$("#submit-review").html('<div class="loading"></div>');
		var product = $(this).data('product');
		$.ajax({ 
				url: 'api/review?product='+product,
				type: 'post',
				data: $("#review").serialize(),
				crossDomain: true,
			}).done(function(response){
				if (response == 'success'){
					$("#response").html('<div class="alert alert-success">'+response+'</div>');
					$("#submit-review").html(btn);
				} else {
					$("#response").html('<div class="alert alert-warning">'+response+'</div>');
					$("#submit-review").html(btn);
				}
			}).fail(function() {
				console.log('Failed');
			});
	});
	// Products listing
	function listing(){
		$("#listing").html('<div class="loading"></div>');
		$.ajax({ 
				url: 'api/products',
				type: 'get',
				data: $("#search").serialize(),
				crossDomain: true,
			}).done(function(response) {
				data = JSON.parse(response);
				var listing = '';
				$.each(data.products, function(index,elem){
					listing += '<div class="col-md-3"><div class="product" id="'+elem.id+'"><div class="pi"><img src="'+elem.images+'"/></div><h5>'+elem.title+'</h5><b>'+elem.price+'</b></div><div class="bg view"><h5>'+elem.title+'</h5><p>'+elem.text+'</p><a href="'+elem.path+'" data-title="'+elem.title+'" class="smooth"><i class="icon-eye"></i>Details</a></div></div>';
				});
				$("#listing").html(listing);
			}).fail(function() {
				console.log('Failed');
			});
	}
	// Search products
	$("body").on('submit','#search',function(e) {
		e.preventDefault();
		listing();
	});
	// Search modal
	$("#search-input").on('keyup', function(){
		var query = $("#search-input").val();
		$("#search-results").html('<div class="search-item"><div class="loading"></div></div>');
		$.ajax({ 
				url: 'api/products',
				type: 'get',
				data: 'search='+query,
				crossDomain: true,
			}).done(function(response) {
				data = JSON.parse(response);
				var listing = '';
				var count = 0;
				$.each(data.products, function(index,elem){
					listing += '<div class="search-item" id="'+elem.id+'"><div class="search-image"><img src="'+elem.images+'"/></div><a href="'+elem.path+'" data-title="'+elem.title+'" class="smooth"><h6>'+elem.title+'</h6></a><b>'+elem.price+'</b></div>';
					count++;
				});
				if (count == 0){
					$("#search-results").html('<div class="search-item search-not-found"><h6>Nothing found</h6></div>');
				} else {
					$("#search-results").html(listing);
				}
			}).fail(function() {
				console.log('Failed');
			});
	});
	// Search modal toggle button
	$("body").on('click','.search-toggle',function() {
        $('.search-modal').toggle();
    });
	// Search modal toggle button
	$("body").on('click','#search-results a',function() {
        $('.search-modal').hide();
    });
	// Newsletter subscribe
	$("body").on('click','#subscribe',function() {
        var email = $("#email").val();
		$("#subscribe").html('<div class="loading"></div>');
		if (!validateEmail(email)){
			$("#subscribe").html('Invalid !');
		}else{
		$.ajax({ 
				url: 'api/subscribe?email='+email+'',
				type: 'get',
				crossDomain: true,
			}).done(function(responseData) {
				$("#subscribe").html('Success !');
			}).fail(function() {
				$("#subscribe").html('Failed !');
			});
		}
    });
		
	$("#country").on('change', function(){
		var phone = $(this).data("phone");
		alert(phone);
		$('input[name="mobile"]').val(phone);
	});
	// Validating emails
	function validateEmail(email) {
	  var re = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
	  return re.test(email);
	}
});