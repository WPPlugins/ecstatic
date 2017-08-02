jQuery(document).ready(function(){wnksClick();});
function wnksClick() { //jams wnks table row into form, for editing
jQuery('#wnkstable tr').click(function(event) {
	if (typeof wnksClick.state == 'undefined') {
		wnksClick.state="closed";
		wnksClick.prev=0;
		wnksClick.html="";
		}
	if (wnksClick.state=="open" && jQuery(this).attr('id')=='trX') { //close form before sorting
		jQuery("#wnkstable #"+wnksClick.prev).html(wnksClick.html);
		wnksClick.state="closed";
		return;
		}
	if ((wnksClick.prev==jQuery(this).attr('id') && wnksClick.state=="open") || jQuery(this).attr('id') == 'trX' || jQuery(this).attr('id') == null || jQuery(this).attr('class')=="deleted")
		return;
	if (wnksClick.state=="open" && wnksClick.prev!=jQuery(this).attr('id')) {
		jQuery("#wnkstable #"+wnksClick.prev).html(wnksClick.html);
		wnksClick.state="closed";
		}
	wnksClick.prev=jQuery(this).attr('id');
	wnksClick.html=jQuery(this).html();
	wnksClick.state="open";

	var tds='<td colspan="10"><div id="wnkser"><form id="wnksedit" method="post" action="">';

	tds+='<table id="inEdit">';
	tds+='<thead><tr id="trX"><th>Name</th><th>Token</th><th>Type</th><th>Last&nbsp;Seen</th><th>Count</th><th>Bot</th><th>Kill</th><th>NS</th><th>WL</th><th>xW</th></tr></thead>';
	tds+='<tr><td><input id="ename" type="text" name="name" value="'+jQuery(this).children('td:nth-child(1)').text()+'" /></td>';
	tds+='<td><input id="etoken" type="text" name="token" value="'+jQuery(this).children('td:nth-child(2)').text()+'" /></td>';
	var selectx=jQuery(this).children("td:nth-child(3)").text();
	tds+='<td><select id="selectbox" name="type" size="1">';
	tds+='<option value="ua"> UA </option>';
	tds+='<option value="ip"> IP </option>';
	tds+='<option value="ref"> Ref </option>';
	tds+='<option value="ruri"> RURI </option>';
	tds+='<option value="mix"> Mix </option>';
	tds+='</select></td>';
	tds+='<td>'+jQuery(this).children('td:nth-child(4)').text()+'</td>'; //no edit
	tds+='<td>'+jQuery(this).children('td:nth-child(5)').text()+'</td>'; //no edit
	tds+='<td><input type="checkbox" name="spider" value="1"'+((jQuery(this).children("td:nth-child(6)").text()=='X')?" checked ":"")+' /></td>';
	tds+='<td><input type="checkbox" name="kill" value="2"'+((jQuery(this).children("td:nth-child(7)").text()=='X')?" checked ":"")+' /></td>';
	tds+='<td><input type="checkbox" name="nolog" value="4"'+((jQuery(this).children("td:nth-child(8)").text()=='X')?" checked ":"")+' /></td>';
	tds+='<td><input type="checkbox" name="wlist" value="8"'+((jQuery(this).children("td:nth-child(9)").text()=='X')?" checked ":"")+' /></td>';
	tds+='<td><input type="checkbox" name="xwlist" value="16"'+((jQuery(this).children("td:nth-child(10)").text()=='X')?" checked ":"")+' /></td>';
	tds+='</tr></table>';

	tds+='<div id="wnksbuttons"><input id="eid" type="hidden" name="id" value="'+wnksClick.prev+'" />';
	tds+='<input id="elastseen" type="hidden" name="lastseen" value="'+jQuery(this).children('td:nth-child(4)').html()+'" />';
	tds+='<input id="ecount" type="hidden" name="count" value="'+jQuery(this).children('td:nth-child(5)').text()+'" />';
	tds+='<input id="wnkssubsave" type="submit" name="wnks_submit_save" value=" Save " /><br />';
	tds+='<input id="wnkssubcancel" type="submit" name="wnks_submit_cancel" value="Cancel" /><br />';
	tds+='<input id="wnkssubdelete" type="submit" name="wnks_submit_delete" value="Delete" />';
	tds+='</div><div class="clear"></div>';

	tds+='</form></div></td>';

	jQuery(this).html(tds);
	jQuery("#selectbox option[value='"+selectx+"']").attr("selected", true); //only after the form is output?

	jQuery('#wnkssubsave').click(function(){
		var data = {
			id: jQuery("#eid").val(),
			name: jQuery("#ename").val(),
			token: jQuery("#etoken").val(),
			type: jQuery("#selectbox").val(),
			lastseen: jQuery("#elastseen").val(),
			count: jQuery("#ecount").val(),
			spider: jQuery('#wnksedit input[name="spider"]').is(':checked'),
			kill: jQuery('#wnksedit input[name="kill"]').is(':checked'),
			nolog: jQuery('#wnksedit input[name="nolog"]').is(':checked'),
			wlist: jQuery('#wnksedit input[name="wlist"]').is(':checked'),
			xwlist: jQuery('#wnksedit input[name="xwlist"]').is(':checked'),
			ecstatit: "wnksplay", //precludes dummy action
			action: 'dummy_function' //function is in ecstatic.php
			};
		jQuery.post(ajaxurl, data, function(response) {
			if (response.indexOf('ERR') != -1)
				alert(response);
			else {
				jQuery("#wnkstable #"+data.id).html(response);
				wnksClick.state="closed";
				}
			});
		jQuery("#wnkstable #"+wnksClick.prev).addClass("saved");
		return false;
		});
	jQuery('#wnkssubcancel').click(function(){
		jQuery("#wnkstable #"+wnksClick.prev).html(wnksClick.html);
		jQuery("#wnkstable #"+wnksClick.prev).addClass("esc");
		wnksClick.state="closed";
		return false;
		});
	jQuery('#wnkssubdelete').click(function(){
		var data = {
			id: jQuery("#eid").val(),
			token: jQuery("#etoken").val(),
			type: jQuery("#selectbox").val(),
			wnks: "doDelete",
			spider: jQuery('#wnksedit input[name="spider"]').is(':checked'),
			kill: jQuery('#wnksedit input[name="kill"]').is(':checked'),
			nolog: jQuery('#wnksedit input[name="nolog"]').is(':checked'),
			wlist: jQuery('#wnksedit input[name="wlist"]').is(':checked'),
			xwlist: jQuery('#wnksedit input[name="xwlist"]').is(':checked'),
			ecstatit: "wnksplay", //precludes dummy action
			action: 'dummy_function'
			};
		jQuery.post(ajaxurl, data, function(response) {
			if (response.indexOf('ERR') != -1)
				alert(response);
			else {
				jQuery("#wnkstable #"+data.id).html(response);
				wnksClick.state="deleted";
				}
			});
		jQuery("#wnkstable #"+wnksClick.prev).addClass("deleted").removeClass("esc saved");
		return false;
		});
	});
}; //wnksClick

jQuery(document).ready(function(){nipsClick();});
function nipsClick() { //jams nips table row into form, for editing
jQuery('#nipstable tr').click(function(event) {
	if (typeof nipsClick.state == 'undefined') {
		nipsClick.state="closed";
		nipsClick.prev=0;
		nipsClick.html="";
		}
	if (nipsClick.state=="open" && jQuery(this).attr('id')=='trZ') { //close form before sorting
		jQuery("#nipstable #"+nipsClick.prev).html(nipsClick.html);
		nipsClick.state="closed";
		return;
		}
	if ((nipsClick.prev==jQuery(this).attr('id') && nipsClick.state=="open") || jQuery(this).attr('id') == 'trZ' || jQuery(this).attr('id') == null || jQuery(this).attr('class')=="deleted")
		return;
	if (nipsClick.state=="open" && nipsClick.prev!=jQuery(this).attr('id')) {
		jQuery("#nipstable #"+nipsClick.prev).html(nipsClick.html);
		nipsClick.state="closed";
		}
	nipsClick.prev=jQuery(this).attr('id');
	nipsClick.html=jQuery(this).html();
	nipsClick.state="open";

	var tds='<td colspan="10"><div id="wnkser"><form id="wnksedit" method="post" action="">';

	tds+='<table id="inEdit">';
	tds+='<thead><tr id="trZ"><th>Name</th><th>Token</th><th>Type</th><th>Last&nbsp;Seen</th><th>Count</th><th>Bot</th><th>Kill</th><th>NS</th><th>WL</th><th>xW</th></tr></thead>';
	tds+='<tr><td><input id="ename" type="text" name="name" value="'+jQuery(this).children('td:nth-child(1)').text()+'" /></td>';
	tds+='<td><input id="etoken" type="text" name="token" value="'+jQuery(this).children('td:nth-child(2)').text()+'" /></td>';
	var selectx=jQuery(this).children("td:nth-child(3)").text();
	tds+='<td><select id="selectbox" name="type" size="1">';
	tds+='<option value="ua"> UA </option>';
	tds+='<option value="ip"> IP </option>';
	tds+='<option value="ref"> Ref </option>';
	tds+='<option value="ruri"> RURI </option>';
	tds+='<option value="mix"> Mix </option>';
	tds+='</select></td>';
	tds+='<td>'+jQuery(this).children('td:nth-child(4)').text()+'</td>'; //no edit
	tds+='<td>'+jQuery(this).children('td:nth-child(5)').text()+'</td>'; //no edit
	tds+='<td><input type="checkbox" name="spider" value="1"'+((jQuery(this).children("td:nth-child(6)").text()=='X')?" checked ":"")+' /></td>';
	tds+='<td><input type="checkbox" name="kill" value="2"'+((jQuery(this).children("td:nth-child(7)").text()=='X')?" checked ":"")+' /></td>';
	tds+='<td><input type="checkbox" name="nolog" value="4"'+((jQuery(this).children("td:nth-child(8)").text()=='X')?" checked ":"")+' /></td>';
	tds+='<td><input type="checkbox" name="wlist" value="8"'+((jQuery(this).children("td:nth-child(9)").text()=='X')?" checked ":"")+' /></td>';
	tds+='<td><input type="checkbox" name="xwlist" value="16"'+((jQuery(this).children("td:nth-child(10)").text()=='X')?" checked ":"")+' /></td>';
	tds+='</tr></table>';

	tds+='<div id="wnksbuttons"><input id="eid" type="hidden" name="id" value="'+nipsClick.prev+'" />';
	tds+='<input id="elastseen" type="hidden" name="lastseen" value="'+jQuery(this).children('td:nth-child(4)').html()+'" />';
	tds+='<input id="ecount" type="hidden" name="count" value="'+jQuery(this).children('td:nth-child(5)').text()+'" />';
	tds+='<input id="nipssubsave" type="submit" name="nips_submit_save" value=" Save " /><br />';
	tds+='<input id="nipssubcancel" type="submit" name="nips_submit_cancel" value="Cancel" /><br />';
	tds+='<input id="nipssubdelete" type="submit" name="nips_submit_delete" value="Delete" />';
	tds+='</div><div class="clear"></div>';

	tds+='</form></div></td>';

	jQuery(this).html(tds);
	jQuery("#selectbox option[value='"+selectx+"']").attr("selected", true); //only after the form is output?

	jQuery('#nipssubsave').click(function(){
		var data = {
			id: jQuery("#eid").val(),
			name: jQuery("#ename").val(),
			token: jQuery("#etoken").val(),
			type: jQuery("#selectbox").val(),
			lastseen: jQuery("#elastseen").val(),
			count: jQuery("#ecount").val(),
			spider: jQuery('#wnksedit input[name="spider"]').is(':checked'),
			kill: jQuery('#wnksedit input[name="kill"]').is(':checked'),
			nolog: jQuery('#wnksedit input[name="nolog"]').is(':checked'),
			wlist: jQuery('#wnksedit input[name="wlist"]').is(':checked'),
			xwlist: jQuery('#wnksedit input[name="xwlist"]').is(':checked'),
			ecstatit: "wnksplay", //precludes dummy action
			action: 'dummy_function' //function is in ecstatic.php
			};
		jQuery.post(ajaxurl, data, function(response) {
			if (response.indexOf('ERR') != -1)
				alert(response);
			else {
				jQuery("#nipstable #"+data.id).html(response);
				nipsClick.state="closed";
				}
			});
		jQuery("#nipstable #"+nipsClick.prev).addClass("saved");
		return false;
		});
	jQuery('#nipssubcancel').click(function(){
		jQuery("#nipstable #"+nipsClick.prev).html(nipsClick.html);
		jQuery("#nipstable #"+nipsClick.prev).addClass("esc");
		nipsClick.state="closed";
		return false;
		});
	jQuery('#nipssubdelete').click(function(){
		var data = {
			id: jQuery("#eid").val(),
			token: jQuery("#etoken").val(),
			type: jQuery("#selectbox").val(),
			wnks: "doDelete",
			spider: jQuery('#wnksedit input[name="spider"]').is(':checked'),
			kill: jQuery('#wnksedit input[name="kill"]').is(':checked'),
			nolog: jQuery('#wnksedit input[name="nolog"]').is(':checked'),
			wlist: jQuery('#wnksedit input[name="wlist"]').is(':checked'),
			xwlist: jQuery('#wnksedit input[name="xwlist"]').is(':checked'),
			ecstatit: "wnksplay", //precludes dummy action
			action: 'dummy_function'
			};
		jQuery.post(ajaxurl, data, function(response) {
			if (response.indexOf('ERR') != -1)
				alert(response);
			else {
				jQuery("#nipstable #"+data.id).html(response);
				nipsClick.state="deleted";
				}
			});
		jQuery("#nipstable #"+nipsClick.prev).addClass("deleted").removeClass("esc saved");
		return false;
		});
	});
}; //nipsClick

jQuery(document).ready(function(){
	jQuery("form#dsearch").submit(function() { //details page SEARCH form
		jQuery("#searched").text("");
		jQuery("#blurb").show("slow");
		var data = {
			action: 'details_search',
			detsrch: jQuery("#dsearchtext").val()
			};
		jQuery.post(ajaxurl, data, function(response) {
			jQuery("#blurb").hide("fast");
//			jQuery("#dsearch:submit:visible:enabled:first").focus();
			jQuery("#dsearchtext").select();
			jQuery("#searched").html(response);
			});
		return false;
		});

	jQuery('#view').click(function(e) { //details page View/Export form
		jQuery("#wnksj").text("");
		var data = {
			action: 'wnks_filter',
			xor: jQuery('#wnksform input[name="xor"]:checked').val(),
			spider: jQuery('#wnksform input[name="spider"]').is(':checked'),
			kill: jQuery('#wnksform input[name="kill"]').is(':checked'),
			nolog: jQuery('#wnksform input[name="nolog"]').is(':checked'),
			wlist: jQuery('#wnksform input[name="wlist"]').is(':checked'),
			xwlist: jQuery('#wnksform input[name="xwlist"]').is(':checked'),
			yo_ajax: true
			};
		jQuery.post(ajaxurl, data, function(response) {
			jQuery("#wnksj").html(response);
			});
		return false;
		});

	jQuery('#jQip').click(function() { //details page TOGGLE NETRANGE/IP form
		jQuery("#netrange").val(this.title);
		return false;
		});
	jQuery('#jQnetrange').click(function() {
		jQuery("#netrange").val(this.title);
		return false;
		});
	jQuery('#browser_help').click(function() {
		jQuery("#browser_id_help").toggle("slow");
		return false;
		});

	jQuery('#addwnks').submit(function() { //details page VALIDATE ADD FORM
		a = jQuery('#addwnks input[name="spbo"]').is(':checked') | jQuery('#addwnks input[name="kill"]').is(':checked') | jQuery('#addwnks input[name="nolog"]').is(':checked') | jQuery('#addwnks input[name="wlist"]').is(':checked');
		b = jQuery('#addwnks input[name="comname"]').val();
		if (!a || !b) {
			wnkslert = "";
			if (!a)
				wnkslert = "At least one of 'Spider/Bot', 'Kill', 'NoShow', or 'WhiteList' must be checked\n\n";
			if (!b)
				wnkslert += "A Common Name is very helpful, and is required.";
			alert(wnkslert);
			return false;
			}
		});
	});

/*
DOMtab by Christian Heilmann -- Version 3.1415927 http://onlinetools.org/tools/domtabdata/ -- Updated March the First 2006
check blog for updates: http://www.wait-till-i.com
free to use, not free to resell
****** Heavily redacted by MikeSoja for ecSTATic Wordpress plugin! ****
*/
domtab={
	tabClass:'domtab', // class to trigger tabbing
	listClass:'domtabs', // class of the menus
	activeClass:'active', // class of current link
	contentElements:'div', // elements to loop through
	init:function(){
		var temp;
		if(!document.getElementById || !document.createTextNode){return;}
		var tempelm=document.getElementsByTagName('div');
		for(var i=0;i<tempelm.length;i++){
			domtab.initTabMenu(tempelm[i]);
			}
		if(document.getElementById(domtab.printID)
		   && !document.getElementById(domtab.printID).getElementsByTagName('a')[0]){
			var newlink=document.createElement('a');
			newlink.setAttribute('href','#');
			domtab.addEvent(newlink,'click',domtab.showAll,false);
			newlink.onclick=function(){return false;} // safari hack
			newlink.appendChild(document.createTextNode(domtab.showAllLinkText));
			document.getElementById(domtab.printID).appendChild(newlink);
			}
		},

	initTabMenu:function(menu){
		var id;
		var lists=menu.getElementsByTagName('ul');
		for(var i=0;i<lists.length;i++){
			if(domtab.cssjs('check',lists[i],domtab.listClass)){
				var thismenu=lists[i];
				break;
			}
		}
		if(!thismenu){return;}
		thismenu.currentSection='';
		thismenu.currentLink='';
		var links=thismenu.getElementsByTagName('a');
		for(i=0;i<links.length;i++){
			if(!/#/.test(links[i].getAttribute('href').toString())){continue;}
			id=links[i].href.match(/#(\w.+)/)[1];
			if(document.getElementById(id)){
				domtab.addEvent(links[i],'click',domtab.showTab,false);
				links[i].onclick=function(){return false;} // safari hack
				domtab.changeTab(document.getElementById(id),0);
			}
		}
		id=links[0].href.match(/#(\w.+)/)[1];
		if(document.getElementById(id)){
			domtab.changeTab(document.getElementById(id),1);
			thismenu.currentSection=id;
			thismenu.currentLink=links[0];
			domtab.cssjs('add',links[0].parentNode,domtab.activeClass);
		}
	},

	changeTab:function(elm,state){
		do{
			elm=elm.parentNode;
		} while(elm.nodeName.toLowerCase() != domtab.contentElements)
		elm.style.display=state==0?'none':'block';
	},
	showTab:function(e){
		var o=domtab.getTarget(e);
		if(o.parentNode.parentNode.currentSection!=''){
			domtab.changeTab(document.getElementById(o.parentNode.parentNode.currentSection),0);
			domtab.cssjs('remove',o.parentNode.parentNode.currentLink.parentNode,domtab.activeClass);
		}
		var id=o.href.match(/#(\w.+)/)[1];
		o.parentNode.parentNode.currentSection=id;
		o.parentNode.parentNode.currentLink=o;
		domtab.cssjs('add',o.parentNode,domtab.activeClass);
		domtab.changeTab(document.getElementById(id),1);
		document.getElementById(id).focus();
		switch (id) { //block added sep 5 2011
			case 'ip':
				document.ipscofo.score.focus();
				document.ipscofo.score.select();
				break;
			case 'ua':
				if (document.uascofo != null) { //test for empty ua which has no score form - 20130520
					document.uascofo.score.focus();
					document.uascofo.score.select();
					}
				break;
			case 'ref':
				document.refscofo.score.focus();
				document.refscofo.score.select();
				break;
			case 'ruri':
				document.ruriscofo.score.focus();
				document.ruriscofo.score.select();
				break;
			case 'search':
				document.dmsearch.dminput.focus();
				document.dmsearch.dminput.select();
				break;
			}

		domtab.cancelClick(e);
	},

/* helper methods */
	getTarget:function(e){
		var target = window.event ? window.event.srcElement : e ? e.target : null;
		if (!target){return false;}
		if (target.nodeName.toLowerCase() != 'a'){target = target.parentNode;}
		return target;
	},
	cancelClick:function(e){
		if (window.event){
			window.event.cancelBubble = true;
			window.event.returnValue = false;
			return;
		}
		if (e){
			e.stopPropagation();
			e.preventDefault();
		}
	},
	addEvent: function(elm, evType, fn, useCapture){
		if (elm.addEventListener)
		{
			elm.addEventListener(evType, fn, useCapture);
			return true;
		} else if (elm.attachEvent) {
			var r = elm.attachEvent('on' + evType, fn);
			return r;
		} else {
			elm['on' + evType] = fn;
		}
	},
	cssjs:function(a,o,c1,c2){
		switch (a){
			case 'swap':
				o.className=!domtab.cssjs('check',o,c1)?o.className.replace(c2,c1):o.className.replace(c1,c2);
			break;
			case 'add':
				if(!domtab.cssjs('check',o,c1)){o.className+=o.className?' '+c1:c1;}
			break;
			case 'remove':
				var rep=o.className.match(' '+c1)?' '+c1:c1;
				o.className=o.className.replace(rep,'');
			break;
			case 'check':
				var found=false;
				var temparray=o.className.split(' ');
				for(var i=0;i<temparray.length;i++){
					if(temparray[i]==c1){found=true;}
				}
				return found;
			break;
		}
	}
}
domtab.addEvent(window, 'load', domtab.init, false);
