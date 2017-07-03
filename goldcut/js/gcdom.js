/**

 http://dmitrysoshnikov.com/ecmascript/es5-chapter-1-properties-and-property-descriptors/

 when.defer() to create a deferred object that has a promise for a value that will become available at some point in the future.
 https://github.com/cujojs/when/wiki/Examples

 _underscore

 Queue, Stack etc
 http://dev.opera.com/articles/view/javascript-array-extras-in-detail/


 base: ajax, cookie, templates, id/css, get/set style
 form validate
 mobile is, ops
 referer, from g/ya callbacks
 With promises lib
 WS, polling
 match patch
 selection
 audio, canvas, graph, uploads
 innerHTML http://habrahabr.ru/post/31413/
 ms dyn create table http://msdn.microsoft.com/en-us/library/ms532998.aspx


 var template = '<div class="BLK list BRIN" style="width:100%" id=""><div style="width:50px" class="FL minute">time. timebonus</div><div style="width:50px" class="FL icon image"><img data-key="image-src" src=""></div><div class="FL description">Text</div></div>';
 var imgSRC = '/img/action/'+action+'.png';
 var data = [{"minute": minbon, "description": text, "image": {'src': imgSRC } }];

 var frag = createFrag(template);
 renderFrag(frag, data);

 var domid = id('place');
 //domid.innerHTML = '';
 if (domid.firstChild)
 domid.insertBefore(frag, domid.firstChild);
 else
 domid.appendChild(frag);
 //domid.replaceChild(frag);
 */

GC = {};
GC.state = {};
GC.state.online = undefined;
GC.cache = {};
GC.database = null;
GC.SCREEN = {};

// IE7 support for querySelectorAll (no support for ".class1, .class2")
if (!document.querySelectorAll) {
    (function (d) {
        d = document, a = d.styleSheets[0] || d.createStyleSheet();
        d.querySelectorAll = function (e) {
            a.addRule(e, 'f:b');
            for (var l = d.all, b = 0, c = [], f = l.length; b < f; b++)l[b].currentStyle.f && c.push(l[b]);
            a.removeRule(0);
            return c
        }
    })()
    document.querySelector = function (q) {
        return document.querySelectorAll(q)[0];
    }
}
// IE7 support for querySelectorAll. (has support for ".class1, .class2")
/**
 (function(d, s) {
 if (!document.querySelectorAll) {
 d=document, s=d.createStyleSheet();
 d.querySelectorAll = function(r, c, i, j, a) {
 a=d.all, c=[], r = r.replace(/\[for\b/gi, '[htmlFor').split(',');
 for (i=r.length; i--;) {
 s.addRule(r[i], 'k:v');
 for (j=a.length; j--;) a[j].currentStyle.k && c.push(a[j]);
 s.removeRule(0);
 }
 return c;
 }
 }
 })()
 */

var makeCRCTable = function(){
    var c;
    var crcTable = [];
    for(var n =0; n < 256; n++){
        c = n;
        for(var k =0; k < 8; k++){
            c = ((c&1) ? (0xEDB88320 ^ (c >>> 1)) : (c >>> 1));
        }
        crcTable[n] = c;
    }
    return crcTable;
}
// +utf8 strings crc http://stackoverflow.com/questions/8353134/javascript-crc32-function-and-php-crc32-not-matching-for-utf8?rq=1
var crc32 = function(str) {
    var crcTable = window.crcTable || (window.crcTable = makeCRCTable());
    var crc = 0 ^ (-1);

    for (var i = 0; i < str.length; i++ ) {
        crc = (crc >>> 8) ^ crcTable[(crc ^ str.charCodeAt(i)) & 0xFF];
    }

    return (crc ^ (-1)) >>> 0;
};

// All js fn compat per browser
// http://kangax.github.com/es5-compat-table/

// https://developer.mozilla.org/en-US/docs/JavaScript/Reference/Global_Objects/Array/forEach
// IE 9+ (ie 8 need this)
if (!Array.prototype.forEach) {
    Array.prototype.forEach = function (fn, scope) {
        for (var i = 0, len = this.length; i < len; ++i) {
            fn.call(scope, this[i], i, this);
        }
    }
}
function ArrayExtended() {}
ArrayExtended.prototype = Object.create(Array.prototype);
ArrayExtended.prototype.max = function () {
    var max = this[0];
    var len = this.length;
    for (var i = 1; i < len; i++) if (this[i] > max) max = this[i];
    return max;
}
ArrayExtended.prototype.min = function () {
    var min = this[0];
    var len = this.length;
    for (var i = 1; i < len; i++) if (this[i] < min) min = this[i];
    return min;
}

// array [x.y] transpose
function transposeArray(array)
{
    var transposedArray = array[0].map(function(col, i) {
        return array.map(function(row) {
            return row[i]
        })
    });
    return transposedArray;
}

var createRange = function (n) {
    return Array.apply(null, new Array(n)).map(function (empty, index) {
        return index+1;
    });
};

Number.prototype.formatMoney = function(c, d, t){
    var n = this,
        c = isNaN(c = Math.abs(c)) ? 2 : c,
        d = d == undefined ? "." : d,
        t = t == undefined ? "," : t,
        s = n < 0 ? "-" : "",
        i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "",
        j = (j = i.length) > 3 ? j % 3 : 0;
    //parseFloat(n) == parseFloat(i)
    var cn = (c ? d + Math.abs(n - i).toFixed(c).slice(2)  : "");
    if (cn = '.00') cn = '';
    return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + cn;
};

/**
 for (var key in some_array) {
 var val = some_array [key];
 alert (key+' = '+val);
 }
 The purpose of the Array.prototype.map method is to create a new array with the results of calling the callback function on every array element.
 The purpose of the Array.prototype.forEach method is to iterate over an array, executing the provided callback function once per array element.
 The purpose of the for...in statement is to enumerate object properties.
 I think that the for...in statement should be avoided to traverse any array-like1 object, where the real purpose is iterate over numeric indexes and not enumerate the object properties (even knowing that those indexes are properties).
 Reasons to avoid for...in to iterate array-like objects:
 Iterates over inherited user-defined properties in addition to the array elements, if you use a library like MooTools for example, which extend the Array.prototype object, you will see all those extended properties.
 The order of iteration is arbitrary, the elements may not be visited in numeric order.
 */
function getRandomArbitary(min, max) {
    return Math.floor(Math.random() * (max - min + 1)) + min;
}

function URN(urn) 
{
	this.urn = urn;
	this.entity = this.entity();
	this.uuid = this.uuid();
}
URN.prototype.entity = function() 
{ 
	return this.urn.split('-')[1];
}
URN.prototype.uuid = function() 
{ 
	return this.urn.split('-')[2];
}
URN.prototype.generate = function(entity)
{
    return ['urn',entity,getRandomArbitary(100000, 999000)].join('-');
}


/*
function A()
{
    this.x = 1;
}
A.prototype.DoIt = function()
{
    this.x += 1;
}
function B()
{
    A.call(this);
    this.y = 1;
}
//B.prototype = new A;
//B.prototype.constructor = B;
inherit(B, A);
B.prototype.DoIt = function()
{
    A.prototype.DoIt.call(this);
    this.y += 1;
}
b = new B;
document.write((b instanceof A) + ', ' + (b instanceof B) + '<BR/>');
b.DoIt();
b.DoIt();
document.write(b.x + ', ' + b.y);
*/
function extend() {
    function ext(destination, source) {
        var prop;
        for (prop in source) {
            if (source.hasOwnProperty(prop)) {
                destination[prop] = source[prop];
            }
        }
    }
    ext(arguments["0"], arguments["1"]);
} 
function noop() {}
function inherit(ctor, superCtor) {
	if (Object.create) {
		ctor.prototype = Object.create(superCtor.prototype, {
			constructor: { value: ctor, enumerable: false }
		});
	} else {
		noop.prototype = superCtor.prototype;
		ctor.prototype = new noop();
		ctor.prototype.constructor = superCtor;
	}
}




//var o1 = {a:1, b:2};
//var o2 = copy(o1);
function copy(o) {
    var copy = Object.create(Object.getPrototypeOf(o));
    var propNames = Object.getOwnPropertyNames(o);
    propNames.forEach(function (name) {
        var desc = Object.getOwnPropertyDescriptor(o, name);
        Object.defineProperty(copy, name, desc);
    });
    return copy;
}


function time() {
    var d = new Date();
    var n = d.getMilliseconds();
    return n;
}

function is_array(a) {
    return typeof(a) == 'object' && (a instanceof Array);
}
function isArray(obj) {
    return (typeof obj !== 'undefined' &&
            obj && obj.constructor === Array);
}
function in_array(obj, arr) {
    return (arr.indexOf(obj) != -1);
}

function id(sid) {
    return document.getElementById(sid);
}

// by default All or First?
function bycss(selector, nth, ctxEl) {
    // if ctxEl && nonlegacybrowser - use ctxEl.querySelectorAll
    if (!ctxEl) ctxEl = document;
    if (nth == 1)
        return ctxEl.querySelector(selector);
    else if (nth > 1)
        return ctxEl.querySelectorAll(selector)[--nth];
    else if (is_array(nth)) {
        // TODO
        // bycss(".sliderImgItem",[2,5],ctx); from nth 2 to 5
    }
    else
        return ctxEl.querySelectorAll(selector);
}

function addClass(el, cls) {
    // r.classList.add
    var c = el.className.split(' ');
    for (var i = 0; i < c.length; i++) {
        if (c[i] == cls) return;
    }
    c.push(cls);
    el.className = c.join(' ');
}

function removeClass(el, cls) {
    var c = el.className.split(' ');
    for (var i = 0; i < c.length; i++) {
        if (c[i] == cls) c.splice(i--, 1);
    }
    el.className = c.join(' ');
}

function hasClass(el, cls) {
    if (typeof(el) == 'undefined') throw 'hasClass() on undefined element';
    if (typeof(el.className) == 'undefined') return false;
    for (var c = el.className.split(' '), i = c.length - 1; i >= 0; i--) {
        if (c[i] == cls) return true;
    }
    return false;
}

function toggleClass(btn, cls) {
    if (!hasClass(btn, cls))
        addClass(btn, cls);
    else
        removeClass(btn, cls);
}


/**
 // OLD
 function setStyle(el, style, value, units) {
 if (typeof el == 'object') x = el;
 if (typeof el == 'string') x = document.getElementById(el);
 if (units) value += units;
 x.style[style] = value;
 }

 function getStyleProp(el, styleProp) {
 if (!el) throw 'getStyleProp on no element';
 var x;
 if (typeof el == 'object') x = el;
 if (typeof el == 'string') x = document.getElementById(el);
 //if (window.getComputedStyle)
 var y = document.defaultView.getComputedStyle(x, null);
 console.log(y);
 var yy = y.getPropertyValue(styleProp);
 console.log(yy);
 //else console.log('no window.getComputedStyle');
 return yy;
 }
 */

getElementWidth = function(el) {
    if (typeof el.clip !== "undefined") {
        return el.clip.width;
        } else {
        if (el.style.pixelWidth) {
            return el.style.pixelWidth;
            } else {
            return el.offsetWidth;
            }
        }
    };

getElementHeight = function(el) {
    if (typeof el.clip !== "undefined") {
        return el.clip.height;
        } else {
        if (el.style.pixelHeight) {
            return el.style.pixelHeight;
            } else {
            return el.offsetHeight;
            }
        }
    };

function getStyle(el, style) {
    //if(!document.getElementById) return;
    if (!el)
    {
        console.log(printStackTrace());
        throw 'No el for setstyle';
    }
    var value = el.style[toCamelCase(style)];
    if (!value)
        if (document.defaultView)
            value = document.defaultView.
                getComputedStyle(el, "").getPropertyValue(style);
        else if (el.currentStyle)
            value = el.currentStyle[toCamelCase(style)];
    return value;
}

function setStyle(el, style, value, units) {
    if (!el)
    {
        console.log(printStackTrace());
        throw 'No el for setstyle';
    }
    if (typeof el == 'object') x = el;
    if (typeof el == 'string') x = document.getElementById(el);
    if (units) value += units;
    x.style[toCamelCase(style)] = value;
}

function toCamelCase(sInput) {
    var oStringList = sInput.split('-');
    if (oStringList.length == 1)
        return oStringList[0];
    var ret = sInput.indexOf("-") == 0 ?
        oStringList[0].charAt(0).toUpperCase() + oStringList[0].substring(1) : oStringList[0];
    for (var i = 1, len = oStringList.length; i < len; i++) {
        var s = oStringList[i];
        ret += s.charAt(0).toUpperCase() + s.substring(1)
    }
    return ret;
}

function unsetClassBelowContextPath(context, className)
{
    var childs = context.querySelectorAll('.'+className);
    for (var i = 0; i < childs.length; i++)
    {
        var c = childs[i];
        removeClass(c, className);
    }
}

function moveLeft(el, delta) {
    var cl = parseInt(getStyle(el, 'left'))
    setStyle(el, 'left', cl - delta, 'px');
}
function moveRight(el, delta) {
    var cl = parseInt(getStyle(el, 'left'))
    setStyle(el, 'left', cl + delta, 'px');
}

function getStyleProp(el, styleProp) {
    return getStyle(el, styleProp);
}

function getDocHeight() {
    var D = document;
    return Math.max(
        Math.max(D.body.scrollHeight, D.documentElement.scrollHeight),
        Math.max(D.body.offsetHeight, D.documentElement.offsetHeight),
        Math.max(D.body.clientHeight, D.documentElement.clientHeight)
    );
}

function getViewportSize()
{
	var w = window, d = document, e = d.documentElement, g = d.getElementsByTagName('body')[0], x = w.innerWidth || e.clientWidth || g.clientWidth, y = w.innerHeight || e.clientHeight || g.clientHeight;
	return {x: x, y: y};
}
function getActualViewportSize()
{
	var w = window, d = document, e = d.documentElement, g = d.getElementsByTagName('body')[0], x = w.innerWidth || e.clientWidth || g.clientWidth, y = w.innerHeight || e.clientHeight || g.clientHeight;
	return {x: g.clientWidth, y: g.clientHeight};
}

function eventCoord(e) {
	// Crossplatform offsetX/Y. IE, Fx, Opera, Safari tested
	if (!e) e = event;
	if (e.target) targ = e.target; 
	else if (e.srcElement) targ = e.srcElement;
	if (targ.nodeType == 3) /* defeat Safari bug */ targ = targ.parentNode;
	if (e.pageX == null) { /* IE case */
		var d = (document.documentElement && document.documentElement.scrollLeft != null) ?
			document.documentElement : document.body;
		ex = e.clientX + d.scrollLeft;
		ey = e.clientY + d.scrollTop;
	} else {
		ex = e.pageX;
		ey = e.pageY;
	}
	if (targ.offsetParent) {
		do {
			ex -= targ.offsetLeft;
			ey -= targ.offsetTop;
		} while (targ = targ.offsetParent);
	}
	return {x: ex, y: ey};
}

function elWidth(el, newval) {
    if (!newval)
        return parseInt(getStyleProp(el, 'width'));
    else
        setStyle(el, 'width', newval, 'px');
}
function elHeight(el, newval) {
    if (!newval)
        return parseInt(getStyleProp(el, 'height'));
    else
        setStyle(el, 'height', newval, 'px');
}




function nodeSetText(wn, value) {
    if (typeof(wn.innerText) != 'undefined')
        wn.innerText = value;
    else
        wn.textContent = value;
}
function setText(o, t) {
    if (!o) return 'no DomEl for set text:' + t;
    if (typeof(o.innerText) != 'undefined') {
        o.innerText = t;
    } else {
        o.textContent = t;
    }
}
function getText(o, t) {
    if (typeof(o.innerText) != 'undefined') {
        return o.innerText;
    } else {
        return o.textContent;
    }
}

function getCheckedTitle(radioObj) {
    if (!radioObj) return null;
    var radioLength = radioObj.length;
    if (radioLength == undefined)
        if (radioObj.checked)
            return radioObj.value;
        else
            return null;
    for (var i = 0; i < radioLength; i++) {
        if (radioObj[i].checked || radioObj[i].selected) {
            if (document.all)
                return radioObj[i].innerText;
            else
                return radioObj[i].textContent;
        }
    }
    return null;
}

// for radio buttons / checkboxes
function getCheckedValue(radioObj) {
    if (!radioObj)
        return null;
    var radioLength = radioObj.length;
    if (radioLength == undefined)
        if (radioObj.checked)
            return radioObj.value;
        else
            return null;
    for (var i = 0; i < radioLength; i++) {
        if (radioObj[i].checked || radioObj[i].selected) {
            return radioObj[i].value;
        }
    }
    return null;
}
function setCheckedValue(radioObj, newValue) {
    if (!radioObj) return;
    var radioLength = radioObj.length;
    if (radioLength == undefined) {
        radioObj.checked = (radioObj.value == newValue.toString());
        return;
    }
    for (var i = 0; i < radioLength; i++) {
        radioObj[i].checked = false;
        if (radioObj[i].value == newValue.toString()) {
            if (radioObj[i].selected != undefined) radioObj[i].selected = true;
            else if (radioObj[i].checked != undefined) radioObj[i].checked = true;
        }
    }
}


var util = {

    // Finds the absolute position of an element on a page
    findPos:function (obj) {
        var curleft = curtop = 0;
        if (obj.offsetParent) {
            do {
                curleft += obj.offsetLeft;
                curtop += obj.offsetTop;
            } while (obj = obj.offsetParent);
        }
        return [curleft, curtop];
    },

    // Finds the scroll position of a page
    getPageScroll:function () {
        var xScroll, yScroll;
        if (self.pageYOffset) {
            yScroll = self.pageYOffset;
            xScroll = self.pageXOffset;
        } else if (document.documentElement && document.documentElement.scrollTop) {
            yScroll = document.documentElement.scrollTop;
            xScroll = document.documentElement.scrollLeft;
        } else if (document.body) {// all other Explorers
            yScroll = document.body.scrollTop;
            xScroll = document.body.scrollLeft;
        }
        return [xScroll, yScroll]
    },

    // Finds the position of an element relative to the viewport.
    findPosRelativeToViewport:function (obj) {
        var objPos = this.findPos(obj)
        var scroll = this.getPageScroll()
        return [ objPos[0] - scroll[0], objPos[1] - scroll[1] ]
    }

}


function showOverlay() {
    el = document.getElementById("overlay");
    //el.style.visibility = (el.style.visibility == "visible") ? "hidden" : "visible";
    el.style.visibility = "visible";
}
function hideOverlay() {
    el = document.getElementById("overlay");
    el.style.visibility = "hidden";
    //el.style.visibility = (el.style.visibility == "visible") ? "hidden" : "visible";
}

/**
 end of lib
 */


function show(el) {
    // TODO add restore old state
    var currDisplay = getStyleProp(el, 'display');
    if (currDisplay == 'none') setStyle(el, 'display', 'block');
}
function show2(el) {
    var currDisplay = getStyleProp(el, 'visibility');
    if (currDisplay == 'hidden') setStyle(el, 'visibility', 'visible');
}

function showWithFx(el)
{
	show2(el);
    margeNormal(el)
	window.setTimeout(function(){ setStyle(el, 'opacity', '1.0') }, 200)
    // local for a site
    if (id('emailfield')) setStyle(id('emailfield'), 'height', '120px')
}

function margeNormal(el){
    setStyle(el, 'marginRight', '0')
}
function margeRight(el){
    setStyle(el, 'marginRight', '100%')
}
function margeLeft(el){
    setStyle(el, 'marginLeft', '100%')
}

function hideWithFx(el)
{
	setStyle(el, 'opacity', '0.0')
    if (id('emailfield'))
    {
        window.setTimeout(function(){ setStyle(id('emailfield'), 'height', '0px') }, 200)
	    window.setTimeout(function(){ hide2(el) }, 255)
    }
	
}

function yellowHighlight(el)
{
    addClass(el,'animatedFast');
    setStyle(el, 'backgroundColor', 'yellow');
    window.setTimeout(function(){ setStyle(el, 'backgroundColor', 'transparent') }, 500);
}

function hide(el) {
    // TODO add restore old state
    var currDisplay = getStyleProp(el, 'display');
    if (currDisplay != 'none') setStyle(el, 'display', 'none');
}
function hide2(el) {
    var currDisplay = getStyleProp(el, 'visibility');
    if (currDisplay != 'hidden') setStyle(el, 'visibility', 'hidden');
}

function centerit(el) {
    setStyle(el, 'position', 'absolute');
    setStyle(el, 'left', (window.innerWidth - elWidth(el)) / 2, 'px');
    setStyle(el, 'top', (window.innerHeight - elHeight(el)) / 2, 'px');
}

function heightAlmostFull(el) {
    setStyle(el, 'height', window.innerHeight - 150, 'px');
}


function DomPath2(domel) {
    this.el = domel;
    this.init = function (el) {
        var max = 20, i = 0;
        var uppath = [];
        var p = el;
        uppath.push({'tag':p.tagName, 'id':p.id, 'classes':p.className.split(' '), 'dom':p});
        while (p.parentNode && p.parentNode.tagName) {
            i++;
            if (i == max) break;
            //console.log(p.tagName, p.id, p.className);
            p = p.parentNode;
            uppath.push({'tag':p.tagName, 'id':p.id, 'classes':p.className.split(' '), 'dom':p});
        }
        //console.log(uppath);
        this.dompath = uppath;
    };
    this.init(this.el);
    this.internalFunc = function () {
    };
    this.checkTagIdClass = function (elO, of) {
        if (typeof(of) == "undefined") {
            return false;
        }
        var hf1 = true, hf2 = true, hf3 = true;
        if (typeof(of.tag) != "undefined") {
            //console.log(elO, of);
            if (elO.tag != of.tag.toUpperCase())
                hf1 = false;
        }
        ;
        if (typeof(of.id) != "undefined") {
            if (elO.id != of.id)
                hf2 = false;
        }
        ;
        //IE 8 error on .class
        if (typeof(of["class"]) != "undefined") {
            //console.log(of.class, elO.classes.indexOf(of.class));
            if (elO.classes.indexOf(of["class"]) >= 0)
                hf3 = true;
            else
                hf3 = false;
        }
        //console.log(hf1, hf2, hf3);
        if (hf1 && hf2 && hf3)
            return true;
        else
            return false;
    }
}
DomPath2.prototype.testNodesOnPath = function (t, of) {
    var hasT = false, hasOf = false;
    for (var i = 0; i < this.dompath.length; i++) {
        //if (this.dompath[i].tag == t.tag.toUpperCase()) hasT = true;
        if (!hasT && this.checkTagIdClass(this.dompath[i], t)) hasT = true;
        if (hasT) hasOf = this.checkTagIdClass(this.dompath[i], of);
        //console.log(this.dompath[i], this.checkTagIdClass(this.dompath[i], t), hasT, hasOf, 1);
        if (hasT && hasOf) return true;
    }
    //console.log('testNodesOnPath', t, of, hasT, hasOf, 2);
    //if (hasT && hasOf) return true;
    //else
    return false;
};
// param: {tag, id, class}
DomPath2.prototype.getNodeBy = function (param) {
    for (var i = 0; i < this.dompath.length; i++) {
        //console.log('+', this.dompath[i], param);
        if (this.checkTagIdClass(this.dompath[i], param)) return this.dompath[i].dom; //console.log('OK'); //
        //console.log(this.checkTagIdClass(this.dompath[i], param));
    }
};

// TODO hasclass(ARRAY of classes (and, or))
function filterTags(nodeList, filter) {
    var filtered = [].filter.call(nodeList, function (node) {
        return node.nodeType == 1 && node.tagName.toLowerCase() == filter.tag && hasClass(node, filter["class"]);
        // return element.parentNode == filter.parent;
    });
    return filtered;
    /*
     [].filter.call(ul.querySelectorAll("li"), function(element){
     return element.parentNode == ul;
     });
     [].filter.call(ul.childNodes, function(node) {
     return node.nodeType == 1 && node.tagName.toLowerCase() == 'li';
     });*/
}

function isAndroid() {
    var ua = navigator.userAgent.toLowerCase();
    var isA = ua.indexOf("android") > -1; //&& ua.indexOf("mobile");
    if (isA) return true;
    else return false;
}

/**
 html5 cross domain postPessage()
 ie8 XDomainRequest
 */

/**
 processor(json, returnControlTo) - recieved data processor
 onStart - fn runned before request
 onDone - runnded on ajax recieved reply but before JSON parse
 returnControlTo - callback passed to processor which will be runned by processor on done
 http://habrahabr.ru/post/120917/ XHR2, FormData
 http://dev.opera.com/articles/view/xhr2/
 Content-type text/xml или application/octet-stream Ю $HTTP_RAW_POST_DATA

 OPTIONS before POST - if POST is used to send request data with a Content-Type other than application/x-www-form-urlencoded, multipart/form-data, or text/plain, e.g. if the POST request sends an XML payload to the server using application/xml or text/xml, then the request is preflighted. setRequestHeader('Content-Type', 'application/xml');  <?xml version="1.0"?><person><name>Arun</name></person>
 Access-Control-Max-Age gives the value in seconds for how long the response to the preflight request can be cached for without sending another preflight request
 Important note: when responding to a credentialed request,  server must specify a domain, and cannot use wild carding
 */
function ajax(url, dataProcessor, opts, method, params) {
    var deferred = when.defer();
    if (!opts) opts = {};
    if (opts['onStart']) opts['onStart']();
    var xhr;
    if (window.ActiveXObject)
        xhr = new ActiveXObject("Microsoft.XMLHTTP"); // IE 5!!, 6!
    else if (window.XMLHttpRequest)
        xhr = new XMLHttpRequest();
    else
        alert("AJAX not supported");
    if (!method) method = "GET";
    if (method == 'GET') url = makeHREF(url, params);
    var qs = null;
    if (method == 'POST') qs = makeQS(params);
    xhr.open(method, url, true);
    if (opts['withCredentials']) xhr.withCredentials = true; // send cookies
    if (opts['noCache'])
    {
        xhr.setRequestHeader("Cache-Control", "no-cache");
        xhr.setRequestHeader("Pragma", "no-cache");
    }
    if (method == 'POST') xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded; charset=UTF-8"); // ; charset=UTF-8
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4) {
            if (xhr.status == 200) {
                if (xhr.responseText != null) {
                    //console.log(xhr.responseText);	// console.log(xhr.responseXML);
                    //if (!xhr.responseText) throw 'no xhr.responseText';
                    //if (!xhr.responseXML) throw 'no xhr.responseXML';
                    if (opts['onDone']) opts['onDone']();
					if (opts['responseType'] == 'XML')
					{
						var jsObject = xhr.responseXML;
					}
					else if (opts['responseType'] == 'plain')
					{
						var jsObject = xhr.responseText;
					}
					else
					{
                    	try {
	                        var jsObject = JSON.parse(xhr.responseText);
	                    }
	                    catch (e) {
	                        console.log('Cant parse JSON (exception):', e);
	                        console.log("Text in response:", xhr.responseText);
                            //if (window['printStackTrace']) console.log(printStackTrace());
	                        if (opts['onError']) opts['onError'](0, 'JSON PARSE ERROR ' + xhr.responseText);
	                    }
					}
                    var returnControlTo = opts['returnControlTo'] ? opts['returnControlTo'] : null;
                    dataProcessor(jsObject, returnControlTo); // check dataProcessor is FN
                    deferred.resolve(jsObject);
                }
                else
                    return false;
            }
            else {
                if (opts['onError']) {
                    var errorMessage;
                    try {
                        var jsObject = JSON.parse(xhr.responseText);
                        errorMessage = jsObject.text;
                    }
                    catch (e) {
                        errorMessage = xhr.responseText;
                    }
                    opts['onError'](xhr.status, errorMessage);
                }
                else alert("No opts['onError']. Error code: " + xhr.status + ", error: " + xhr.statusText);
            }
        }
    }
    xhr.send(qs);
    return deferred.promise;
}

/**
 * CORS
 http://www.nczonline.net/blog/2010/05/25/cross-domain-ajax-with-cross-origin-resource-sharing/

 Access-Control-Allow-Origin: http://www.nczonline.net
 Access-Control-Allow-Methods: POST, GET
 Access-Control-Allow-Headers: NCZ
 Access-Control-Max-Age: 1728000

 Firefox 3.5+, Safari 4+, and Chrome all support preflighted requests; Internet Explorer 8 does not.
 To do the same in Internet Explorer 8, you’ll need to use the XDomainRequest object in the same manner:
 The XMLHttpRequest object in Firefox, Safari, and Chrome has similar enough interfaces to the IE XDomainRequest object that this pattern works fairly well. The common interface properties/methods are:

 req Origin: http://www.nczonline.net
 resp Access-Control-Allow-Origin: http://www.nczonline.net

*/
function createCORSRequest(method, url) {
    var xhr = new XMLHttpRequest();
    if ("withCredentials" in xhr) {
        xhr.open(method, url, true);
    } else if (typeof XDomainRequest != "undefined") {
        xhr = new XDomainRequest();
        xhr.open(method, url);
    } else {
        xhr = null;
    }
    return xhr;
}
/*
 var request = createCORSRequest("get", "http://www.nczonline.net/");
 if (request){
 request.onload = function(){
 //do something with request.responseText
 };
 request.send();
 }

 */


/**
 this.onload = function () {
 var many = 0;
 JSONP("test.php?callback", function (a, b, c) {
 this.document.body.innerHTML += [
 a, b, ++many, c
 ].join(" ") + "<br />";
 });
 JSONP("test.php?callback", function (a, b, c) {
 this.document.body.innerHTML += [
 a, b, ++many, c
 ].join(" ") + "<br />";
 });
 };
 */
var JSONP = function (global) {
    // (C) WebReflection Essential - Mit Style
    // 216 bytes minified + gzipped via Google Closure Compiler
    function JSONP(uri, callback) {
        function JSONPResponse() {
            try {
                delete global[src]
            } catch (e) {
                // kinda forgot < IE9 existed
                // thanks @jdalton for the catch
                global[src] = null
            }
            documentElement.removeChild(script);
            callback.apply(this, arguments);
        }

        var
            src = prefix + id++,
            script = document.createElement("script")
            ;
        global[src] = JSONPResponse;
        documentElement.insertBefore(
            script,
            documentElement.lastChild
        ).src = uri + "=" + src;
    }

    var
        id = 0,
        prefix = "__JSONP__",
        document = global.document,
        documentElement = document.documentElement
        ;
    return JSONP;
}(this);


// http://www.quirksmode.org/js/cookies.html
function createCookie(name, value, days) {
    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        var expires = "; expires=" + date.toGMTString();
    }
    else var expires = "";
    document.cookie = name + "=" + escape(value) + expires + "; path=/";
}

function readCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') c = c.substring(1, c.length);
        if (c.indexOf(nameEQ) == 0) return unescape(c.substring(nameEQ.length, c.length));
    }
    return null;
}

function eraseCookie(name) {
    createCookie(name, "", -1);
}

// http://blog.stevenlevithan.com/archives/faster-trim-javascript
function trim(str) {
    var str = str.replace(/^\s\s*/, ''), ws = /\s/, i = str.length;
    while (ws.test(str.charAt(--i)));
    return str.slice(0, i + 1);
}
function ltrim(s) {
    var ptrn = /\s*((\S+\s*)*)/;
    return s.replace(ptrn, "$1");
}
// Убирает пробельные символы справа
function rtrim(s) {
    var ptrn = /((\s*\S+)*)\s*/;
    return s.replace(ptrn, "$1");
}




function createFragFromElement(domel) {
    var frag = createFrag(domel.innerHTML);
//    domel.parentNode.removeChild(domel);
    return frag;
}

function createFrag(html) {
    var frag = document.createDocumentFragment();
    var temp = document.createElement('div');
    temp.innerHTML = html;
    while (temp.firstChild) {
        frag.appendChild(temp.firstChild);
    }
    return frag;
}
/**
 var r = m[0].cloneNode(true);
 */

function matchingNodeForKey(f, k) {
    //console.log('MD', k, f);
    var m = f.querySelectorAll('.' + k);
    if (m.length) return m[0];
    m = f.querySelectorAll('[' + k + ']');
    if (m.length) return m[0];
    m = f.querySelectorAll('#' + k);
    if (m.length) return m[0];
    //if (!cwn) cwn = f;
    //return f;
}

/**
 1 attr data-NAME > :setAttr NAME (data-)
 <figure class="image"><img data-key="image-src" src="" | {"title":"TITLECAlistScreen,last","image":{"src":"/path.jpg","alt":""}}

 2 .NAME > :setText
 3 [ATTR] > :setAttr
 4 #NAME > setText
 replace In parentNode??
 */
function queryReplaceKeyInFragment(f, k, value, pk) {
    if (!f) throw 'No fragment for key replacement';
    var wn, dk;

    if (pk)
        dk = pk + '-' + k;
    else
        dk = k;

    //console.log('DK', dk, k);
    //var dkk = '[data-'+k+']'; // [data-selector=src]
    //var dkk = '[data-selector="' + k + '"]';
    var dkk = '[data-write-'+k+']';
    var dkki = 'data-' + k;
    //console.log('DK1', pk, dk, dkk, k);
    var m = f.querySelectorAll(dkk); // if <img src=* is in root level and overlays another imgs // '[data-key='+dk+']'
    for (var i=0; i<m.length; i++) {
        wn = m[i];
        var dwrite = wn.getAttribute('data-write-'+k);
        //console.log(k, 'data-write-'+k+'=', dwrite);
        if (wn.getAttribute(k) != null) {
            //console.log('has direct attrib'); // ?????????????? used?
            wn.setAttribute(k, value);
        }
        else if (dwrite != null) {
            //console.log('has data-write-k ('+'data-write-'+k+') = '+dwrite);
            wn.setAttribute(dwrite, value);
        }
        else {
            //console.log('no direct attrib, no data-write-k ('+'data-write'+k+'). set .innerText');
            wn.innerText = value;
            //wn.setAttribute(dkki, value);
        }
        //wn.removeAttribute('data-selector');
        if (dwrite != null) wn.removeAttribute('data-write-'+k);
        if (i == m.length-1) return;
    }

    var dkk = '[data-' + k + ']';
    var m = f.querySelectorAll(dkk);
    for (var i=0; i<m.length; i++) {
        wn = m[i];
        var an = 'data-'+k;
        wn.setAttribute(an, value);
        if (i == m.length-1) return;
    }

    //console.log('DK2', dk, k, '.'+k);
    var m = f.querySelectorAll('.' + k); // for match on base level when f is target itself
    for (var i=0; i<m.length; i++) {
        wn = m[i];
        //nodeSetText(wn, value);
        wn.innerHTML = value;
        if (i == m.length-1) return;
    }

    //console.log('DK3', dk, k, '['+k+']');
    m = f.querySelectorAll('[' + k + ']');
    for (var i=0; i<m.length; i++) {
        wn = m[i];
        wn.setAttribute(k, value);
        //if (k == 'id') return;
        if (i == m.length-1) return;
    }

    //console.log('DK4', dk, k, '#'+k);
    m = f.querySelectorAll('#' + k);
    for (var i=0; i<m.length; i++) {
        wn = m[i];
        //nodeSetText(wn, value);
        wn.innerHTML = value;
        if (i == m.length-1) return;
    }
    //console.log(f, '+', k, '+', value, 'replaced: ', wn);
    // WHERE IT USED????
    if (!wn && f.parentNode) {
        //console.log('^^^ replace in f.parentNode', f, f.parentNode);
        queryReplaceKeyInFragment(f.parentNode, k, value);
    }
    if (!wn && !f.parentNode) {
        //console.log('!wn, !f.parentNode');
        if (hasClass(f, k)) {
            wn = f;
            nodeSetText(wn, value);
        }
        //console.log(k, value, f, typeof(f.getAttribute));
        if (f.getAttribute && f.getAttribute(k)) { // if not found in upper
            wn = f;
            wn.setAttribute(k, value);
        }
    }
    /*
     if (wn !== undefined)
     {
     wn.classList.add("X_"+k);
     console.log(wn, '!== undefined');
     }
     */
    return wn;
}

/**
 k
 k.k.*
 [k]
 k:[k]
 k:[k:k] ?
 k:[k:[k]] ?
 */
function recursiveKeyDataFragmentRenderer(f, d, k, pk) // , prepath
{
    //console.log('@', f, d, k, pk);
    if (!f) {
        //console.log(k, d);
        throw 'No fragment for match';
    }
    var datalocal;
    if (k) datalocal = d[k];
    else datalocal = d;
    //console.log('datalocal, d, k: ', datalocal, d, k);
    if (datalocal instanceof Array) // whole data as [] or {somekey: []
    {
        //console.log('ARRAY K', k);
        /**
         var sc = ".list";
         if (k) sc += '.'+k;
         */
        var listname;
        if (!k) listname = 'root';
        else listname = k;
        sc = "[data-list='" + listname + "']";
        var m = f.querySelectorAll(sc);
        if (m.length) {
            var pn = m[0].parentNode;
            for (var i = 0; i < datalocal.length; i++) {
                //console.log('ARRAY' + i);
                var r = m[0].cloneNode(true);
                //pn.appendChild(r);
                r.className += ' __list' + i;
                if (datalocal[i]['_level']) {
                    addClass(r, "level_" + datalocal[i]['_level']);
                }
                //console.log(datalocal[i]);
                recursiveKeyDataFragmentRenderer(r, datalocal[i]);
                pn.appendChild(r);
            }
            pn.removeChild(m[0]);
        }
        else if (typeof k == 'undefined') {
            var e = 'key undefined (' + k + '). Pk: '+pk;
            console.log(e);
        }
        else {
            var e = 'key: ' + k + ' is array(?) but no one css path "' + sc + '" found';
            //console.log(e);
            //throw e;
        }
    } // DEEP: {}
    else if (typeof(datalocal) == 'object' && k) { // на первом проходе данные являются объектом, а нам нужны вложенные объекты
        var cwn = matchingNodeForKey(f, k); // node width name of inner container (<news><_image_><img>)
        if (!cwn) cwn = f;
        //console.log('DEEP', k, f.id, cwn.id, f.innerHTML, cwn.innerHTML);
        //console.log('OBJECT, container work node, f, k', datalocal, cwn, f, k, f.id);
        for (var kk in datalocal) {
            //console.log('--', k, kk, '==', datalocal[kk], '++',  cwn);
            if (cwn) recursiveKeyDataFragmentRenderer(cwn, datalocal, kk, k); //, prepathlocal
        }
    }
    else if (typeof(datalocal) == 'object' && !k) {
        //console.log('ROOT OBJECT', datalocal, f);
        for (var kr in datalocal)
            wn = recursiveKeyDataFragmentRenderer(f, datalocal, kr, k)
    }
    else // final object for keys replace {title: etc}
    {
        //console.log('FINAL KEY', k, f, datalocal, pk);
        var wn = queryReplaceKeyInFragment(f, k, datalocal, pk);
        //console.log(k,f);
    }

}

function renderFrag(f, d) {
    if (!f) throw 'No fragment in renderFrag';
    if (!d) throw 'No data in renderFrag';
    recursiveKeyDataFragmentRenderer(f, d);
}

// todo if k: {} > delegate to (.k .kkeach)


function loadData(datahash, cb, cthis, cbparams) {
    if (typeof(datahash) == 'object') throw 'Load datahash is not string';
    if (typeof(datahash) == 'undefined') throw 'Load datahash is EMPTY';
    //console.log('LOAD DATA', datahash, cbparams);

    //var dataList = [ {href: '/url1', title: 'TITLE1', sub: 'subtitle1'}, {href: '/url2', title: 'TITLE2', sub: 'subtitle2'}, {href: '/url21', title: 'TITLE21', sub: 'subtitle21'} ];
    //var dataRead = { title: 'TITLECA'+cbparams, image: {src: '/path.jpg', alt: 'Alt', imgtitle: 'Image Title411' }  };

    // ASYNC LOAD
    var xmlhttp;
    if (window.XMLHttpRequest) {
        xmlhttp = new XMLHttpRequest();
    } else {
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.open("GET", "/" + cbparams[1] + ".json", true);
    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
            var remoteObject = JSON.parse(xmlhttp.responseText);
            //console.log(remoteObject);
            GC.cache[datahash] = remoteObject;
            cb.apply(cthis, cbparams);
        }
    }
    xmlhttp.send();
}



function getElemText(node){
    return node.text || node.textContent || (function(node){
        var _result = "";
        if (node == null) {
            return _result;
        }
        var childrens = node.childNodes;
        var i = 0;
        while (i < childrens.length) {
            var child = childrens.item(i);
            switch (child.nodeType) {
                case 5: // ENTITY_REFERENCE_NODE
                    _result += arguments.callee(child);
                    break;
                case 4: // CDATA_SECTION_NODE
                    _result += child.nodeValue;
                    break;
            }
            i++;
        }
        return _result;
    }(node));
}


/*
 var head = document.getElementsByTagName('head')[0],
 script = document.createElement('script');
 script.src = url;
 head.appendChild(script);
 In older browsers that don't support the async attribute, parser-inserted scripts block the parser..."
 IE 6 and 7 do this, only allowing one script to be downloaded at a time and nothing else. IE 8 and Safari 4 allow multiple scripts to download in parallel, but block any other resources
 <script async src="http://third-party.com/resource.js"></script>
 The browser support for it is Firefox 3.6+, IE 10+, Chrome 2+, Safari 5+, iOS 5+, Android 3+. No Opera support yet.
 (function(d, t) {
     var g = d.createElement(t),
     s = d.getElementsByTagName(t)[0];
     g.src = '//third-party.com/resource.js';
     s.parentNode.insertBefore(g, s);
 }(document, 'script'));
 <script type="text/javascript">
 (function(){
     var bsa = document.createElement('script');
     bsa.type = 'text/javascript';
     bsa.async = true;
     bsa.src = '//s3.buysellads.com/ac/bsa.js';
     (document.getElementsByTagName('head')[0]||document.getElementsByTagName('body')[0]).appendChild(bsa);
 })();
 </script>
 //url - protocol-relative URL. This is a darn useful way to load the script from either HTTP or HTTPS depending on the page that requested it
 The defer attribute makes a solemn promise to the browser. It states that your JavaScript does not contain any document.write or DOM modification nastiness:
 <script src="file.js" defer></script> IE 4+
 While all deferred scripts are guaranteed to run in sequence, it’s difficult to determine when that will occur. In theory, it should happen after the DOM has completely loaded, shortly before the DOMContentLoaded event. In practice, it depends on the OS and browser, whether the script is cached, and what other scripts are doing at the time.

 */
function loadModule(path, name, cb, cthis, params) {
    //console.log('loadModule',path, name);
    function async_load() {
        var path = this[0];
        var name = this[1];
        var s = document.createElement('script');
        s.type = 'text/javascript';
        s.async = true;
        s.src = '/js/' + path + '/' + name + '.js';
        s.onload = s.onreadystatechange = function (s) {
            //console.log(path, name, 'ONLOAD script');
            GC[path][name].init();
            if (cb)
                cb.apply(cthis, params);
        };
        var x = document.getElementsByTagName('script')[0];
        x.parentNode.insertBefore(s, x);
    }

    async_load.bind([path, name]).call(null, cb); //
}

function makeQS(arr) {
    var s = "";
    for (var e in arr) {
        s += "&" + e + "=" + encodeURIComponent(arr[e]);
    }
    return s.substring(1);
}


function makeHREF(url, arr) {
    if (arr)
        return url + "?" + makeQS(arr);
    else return url;
}




function EQWC() {
    //if (RegExp(" Mobile/").test(navigator.userAgent)) return;
    var ws = document.querySelectorAll(".EQWC .widget");
    var elH = new ArrayExtended();
    for (var i = 0; i < ws.length; i++)
        elH.push(elHeight(ws[i]));
    var maxH = elH.max();
    for (var i = 0; i < ws.length; i++)
        elHeight(ws[i], maxH);
}
function EQWC2() {
    //if (RegExp(" Mobile/").test(navigator.userAgent)) return;
    var ws = document.querySelectorAll(".EQWC2 .widget");
    var elH = new ArrayExtended();
    for (var i = 0; i < ws.length; i++)
        elH.push(elHeight(ws[i]));
    var maxH = elH.max();
    for (var i = 0; i < ws.length; i++)
        elHeight(ws[i], maxH);
}

// ie 7-8
/**
 if (typeof document.defaultView == 'undefined')
 {
 document.defaultView = {};
 }
 if (typeof document.defaultView.getComputedStyle == 'undefined')
 {
 document.defaultView.getComputedStyle = function(element, pseudoElement)
 {
 return element.currentStyle;
 }
 console.log(document.defaultView.getComputedStyle);
 }
 */
/**
 if (!window.getComputedStyle)
 {
 window.getComputedStyle = function(el, pseudo) {
 this.el = el;
 this.getPropertyValue = function(prop) {
 var re = /(\-([a-z]){1})/g;
 if (prop == 'float') prop = 'styleFloat';
 if (re.test(prop)) {
 prop = prop.replace(re, function () {
 return arguments[2].toUpperCase();
 });
 }
 return el.currentStyle[prop] ? el.currentStyle[prop] : null;
 }
 return this;
 }
 }
 */

pageloaded = false;
// dom ready - html loaded
// onload - images loaded
window.onload = function () {
    if (!pageloaded) {
        pageloaded = true;
        init2();
    }
};

if (document.addEventListener) {
    document.addEventListener("DOMContentLoaded", function () {
        if (!pageloaded) {
            pageloaded = true;
            init1();
        }
    }, false);
}

function init1() {
//    console.log('init1 domready');
    if (window['gcmain']) gcmain();
}

function init2() {
//    console.log('init2 onload');
    if (window['gcmain']) gcmain();
//    if (typeof main == 'function') main();
}

/**
var readyStateCheckInterval = setInterval(function() {
    if (document.readyState === "complete") {
        init3();
        clearInterval(readyStateCheckInterval);
    }
}, 10);
*/

//EQWC();
//EQWC2();


/**
 // ie dom load simplest - move the code to the bottom of the page instead of using DOMReady event
 // ie6
 var readyStateCheckInterval = setInterval(function() {
 if (document.readyState === "complete") {
 init3();
 clearInterval(readyStateCheckInterval);
 }
 }, 10);

 // ie7 dom load
 if (document.all && !window.opera){ //Crude test for IE
 //Define a "blank" external JavaScript tag
 document.write('<script type="text/javascript" id="contentloadtag" defer="defer" src="javascript:void(0)"><\/script>')
 var contentloadtag=document.getElementById("contentloadtag")
 contentloadtag.onreadystatechange=function(){
 if (this.readyState=="complete")
 walkmydog()
 }
 }
 */

/**

 */


// <abbr class="timeago" title="2008-07-17T09:24:17Z">July 17, 2008</abbr>
function timeAgo(time)
{
    var ts = parseInt(time);
    //var date = new Date((time || "").replace(/-/g,"/").replace(/[TZ]/g," ")),
    // var ts = date.getTime();
    var now = (new Date()).getTime() / 1000;
    var diff = now - ts;
    var day_diff = Math.ceil(diff / 86400);
    if ( isNaN(day_diff) || day_diff < 0 ) return 'time_shift';
    return day_diff == 0 && (
        diff < 60 && "Сейчас" ||
            diff < 120 && "Минуту назад" ||
            diff < 3600 && Math.floor( diff / 60 ) + " минут" ||
            diff < 7200 && "Час назад" ||
            diff < 86400 && Math.floor( diff / 3600 ) + " часов") ||
        day_diff == 1 && "Вчера" ||
        day_diff < 7 && day_diff + " дней назад" ||
        day_diff < 31 && Math.ceil( day_diff / 7 ) + " недели" ||
        day_diff > 31 && Math.ceil( day_diff / 31 ) + " месяцев";
}



function getSelectedText()
{
    var txt = '';
    if (window.getSelection)
    {
        txt = window.getSelection();
    }
    else if (document.getSelection) // FireFox
    {
        txt = document.getSelection();
    }
    else if (document.selection)  // IE 6/7
    {
        txt = document.selection.createRange().text;
    }
    else return;
    return txt;
}


String.prototype.emoji = function(){
    if (!window['ioNull'])
    {
        console.log('Emoji.js not included');
        return this;
    }
    return ioNull.emoji.parse(this);
}

String.prototype.parseURL = function() {
    return this.replace(/[A-Za-z]+:\/\/[A-Za-z0-9-_]+\.[A-Za-z0-9-_:%&~\?\/.=]+/g, function(url) {
        return url.link(url);
    });
};
String.prototype.parseHashtag = function() {
    return this.replace(/[#]+[A-Za-z0-9-_]+/g, function(t) {
        //var tag = t.replace("#","%23")
        var tag = t.replace("#","");
        return t.link("#tag/"+tag);
    });
};
String.prototype.parseUsername = function() {
    return this.replace(/[@]+[A-Za-z0-9-_]+/g, function(u) {
        var username = u.replace("@","");
        //var username = u;
        return u.link("#user/"+username);
    });
};
String.prototype.forceUsername = function() {
    return this.link("#user/"+this);
};


Report = function(ismulty){
    this.meta = {}
    if (ismulty instanceof Array)
        this.chart = []
    else
        this.chart = {}
}

// EVENTS
/**
 Event.add(slider, "click", function(e) { alert("Hi") })
 */
Event = (function () {

    var guid = 0

    function fixEvent(event) {
        event = event || window.event

        if (event.isFixed) {
            return event
        }
        event.isFixed = true

        event.preventDefault = event.preventDefault || function () {
            this.returnValue = false
        }
        event.stopPropagation = event.stopPropagaton || function () {
            this.cancelBubble = true
        }

        if (!event.target) {
            event.target = event.srcElement
        }

        if (!event.relatedTarget && event.fromElement) {
            event.relatedTarget = event.fromElement == event.target ? event.toElement : event.fromElement;
        }

        if (event.pageX == null && event.clientX != null) {
            var html = document.documentElement, body = document.body;
            event.pageX = event.clientX + (html && html.scrollLeft || body && body.scrollLeft || 0) - (html.clientLeft || 0);
            event.pageY = event.clientY + (html && html.scrollTop || body && body.scrollTop || 0) - (html.clientTop || 0);
        }

        if (!event.which && event.button) {
            event.which = (event.button & 1 ? 1 : ( event.button & 2 ? 3 : ( event.button & 4 ? 2 : 0 ) ));
        }

        return event
    }

    /* Вызывается в контексте элемента всегда this = element */
    function commonHandle(event) {
        event = fixEvent(event)

        var handlers = this.events[event.type]

        for (var g in handlers) {
            var handler = handlers[g]

            var ret = handler.call(this, event)
            if (ret === false) {
                event.preventDefault()
                event.stopPropagation()
            }
        }
    }

    return {
        add:function (elem, type, handler) {
        	if (!elem) {
                if (window['printStackTrace']) console.log(printStackTrace());
                throw 'No domel provided for Event.add. May be early call before dom loaded';
            }
            if (elem.setInterval && ( elem != window && !elem.frameElement )) {
                elem = window;
            }

            if (!handler.guid) {
                handler.guid = ++guid
            }

            if (!elem.events) {
                elem.events = {}
                elem.handle = function (event) {
                    if (typeof Event !== "undefined") {
                        return commonHandle.call(elem, event)
                    }
                }
            }

            if (!elem.events[type]) {
                elem.events[type] = {}

                if (elem.addEventListener)
                    elem.addEventListener(type, elem.handle, false)
                else if (elem.attachEvent)
                    elem.attachEvent("on" + type, elem.handle)
            }

            elem.events[type][handler.guid] = handler
        },

        remove:function (elem, type, handler) {
            var handlers = elem.events && elem.events[type]

            if (!handlers) return

            delete handlers[handler.guid]

            for (var any in handlers) return
            if (elem.removeEventListener)
                elem.removeEventListener(type, elem.handle, false)
            else if (elem.detachEvent)
                elem.detachEvent("on" + type, elem.handle)

            delete elem.events[type]


            for (var any in elem.events) return
            try {
                delete elem.handle
                delete elem.events
            } catch (e) { // IE
                elem.removeAttribute("handle")
                elem.removeAttribute("events")
            }
        }
    }
}())


function timestampNow(){
    return Math.round(new Date().getTime() / 1000);
}

/*
 var infiniteScrollPreloadOffsetOriginal = infiniteScrollPreloadOffset = 2000;
 // INSIDE NEWELEMENTS ADDER
 if (pager === false) scrolleventlock = true;
 else scrolleventlock = false;
 if (data.data.length < 10) infiniteScrollPreloadOffset = 500;
 else infiniteScrollPreloadOffset = infiniteScrollPreloadOffsetOriginal;
 scrolleventlock = false;
 */
/*
 window.onscroll = function(ev) {
 if (scrolleventlock) return;
 var scrolldiff = document.body.offsetHeight - (window.innerHeight + window.scrollY);
 //console.log(scrolldiff);
 if (scrolldiff <= infiniteScrollPreloadOffset) {
 console.log('END OF PAGE', pager);
 scrolleventlock = true;
 // what to paginate?
 if (currentScreenName == 'tag') myInstagram.getTagMedia(tag, null, pager);
 }
 };
 */


/** Функция для перевода на лат и обратно на рус. Если с английского на русский, то передаём вторым параметром true.
 var txt = "Съешь ещё этих мягких французских булок, да выпей же чаю!";
 alert(transliterate(txt));
 alert(transliterate(transliterate(txt), true));
 */

var transliterate = (
    function() {
        var
            rus = "щ   ш  ч  ц  ю  я  ё  ж  ъ  ы  э  а б в г д е з и й к л м н о п р с т у ф х ь ї".split(/ +/g),
            eng = "shh sh ch cz yu ya yo zh `` y' e` a b v g d e z i j k l m n o p r s t u f x ` i".split(/ +/g)
            ;
        return function(text, engToRus) {
            var x;
            for(x = 0; x < rus.length; x++) {
                text = text.split(engToRus ? eng[x] : rus[x]).join(engToRus ? rus[x] : eng[x]);
                text = text.split(engToRus ? eng[x].toUpperCase() : rus[x].toUpperCase()).join(engToRus ? rus[x].toUpperCase() : eng[x].toUpperCase());
            }
            return text;
        }
    }
    )();
