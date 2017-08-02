var TINY={}; //Tiny Table Sort by Michael Leigeber at http://www.leigeber.com/2009/03/table-sorter/  -chopped to minimums and augmented by MLS
function T$(i){return document.getElementById(i)}
function T$$(e,p){return p.getElementsByTagName(e)}
TINY.table=function(){
function sorter(n){this.n=n;}
sorter.prototype.init=function(e,f){
	var t=ge(e), i=0; this.e=e; this.l=t.r.length; t.a=[]; t.h=T$$('thead',T$(e))[0].rows[0]; t.w=t.h.cells.length; this.head='head';
	if (this.reverse){this.asc='desc';this.desc='asc';}else{this.asc='asc';this.desc='desc';}
	for(i;i<t.w;i++){var c=t.h.cells[i]; var st=cp; if(c.className!='nosort'){if (c.className=='natsort') st=ns; c.className=this.head; c.onclick=new Function(this.n+'.wk(this.cellIndex,'+st+')')}}
	for(i=0;i<this.l;i++){t.a[i]={}}
	if(f!=null){var a=new Function(this.n+'.wk('+f+','+st+')'); a()}
	};
sorter.prototype.wk=function(y,st){
	var t=ge(this.e), x=t.h.cells[y], i=0;
	for(i;i<this.l;i++){
		t.a[i].o=i; var v=t.r[i].cells[y]; t.r[i].style.display='';
		while(v.hasChildNodes()){v=v.firstChild}
		t.a[i].v=v.nodeValue?v.nodeValue:''
		}
	for(i=0;i<t.w;i++){var c=t.h.cells[i]; if(c.className!='nosort'){c.className=this.head}}
	if(t.p==y){t.a.reverse(); x.className=t.d?this.asc:this.desc; t.d=t.d?0:1}
	else{t.p=y; if(this.reverse)t.a.sort(st).reverse(); else t.a.sort(st); t.d=0; x.className=this.asc} //woo hoo
	var n=document.createElement('tbody');
	for(i=0;i<this.l;i++){var r=t.r[t.a[i].o].cloneNode(true); n.appendChild(r);}
	t.replaceChild(n,t.b);
	if (window.wnksClick)
		wnksClick();
	};
function ge(e){var t=T$(e); t.b=T$$('tbody',t)[0]; t.r=t.b.rows; return t};
function cp(f,c){
	var g,h; f=g=f.v.toLowerCase(), c=h=c.v.toLowerCase();
	var i=parseFloat(f.replace(/(\$|\,)/g,'')), n=parseFloat(c.replace(/(\$|\,)/g,''));
	if(!isNaN(i)&&!isNaN(n)){g=i,h=n}
	i=Date.parse(f); n=Date.parse(c);
	if(!isNaN(i)&&!isNaN(n)){g=i; h=n}
	return g>h?1:(g<h?-1:0)
	};
/*
 * Natural Sort algorithm for Javascript - Version 0.6 - Released under MIT license
 * Author: Jim Palmer (based on chunking idea from Dave Koelle)
 * Contributors: Mike Grier (mgrier.com), Clint Priest, Kyle Adams, guillermo
 * http://www.overset.com/2008/09/01/javascript-natural-sort-algorithm-with-unicode-support/
 */
function ns(a,b) {
	var re = /(^-?[0-9]+(\.?[0-9]*)[df]?e?[0-9]?$|^0x[0-9a-f]+$|[0-9]+)/gi,
	sre = /(^[ ]*|[ ]*$)/g,
	dre = /(^([\w ]+,?[\w ]+)?[\w ]+,?[\w ]+\d+:\d+(:\d+)?[\w ]?|^\d{1,4}[\/\-]\d{1,4}[\/\-]\d{1,4}|^\w+, \w+ \d+, \d{4})/,
	hre = /^0x[0-9a-f]+$/i,
	ore = /^0/,
	x = a.v.toString().toLowerCase().replace(sre, '') || '',
	y = b.v.toString().toLowerCase().replace(sre, '') || '',
	xN = x.replace(re, '\0$1\0').replace(/\0$/,'').replace(/^\0/,'').split('\0'),
	yN = y.replace(re, '\0$1\0').replace(/\0$/,'').replace(/^\0/,'').split('\0'),
	xD = parseInt(x.match(hre)) || (xN.length != 1 && x.match(dre) && Date.parse(x)),
	yD = parseInt(y.match(hre)) || xD && y.match(dre) && Date.parse(y) || null;
	if (yD)
	if ( xD < yD ) return -1;
	else if ( xD > yD )
	return 1;
	for(var cLoc=0, numS=Math.max(xN.length, yN.length); cLoc < numS; cLoc++) {
	oFxNcL = !(xN[cLoc] || '').match(ore) && parseFloat(xN[cLoc]) || xN[cLoc] || 0;
	oFyNcL = !(yN[cLoc] || '').match(ore) && parseFloat(yN[cLoc]) || yN[cLoc] || 0;
	if (isNaN(oFxNcL) !== isNaN(oFyNcL)) return (isNaN(oFxNcL)) ? 1 : -1;
	else if (typeof oFxNcL !== typeof oFyNcL) {
		oFxNcL += '';
		oFyNcL += '';
		}
	if (oFxNcL < oFyNcL) return -1;
	if (oFxNcL > oFyNcL) return 1;
	}
	return 0;
	}
return{sorter:sorter}
}();

//jQuery(document).ready(function() {
jQuery(document).ready(function(){tdPop1();});
function tdPop1() {
	jQuery(".ecpop").delegate(".tdPop", "mouseover mouseout", function(event){
		pop = jQuery(this).find('.pop');
		if (event.type=='mouseover') {
			proto = pop.text();
			pp = "<table class='poptable' summary='pop score'><tr><th colspan='2'>Score</th></tr>";
			if (proto == "x")
				pp += "<tr><td colspan='2' class='nada'>Nothing to see here</td></tr>";
			else {
				t = "";
				q = proto.split("|");
				for (i=1;i<q.length;i++) {
					r = q[i].split(":");
					pp += "<tr><td>" + t + r[0] + "</td><td class='data'>" + r[1] + "</td></tr>";
					if (r[0] == "Killed")
						t = " &middot; ";
					}
				}
			pp += "</table>";
			pop.html(pp);
			pop.show();
			}
		else {
			pop.hide();
			pop.html(proto);
			}
	}).mousemove(function(e) {
		pop = jQuery(this).find('.pop');
		yMin = jQuery(window).scrollTop() + 8; //top of window
		yMax = jQuery(window).scrollTop() + jQuery(window).height() - 152; //bottom of window
		y = e.pageY - 60;
		if (y < yMin)
			y = yMin;
		else if (y > yMax)
			y = yMax;
		pop.css({left:e.pageX - 230,top:y});
		});
	};
//	});
