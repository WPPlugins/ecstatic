<?php
/****************************************************/
class chartParms {
/****************************************************/
function cParms() {
return array(
 "linewide" => "width:800,height:400,chartArea:{left:80,top:80,width:'74%',height:'70%'}"
,"halfwide" => "width:450,height:250,chartArea:{left:80,top:60,width:'60%',height:'60%'}"
,"piesmall" => "width:300,height:270,chartArea:{left:40,top:58,width:'90%',height:'60%'}"
,"piemedium" => "width:400,height:320,chartArea:{left:60,top:60,width:'80%',height:'64%'}"
,"piebig" => "width:800,height:360,chartArea:{left:100,top:80,width:'80%',height:'60%'}"
,"shortbar" => "width:800,height:325,chartArea:{left:70,top:20,width:'70%',height:'80%'}"
,"colstackedbig" => "width:800,height:450,isStacked:true,tooltip:{isHtml:true},chartArea:{left:70,top:40,width:'55%',height:'80%'}"
,"shortwide" => "width:800,height:360,interpolateNulls:true,chartArea:{left:64,top:64,width:'72%',height:'60%'}"
,"annotatedtimeline" => "displayAnnotations:false,allowRedraw:true,thickness:2,colors:['#ff9900','#dc3912','#3366cc']"
,"table" => "width:800,showRowNumber:true,page:'enable',pageSize:20,allowHtml:true"
);
} //cParms
} //class chartParms
/****************************************************/
class echart {
private $axis = "";
public $minhAxis = "";
public $maxhAxis = "";
public $columns = "";
public $rows = "";
public $options = "";
public $formats = "";
private $format_numbers_bool = false;
public $divs = array();
/****************************************************/
function options($title, $cParms) {
$this->options = "title:'{$title}',{$cParms}";
} //options
/****************************************************/
function turnColorsUpSideDown($colors) { //a trick for stacked column chart label colors - Google inverts things for some reason.
$Gcolors = array('#3366cc','#dc3912','#ff9900','#109618','#990099','#0099c6','#dd4477','#66aa00','#b82e2e','#316395','#994499','#22aa99','#aaaa11','#6633cc','#e67300','#8b0707','#651067','#329262','#5574a6','#3b3eac', '#b77322','#16d620','#b91383','#f4359e','#9c5935','#a9c413','#2a778d','#668d1c','#bea413','#0c5922','#743411'); //semi-official Google Chart colors
//$otherColors2 = "colors:['#0000FF','#FF0000','#00FF00','#FFFF00','#800080','#FF9933','#006699','#FF6699','#008000','#ADA990','#6600CC','#FFCC66','#00FFFF','#CC0033','#66FF99','#CCFF00','#CC99FF','#996600']"; //really garish, but good for the color blind, I guess
$c = 0;
$s = ",colors:[";
for ($x=$colors;$x>0;$x--) {
	if ($c++)
		$s .= ",";
	$s .= "'" . $Gcolors[($x-1) % sizeof($Gcolors)] . "'"; //when it runs out of colors, it starts over
	}
$s .= "]";
$this->options .= $s;
} //turnColorsUpSideDown
/****************************************************/
function e2Gdate($edate) { //take date from cumulative tables yyyy-mm-dd, other table datetimes have to be converted to yyyy-mm-dd before calling here
$y = substr($edate, 0, 4);
$m = substr($edate, 5, 2)-1; //javascript uses 0 for January
$d = substr($edate, 8, 2);
switch ($this->axis) {
	case "d":
		return "new Date($y,$m,$d)";
	case "m":
		return "new Date($y,$m)";
	case "y":
		return "new Date($y)";
	}
} //e2Gdate
/****************************************************/
function date_jumper($d) {
if ($this->axis == "s")
	$this->rows .= "'{$d}'";
else
	$this->rows .= $this->e2Gdate($d);
} //date_jumper
/****************************************************/
function addRowLineChart() { //variable arg list - a first
$arg_list = func_get_args();
$this->rows .= "[";
for($x=0;$x<sizeof($arg_list);$x++) {
	switch($x) {
		case 0:
			$this->date_jumper($arg_list[$x]);
			break;
		default:
			$this->rows .= "," . $arg_list[$x];
			break;
		}
	}
$this->rows .= "],";
} //addRowLineChart
/****************************************************/
function addRowTable() { //variable arg list
$arg_list = func_get_args();
$this->rows .= "[";
for($x=0;$x<sizeof($arg_list);$x++) {
	switch($x) {
		case 0:
			$this->rows .= "new Date(" . $arg_list[$x] * 1000 . ")"; //php timestamp in seconds, while javascript timestamps run in milliseconds
			break;
		default:
			if (is_numeric($arg_list[$x]))
				$this->rows .= "," . $arg_list[$x];
			else
				$this->rows .= ",'" . $arg_list[$x] . "'";
			break;
		}
	}
$this->rows .= "],";
} //addRowTable
/****************************************************/
function addRowStringLineChart($s) {
$s = rtrim($s, ",");
$rows = explode(",", $s);
foreach($rows as $r) {
	$cells = explode("::", $r);
	$this->rows .= "[";
	for($x=0;$x<sizeof($cells);$x++) {
		switch($x) {
			case 0:
				$this->date_jumper($cells[$x]);
				break;
			default:
				$this->rows .= "," . $cells[$x];
				break;
			}
		}
	$this->rows .= "],";
	}
} //addRowStringLineChart
/****************************************************/
function addSlicesPieChart($p) { //$p is an array
$total = 0;
foreach($p as $key => $num) {
	$this->rows .= "['" . $key . "'," . $num . "],";
	$total += $num;
	}
$this->options = str_replace("XXX", "(" . number_format($total) . ")", $this->options);
} //addSlicesPieChart
/****************************************************/
function addRowBarChart($key,$num) {
$this->rows .= "['" . $key . "'" . $num . "],";
} //addRowBarChart
/****************************************************/
function addRowColChart($d,$s) {
$this->rows .= "\n[";
$this->date_jumper($d);
$this->rows .= $s . "],";
} //addRowColChart
/****************************************************/
function addFormatNumber($col, $f=0) {
if ($this->format_numbers_bool == false) {
	$this->format_numbers_bool = true;
	$this->formats .= "var num_format{$this->id} = new google.visualization.NumberFormat({groupingSympbol:',', fractionDigits:{$f}});\n";
	}
$this->formats .= "num_format{$this->id}.format(data{$this->id},{$col});\n";
} //addFormatNumber
/****************************************************/
function addColumns($s) {
$col = 0;
$p = explode(",", $s);
foreach($p as $pp) {
	$r = explode("::", $pp);
	switch($r[0]) {
		case "d":
			$t="date";
			if ($r[1] == "Day")
				$pattern = "MMM d, yyyy";
			else
				$pattern = "MMM yyyy";
			$this->formats .= "var date_format{$this->id} = new google.visualization.DateFormat({pattern: \"{$pattern}\"});\ndate_format{$this->id}.format(data{$this->id},0);\n";
			break;
		case "dt":
			$t="datetime";
			$pattern = "MM/dd/yy hh:mm:ss"; //MMM d, yyyy
			$this->formats .= "var date_format{$this->id} = new google.visualization.DateFormat({pattern: \"{$pattern}\"});\ndate_format{$this->id}.format(data{$this->id},0);\n";
			break;
		case "n":
			$t="number";
			if ($r[1] == "Ratio") //a one off, so far
				$this->addFormatNumber($col, 3);
			else
				$this->addFormatNumber($col);
			break;
		case "s":
			if ($r[1] == "tooltip") {
				$this->columns .= "data{$this->id}.addColumn({type:'string',role:'tooltip',p:{'html':true}});\n";
				continue 2;
				}
			else
				$t = "string";
			break;
		}
	$this->columns .= "data{$this->id}.addColumn('{$t}','{$r[1]}');\n";
	$col++;
	}
} //addColumns
/****************************************************/
function addChart($control="") {
$this->rows = rtrim($this->rows, ",");
$this->rows .= "]);\n";
if ($this->minhAxis)
	$this->options .= ",hAxis:{textStyle:{color:'black',fontSize:10},format:'MMM d',viewWindowmode:'explicit',viewWindow:{min: " . $this->e2Gdate($this->minhAxis) . ",max: " . $this->e2Gdate($this->maxhAxis) . "}}";
if ($this->type == "AnnotatedTimeLine")
	$this->divs[] = "<div id='chart{$this->id}' style='float:left;width:640px;height:400px;margin:0px 60px'></div>\n"; //hard coded div size for Annotated Time Line
elseif ($this->type == "Table") {
	$this->divs[] = "<div id='dashboard' style='float:left;'>\n\t";
//	$this->divs[] = "<span id='control' style='float:left'></span><span id='control2'></span>\n\t";
	$this->divs[] = "<table style='width:800px;'><tr><td id='control'></td><td id='control2'></td><td id='control3'></td></tr></table>\n\t";
	$this->divs[] = "<div id='chart{$this->id}'></div>\n\t";
	$this->divs[] = "<div id='groupRef' style='float:left;width:215px;margin:20px 0;'></div>\n\t";
	$this->divs[] = "<div id='groupPage' style='float:left;width:350px;margin:20px 0 20px 10px;'></div>\n\t";
	$this->divs[] = "<div id='groupDomain' style='float:left;width:215px;margin:20px 0 20px 10px;'></div>\n";
//	$this->divs[] = "<div id='drivepie'></div>\n";
	$this->divs[] = "</div>\n";
	}
else
	$this->divs[] = "<div id='chart{$this->id}' style='float:left;'></div>\n";

return $this;
} //addChart
/****************************************************/
function addDiv($id, $s, $style="") {
$this->divs[] = "<div id='{$id}' style='float:left;width:100%;margin-left:20px;{$style}'>{$s}</div><div style='clear:both;'></div><br />\n";
} //addDiv
/****************************************************/
function __construct($id, $axis, $title, $column_fields, $type, $cParms) {
$this->id = $id;
$this->axis = $axis; //d=daily m=monthly
$this->type = $type;
$this->rows = "data{$id}.addRows([";
$this->addColumns($column_fields);
$this->options($title, $cParms);
} //constructor
} //class echart

/*
echo "<pre>";
var_dump($);
echo "</pre>";
*/

/****************************************************/
class cSearchEngines {
private $ecstatic;
public $switch = false;
public $header = "";
public $blurb = "";
private $cParms = array();
/****************************************************/
function buildChart() {
global $wpdb;
$compare = array("<", ">=");
$sub1 = array("", " for <span style='font-style:italic;color:red;'>Killed</span> Hits");
$charts = $seHost = $ba = $pa = $pr = $pab = $pags = array();
$nchart = 1;

$h = $this->ecstatic->hits_table;
$u = $this->ecstatic->iurr_tables["ua"];
$r = $this->ecstatic->iurr_tables["ruri"];
$qs = "SELECT $h.datetime, $u.ua, $u.browser, $r.id, $r.ruri FROM $h, $u, $r WHERE $h.ua=$u.id AND $u.aux=2 AND $h.ruri=$r.id AND $h.score {$compare[$this->switch]} 10 ORDER BY $h.datetime";
$rq = $wpdb->get_results($qs);
$totz = 0;
foreach($rq as $r) {
	$ed = date("Y-m-d", $r->datetime);
	if (strpos($r->ua, "WordPress") !== false AND strpos($r->ruri, "doing_wp_cron") !== false) { //in case the WordPress Cron monster is marked as a bot, which it should be
		$seHost["WordPressCron"]++;
		$ba[$ed]["WordPressCron"]++;
		}
	else {
		$seHost[$r->browser]++;
		$ba[$ed][$r->browser]++;
		}
	$pa[$r->id]++;
	$pr[$r->id] = $r->ruri;
	$totz++;
	}
unset($rq);

arsort($seHost);
$theRest = 0;
foreach($seHost as $host => $num) {
	if ($num < $totz * 0.0075) {
		$theRest += $num;
		unset($seHost[$host]);
		}
	}
if ($theRest)
	$seHost["Others"] = $theRest;

$chart1 = new echart($nchart++,"d","Search Engines XXX","s::Search Engine,n::Number","PieChart",$this->cParms["piebig"]);
$chart1->addSlicesPieChart($seHost);
$charts[] = $chart1->addChart();

$m = 48; //days to show in stacked col chart
$then = $this->ecstatic->zero_am_today - (60*60*24*$m); //$m days ago
foreach($ba as $b => $r) {
	$drt = strtotime($b);
	if ($drt >= $then) {
		foreach($r as $browz => $num)
			$nc[$browz] += $num; //build $colstring parms
		}
	}
if (is_array($nc)) { //test for no data for new plugin installations or very un-busy websites
$colors = 0; //count for building custom color key
$colstring = "d::Day";
asort($nc);
foreach($nc as $key => $val) {
	$colstring .= ",n::{$key}({$val}),s::tooltip";
	$colors++;
	}
$chart2 = new echart($nchart++,"d","Last {$m} Days",$colstring,"ColumnChart", $this->cParms["colstackedbig"]);
$chart2->turnColorsUpSideDown($colors); //a peculularity of stacked column charts
for ($i=0;$i<$m+1;$i++) {
	$when = date("Y-m-d", $then);
	$nice_when = date("M d, Y", $then);
	$rowstring = "";
	if (isset($ba[$when])) {
		foreach($nc as $host => $num) {
			if ($host == "Others")
				continue;
			if ($ba[$when][$host])
				$rowstring .= ",1,'<div style=\'padding:5px;\'><b>{$nice_when}</b><br />{$host} -- Hits: <b>{$ba[$when][$host]}</b></div>'";
			else
				$rowstring .= ",null,null";
			}
		$chart2->addRowColChart($when, $rowstring);
		}
	$then += (60*60*24);
	}
$chart2->maxhAxis = date("Y-m-d", $then);
$chart2->minhAxis = date("Y-m-d", $this->ecstatic->datetime - (60*60*24*($m+1))); //shifts the chart - an unshifted chart puts the first column partly outside the chart
$charts[] = $chart2->addChart();
$chart2->addDiv("c2blurb", "<p style='font-size:x-small;padding-right:120px;'>The chart above is a little different.&nbsp; The major search engines so dwarf the lesser ones in their relentless crawling that any usual scale renders the chart almost useless.&nbsp; To compensate, each SE on each day is assigned a column height of one.&nbsp; Mousing over the colored bars shows the number of hits on each day from the individual entities, while the numbers at the right of the chart legend are the totals for the {$m} day window.&nbsp; If you allow a lot of search engines to crawl your site, a taller window, or some limiting factor, might be in order.&nbsp; Let me know and I'll try to work it in to a future release.</p>");
} //if

arsort($pa);
$limit = $bottom = 0;
$chart3 = new echart($nchart++,"d","Top Pages Indexed XXX","s::Page,n::Number","PieChart",$this->cParms["piemedium"]);
foreach($pa as $key => $num) {
	if ($num < $totz * 0.005) {
		$bottom += $num;
		if ($limit++ > 49)
			continue;
		$pab[$key] = $num; //used in next chart
		continue;
		}
	$ruri_link = ecstatic_format_requri($pr[$key]);
	$ruri_link = preg_replace("/[\"\']|&middot;/", "", $ruri_link);
	$pags[$ruri_link] += $num;
	}
$pags["Others"] = $bottom;
$chart3->addSlicesPieChart($pags);
$charts[] = $chart3->addChart();

if ($bottom) {
	$pags = array();
	$title = "Top " . sizeof($pab) . " \'Others\' Indexed XXX";
	$chart4 = new echart($nchart++,"d",$title,"s::Page,n::Number","PieChart",$this->cParms["piemedium"]);
	foreach($pab as $key => $num) {
		$ruri_link = ecstatic_format_requri($pr[$key]);
		$ruri_link = preg_replace("/[\"\']|&middot;/", "", $ruri_link);
		$pags[$ruri_link] += $num;
		}
	$chart4->addSlicesPieChart($pags);
	$charts[] = $chart4->addChart();
	}
$this->header = "<div id='chartheader' style='float:left;width:600px;'><h1>Search Engines{$sub1[$this->switch]}</h1></div>\n";
//$this->blurb = "<p>If the top chart shows a slice for the \"Unknown\" Search Engine, or the second chart shows a bunch of Spider/Bots with names like FireFox 12.0 and Mozilla 5.0, know that a recent change to the User Agent Parser routines should slowly better identify those items, as you use the program.&nbsp; Or you could just click the button below to run the Fix Browsers routine.<br /><br /><form id='fixBrowsers' method='post' action=''><input type='hidden' name='ecstatit' value='fixBrowsers' /><input type='submit' name='fixBrowsers' value='Know the Unknown' /></form><br /></p>";
$this->blurb = "<p>Hey!&nbsp; Search Engines.</p><p>A single database draw uses the Hits, User Agents, and Requested URI tables for this page.</p>";
return $charts;
}
/****************************************************/
function __construct($ecstatic) {
$this->ecstatic = $ecstatic;
$chartParms = new chartParms(); //table dimentions and misc options
$this->cParms = $chartParms->cParms();
} //constructor
} //class cSearchEngines

/****************************************************/
class cReferrers {
private $ecstatic;
public $switch = false;
public $mode = false;
public $header = "";
public $blurb = "";
private $cParms = array();
private $sez = array();
public $dashboard = true;
/****************************************************/
function parseRef($ref) {
$refparts = parse_url($ref);
if (!isset($refparts["scheme"]) OR !isset($refparts["host"]))
//	return $ref;
	$name = $ref;
//return $refparts["scheme"] . "://" . $refparts["host"];
else
	$name = str_replace("www.", "", $refparts["host"]);

foreach($this->sez as $se) {
	if (strpos($se->token, "*"))
		$tk = "~" . str_replace("*", ".+", $se->token) . "~"; //create regular expression search term
	else
		$tk = "~" . $se->token . "~";
//	if (preg_match($tk, $ref) AND strpos($ref, $se->qsig) !== false) {
	if (preg_match($tk, $ref)) {
		$name = $se->name;
//		break;
		}
	}
return $name;
} //parseRef
/****************************************************/
function buildChart() {
global $wpdb;
$aux = array(1, 2);
$compare = array("<", ">=");
$sub1 = array("", " for <span style='font-style:italic;color:red;'>Killed</span> Hits");
$sub2 = array("Non-", "");
$charts = $refHost = $ba = $pa = $pr = $pab = $pags = array();
$nchart = 1; //chart control number

$se_table = $this->ecstatic->make_table("aux_se");
$this->sez = $wpdb->get_results("SELECT name, token, qsig FROM $se_table"); //load search engine signature tokens and formal name

$totz=0;
$h = $this->ecstatic->hits_table;
$ip = $this->ecstatic->iurr_tables["ip"];
$r = $this->ecstatic->iurr_tables["ref"];
$u = $this->ecstatic->iurr_tables["ruri"];
$qs = "SELECT $h.datetime, $r.ref, $u.id, $u.ruri, $ip.domain FROM $r, $h, $u, $ip WHERE $r.aux={$aux[$this->mode]} AND $h.ref=$r.id AND $h.ruri=$u.id AND $h.ip=$ip.id AND $h.score {$compare[$this->switch]} 10 GROUP BY $h.datetime";
$rq = $wpdb->get_results($qs);
foreach($rq as $r) {
	$refDom = $this->parseRef($r->ref); //parse the referrer
	$refHost[$refDom]++;
	$ed = date("Y-m-d", $r->datetime);
	$ba[$ed][$refDom]++;
	$pa[$r->id]++;
	$pr[$r->id] = $r->ruri;
	if (!$totz++) //catch a one time hook
		$first_dat = date("M d, Y", $r->datetime);
	}

arsort($refHost);
$theRest = 0;
foreach($refHost as $host => $num) {
	if ($num < $totz * 0.0075) {
		$theRest += $num;
		unset($refHost[$host]);
		}
	}
if ($theRest)
	$refHost["Others"] = $theRest;
$chart1 = new echart($nchart++,"d","Referrers XXX","s::Referrer,n::Number","PieChart",$this->cParms["piebig"]);
$chart1->addSlicesPieChart($refHost);
$charts[] = $chart1->addChart();

arsort($pa);
$limit = $bottom = 0;
$chart3 = new echart($nchart++,"d","Top Pages Referred To XXX","s::Page,n::Number","PieChart",$this->cParms["piemedium"]);
foreach($pa as $key => $num) {
	if ($num < $totz * 0.01) {
		$bottom += $num;
		if ($limit++ > 49)
			continue;
		$pab[$key] = $num; //used in next chart
		continue;
		}
	$ruri_link = ecstatic_format_requri($pr[$key]);
	$ruri_link = preg_replace("/[\"\']|&middot;/", "", $ruri_link);
	$pags[$ruri_link] = $num;
	}
$pags["Others"] = $bottom;
$chart3->addSlicesPieChart($pags);
$charts[] = $chart3->addChart();

if ($bottom) {
	$pags = array();
	$title = "Top " . sizeof($pab) . " \'Others\' Pages Referred To XXX";
	$chart4 = new echart($nchart++,"d",$title,"s::Page,n::Number","PieChart",$this->cParms["piemedium"]);
	foreach($pab as $key => $num) {
		$ruri_link = ecstatic_format_requri($pr[$key]);
		$ruri_link = preg_replace("/[\"\']|&middot;/", "", $ruri_link);
		$pags[$ruri_link] = $num;
		}
	$chart4->addSlicesPieChart($pags);
	$charts[] = $chart4->addChart();
	}

$m = 60; //days to show in stacked col chart

$y = $chart3;
if ($bottom)
	$y = $chart4;

$y->addDiv("refBlurb", "<h3>The following Charts feature data from the last {$m} days</h3>");

$then = $this->ecstatic->zero_am_today - (60*60*24*$m); //$m days ago
foreach($ba as $b => $r) {
	$drt = strtotime($b);
	if ($drt >= $then) {
		foreach($r as $refz => $num)
			$nc[$refz] += $num; //build $colstring parms
		}
	}
if (is_array($nc)) { //test for no data for new plugin installations or very un-busy websites
$colors = 0; //count for building custom color key
$colstring = "d::Day";
asort($nc);
foreach($nc as $key => $val) {
	$colstring .= ",n::{$key}({$val})";
	$colors++;
	}
$chart2 = new echart($nchart++,"d","Daily Referrers",$colstring,"ColumnChart", $this->cParms["colstackedbig"]);
$chart2->turnColorsUpSideDown($colors); //a peculularity of stacked column charts
for ($i=0;$i<$m+1;$i++) {
	$when = date("Y-m-d", $then);
	$rowstring = "";
	if (isset($ba[$when])) {
		foreach($nc as $host => $num) {
			if ($host == "Others")
				continue;
			if ($ba[$when][$host])
				$rowstring .= ",{$ba[$when][$host]}";
			else
				$rowstring .= ",null";
			}
		$chart2->addRowColChart($when, $rowstring);
		}
	$then += (60*60*24);
	}
$chart2->maxhAxis = date("Y-m-d", $then);
$chart2->minhAxis = date("Y-m-d", $this->ecstatic->datetime - (60*60*24*($m+1))); //shifts the chart - an unshifted chart puts the first column partly outside the chart
$charts[] = $chart2->addChart();
} //if

$host_error = array("1(FORMERR)"=>"Format Error", "2(SERVFAIL)"=>"(ServerFail)", "3(NXDOMAIN)"=>"(Non-existent)", "4(NOTIMP)"=>"Not Implemented", "5(REFUSED)"=>"Query Refused", "6(YXDOMAIN)"=>"Name Exists when it should not", "7(YXRRSET)"=>"RR Set Exists when it should not", "8(NXRRSET)"=>"RR Set that should exist does not", "9(NOTAUTH)"=>"Server Not Authoritative for zone", "10(NOTZONE)"=>"Name not contained in zone", "TO"=>"(timed out)", "timed out"=>"(timed out)", "(empty)" => "(Empty)", "hosterr" => "(Host error)", "NA"=>"(NotAvailable)"); //translate cryptic error codes
$chart5 = new echart($nchart++,"d","Recent Referrers","dt::Date/Time,s::Referrer,s::page,s::Page,s::Domain","Table",$this->cParms["table"]); //dt=datetime bookmark
$ago = $this->ecstatic->zero_am_today - (60*60*24*$m);
foreach($rq as $r) {
	if ($r->datetime > $ago) {
		$refDom = $this->parseRef($r->ref); //parse the referrer
		$ruri = ecstatic_format_requri($r->ruri);
		$ruri = str_replace("'", "\'", $ruri);
		$ruri = str_replace("&middot;", "", $ruri);
		$ruri_link = ecstatic_makelink($r->ruri);
		$ruri_link = str_replace("'", "\'", $ruri_link);
		$ruri_link = str_replace("&middot;", "", $ruri_link);
		if (isset($host_error[$r->domain]))
			$r->domain = $host_error[$r->domain];
		$chart5->AddRowTable($r->datetime,$refDom,$ruri,$ruri_link,$r->domain);
		}
	}
$chart5->addDiv("tableBlurb", "<h3>Recent Referrers.&nbsp; Columns are sortable, and Select boxes provide aggregated stats.&nbsp; (Very Beta)</h3>");
$charts[] = $chart5->addChart();

$this->header = "<div id='chartheader' style='float:left;width:600px;'><h1>{$sub2[$this->mode]}Search Engine Referrers{$sub1[$this->switch]}</h1><h3>The Pie Charts feature data since {$first_dat}</h3></div>\n";
$this->blurb = "<p>Two database queries for this page.&nbsp; One to load Search Engine Referrer tokens and names, and another query combining data from the Hits, Referrers, Requested URI, and IP tables.</p><p>The Table charts, at the bottom, are all very Beta as I explore functionality and utility.&nbsp; The Google API for Table charts is a toss up; the features are not all-encompassing, the documentation is lacking, and the Big Internets aren't quite filling in the gaps like they usually do.&nbsp; Still and all it's pretty freaking sweet.</p>";
return $charts;
//$this->ostart = max(array($ecstatic->options["purgeolderthan"], $ecstatic->options["purgebotolderthan"], $ecstatic->options["purgerssolderthan"]));
}
/****************************************************/
function __construct($ecstatic) {
$this->ecstatic = $ecstatic;
$chartParms = new chartParms(); //table dimentions and misc options
$this->cParms = $chartParms->cParms();
} //constructor
} //class cReferrers

/****************************************************/
class cBrowsers {
private $ecstatic;
public $switch = false;
public $header = "";
public $blurb = "";
private $cParms = array();
/****************************************************/
function buildChart() {
global $wpdb;
$monmajbrowser = $majbrowser = $minorbrowser = $majarray = $colarray = array();
$compare = array("<", ">=");
$sub1 = array("", " for <span style='font-style:italic;color:red;'>Killed</span> Hits");
$sub2 = array("Scores less than 10 (ie. the Visitors weren't Blocked)", "Scores >= 10 (ie. the Visitors were Blocked)");

$h = $this->ecstatic->hits_table;
$b = $this->ecstatic->iurr_tables["ua"];
$q = "SELECT $h.ua, $h.datetime, $b.browser FROM $h, $b WHERE $h.score {$compare[$this->switch]} 10 AND $h.ua=$b.id AND $b.aux=1 ORDER BY $h.datetime ASC";
$qq = $wpdb->get_results($q);
foreach($qq as $qqq) {
	$mb = $minorb = "";
	$t = explode(" ", $qqq->browser); //split browser from version
	for ($i=0; $i<sizeof($t);$i++) {
		if (!$i AND $t[1] == "Safari") //fix for not keeping superfluous ids in ua_parser up to date
			continue;
		if ($i < sizeof($t) - 1 OR sizeof($t) == 1) //last element is the version number
			$mb .= $t[$i] . " ";
		else
			$minorb .= $t[$i] . " ";
		}
	$mb = trim($mb);
	$majbrowser[$mb]++;
	$minorb = trim($minorb);
	$minorbrowser[$mb][$minorb]++;
	$byday = date("Y-m", $qqq->datetime); //chart2
	$monmajbrowser[$byday][$mb]++; //chart2
	}
arsort($majbrowser);
$z = 0;
$c1string = "";
$majstring = "d::Month"; //d=datetime s=string
$majstring1 = "s::Browsers"; //d=datetime s=string
foreach($majbrowser as $br => $num) {
	if ($num > 0.005 * sizeof($qq)) {
		$c1string .= ",{$num}";
		$majarray[] = $br;
		$majstring .= ",n::" . $br;//*
		$majstring1 .= ",n::" . $br;//*
		}
	else
		$z += $num;
	}
if ($z) {
	$majstring1 .= ",n::MISC";
	$c1string .= ",{$z}";
	}
$chart1 = new echart(2,"s","Consolidated Rankings", $majstring1, "BarChart", $this->cParms["shortbar"]); //chart1 is really chart #2
$chart1->addRowBarChart("", $c1string);

$chart2 = new echart(1,"m","Browsers by Month", $majstring, "LineChart", $this->cParms["shortwide"]); //chart2 is really chart #1
$tmp = "";
foreach($monmajbrowser as $day => $dmb) {
	$tmp .= $day;
	foreach($majarray as $ms) {
		$x = 0;
		foreach($dmb as $bname => $num) {
			if ($bname == $ms) {
				$tmp .= "::" . $num;
				$x++;
				break;
				}
			}
		if (!$x)
			$tmp .= "::null";
		}
	$tmp .= ",";
	}
$chart2->addRowStringLineChart($tmp);
$charts[] = $chart2->addChart();
$charts[] = $chart1->addChart();

$chart1->addDiv("chartmiddle", "<h2>Version Breakdown</h2>");

$z = 3; //third chart and beyond
foreach($majarray as $mb) { //piecharts for browser versions
	if ($mb == "Unknown")
		continue;
	$tq = array();
	$chart = new echart($z++,"d",$mb . " XXX","s::Version,n::Number","PieChart",$this->cParms["piesmall"]);
	uksort($minorbrowser[$mb], "strnatcmp");
	$minorbrowser[$mb] = array_reverse($minorbrowser[$mb]);
	$misc = 0;
	foreach($minorbrowser[$mb] as $br => $num) {
		if ($num > $majbrowser[$mb] * 0.0075)
			$tq[$br] = $num;
		else
			$misc += $num;
		}
	if ($misc)
		$tq["Misc"] = $misc;
	$chart->addSlicesPieChart($tq); //automatically adds totals to title
	$charts[] = $chart->addChart(); //trims trailing comma, closes the row with "]);\n", adds any last options, adds a <div> to the div queue, and adds chart to chart queue
	} //foreach

$this->header = "<div style='float:left;width:600px;'><h1>Browser Charts{$sub1[$this->switch]}</h1></div>\n";
$this->blurb = "<p>The data for this page is drawn in a single mySQL call from the ecSTATic Hits table for all hits with {$sub2[$this->switch]}, matched against the User Agents table for Browsers not marked as Spider/Bots.</p>";
return $charts;
}
/****************************************************/
function __construct($ecstatic) {
$this->ecstatic = $ecstatic;
$chartParms = new chartParms(); //table dimentions and misc options
$this->cParms = $chartParms->cParms();
} //constructor
} //class cBrowsers

/****************************************************/
class cPages {
private $ecstatic;
public $header = "";
public $blurb = "";
private $cParms = array();
/****************************************************/
function buildChart() {
global $wpdb;
$nchart = 1;
$mty = date("Y", $this->ecstatic->datetime);
$mq = $tq = $yq = $ty = array();
$chart1 = new echart($nchart++,"d","Daily Page Views","d::Date,n::Spider/Bots,n::Feeds,n::Visitors","AnnotatedTimeLine",$this->cParms["annotatedtimeline"]); //chart #, axis type="d" or "m", title, columns, chart type, chart parameters
$chart2 = new echart($nchart++,"s","Yearly Page Views","s::Year,n::Pages,n::Feeds,n::Spider/Bots","ColumnChart", $this->cParms["halfwide"]);
$chart3 = new echart($nchart++,"d","Total Page Views XXX","s::Type,n::Number","PieChart",$this->cParms["piesmall"]);
$chart4 = new echart($nchart++,"m","Monthly Page Views", "d::Month,n::Pages,n::Feeds,n::Spider/Bots", "LineChart",$this->cParms["linewide"]);
$chart5 = new echart($nchart++,"m","Monthly Page Views and Visitors","d::Month,n::Pages,n::Visitors","LineChart", $this->cParms["halfwide"]);
$chart7 = new echart($nchart++,"d","Pages This Year XXX", "s::Type,n::Number", "PieChart", $this->cParms["piesmall"]);
$chart6 = new echart($nchart++,"m","Page Views Per Visitor","d::Month,n::Ratio","LineChart",$this->cParms["halfwide"]);
$cq = $wpdb->get_results("SELECT day, regi, regp, feedp, botp FROM {$this->ecstatic->cumulative_table} ORDER BY day ASC");
foreach($cq as $c) {
	$chart1->addRowLineChart($c->day, $c->botp, $c->feedp, $c->regp); //reversed to override peculularities of the annotated time line chart pertaining to the small range selector graph
	$m = substr($c->day, 0, 7);
	$mq[$m]["v"] += $c->regi;
	$mq[$m]["p"] += $c->regp;
	$mq[$m]["r"] += $c->feedp;
	$mq[$m]["s"] += $c->botp;
	$y = substr($c->day, 0, 4);
	$yq[$y]["v"] += $c->regp;
	$yq[$y]["r"] += $c->feedp;
	$yq[$y]["s"] += $c->botp;
	$tq["Visitors"] += $c->regp;
	$tq["Feeds"] += $c->feedp;
	$tq["Spider/Bots"] += $c->botp;
	if ($y == $mty) { //current year visitors
		$ty["Visitors"] += $c->regp;
		$ty["Feeds"] += $c->feedp;
		$ty["Spider/Bots"] += $c->botp;
		}
	}
foreach($yq as $d => $c)
	$chart2->addRowLineChart($d, $c["v"], $c["r"], $c["s"]);
$chart3->addSlicesPieChart($tq);
foreach($mq as $d => $c)
	$chart4->addRowLineChart($d, $c["p"], $c["r"], $c["s"]);
foreach($mq as $d => $c) {
	$chart5->addRowLineChart($d, $c["p"], $c["v"]);
	$r = $c["p"] / $c["v"];
	$chart6->addRowLineChart($d, $r);
	}
$chart7->addSlicesPieChart($ty);
$charts[] = $chart1->addChart(); //trims trailing comma, closes the row with "]);\n", adds any last options, adds a <div> to the div queue, and adds chart to chart queue
$charts[] = $chart2->addChart();
$charts[] = $chart3->addChart();
$charts[] = $chart4->addChart();
$charts[] = $chart5->addChart();
$charts[] = $chart7->addChart();
$charts[] = $chart6->addChart();
$this->header = "<div id='chartheader' style='float:left;width:600px;'><h1>Pages</h1></div>\n";
$this->blurb = "<p>NEW interactive chart with Range Slider and Zoom Keys!&nbsp; The Google Api uses Flash to render the \"AnnotatedTimeLine\" chart.&nbsp; The short graph just above the slider is a log scale view of your Visitors.</p><p>One draw from Cumulative table for the page.</p>\n";
return $charts;
}
/****************************************************/
function __construct($ecstatic) {
$this->ecstatic = $ecstatic;
$chartParms = new chartParms(); //table dimentions and misc options
$this->cParms = $chartParms->cParms();
} //constructor
} //class cPages

/****************************************************/
class cVisitors {
private $ecstatic;
public $header = "";
public $blurb = "";
private $cParms = array();
/****************************************************/
function buildChart() {
global $wpdb;
$nchart = 1;
$mty = date("Y", $this->ecstatic->datetime);
$mq = $tq = $xq = $yq = $ty = array();
$chart1 = new echart($nchart++,"d","Daily Visitors", "d::Day,n::Spider/Bots,n::Feeds,n::Visitors", "AnnotatedTimeLine", $this->cParms["annotatedtimeline"]); //chart #, axis type="d" or "m", title, columns, chart type, chart parameters
$chart2 = new echart($nchart++,"s","Yearly Visitors","s::Year,n::Visitors,n::Feeds,n::Spider/Bots","ColumnChart", $this->cParms["halfwide"]);
$chart3 = new echart($nchart++,"d","Total Visitors XXX","s::Type,n::Number","PieChart",$this->cParms["piesmall"]);
$chart4 = new echart($nchart++,"m","Monthly Visitors", "d::Month,n::Visitors,n::Feeds,n::Spider/Bots","LineChart",$this->cParms["linewide"]);
$chart5 = new echart($nchart++,"m","Average Visitors per Day by Month","d::Month,n::Visitors,n::Feeds,n::Spider/Bots","LineChart", $this->cParms["halfwide"]);
$chart6 = new echart($nchart++,"d","Visits This Year XXX", "s::Type,n::Number", "PieChart", $this->cParms["piesmall"]);
$cq = $wpdb->get_results("SELECT day, regi, feedi, boti FROM {$this->ecstatic->cumulative_table} ORDER BY day");
foreach($cq as $c) {
	$chart1->addRowLineChart($c->day, $c->boti, $c->feedi, $c->regi); //order reversed compared to other charts because the range selector graph reflects the last column entered.  Requires reversing of colors, to match other charts.
	$m = substr($c->day, 0, 7);
	$mq[$m]["v"] += $c->regi;
	$mq[$m]["r"] += $c->feedi;
	$mq[$m]["s"] += $c->boti;
	$y = substr($c->day, 0, 4);
	$yq[$y]["v"] += $c->regi;
	$yq[$y]["r"] += $c->feedi;
	$yq[$y]["s"] += $c->boti;
	$xq[$m]["v"]++;
	$xq[$m]["r"]++;
	$xq[$m]["s"]++;
	$tq["Visitors"] += $c->regi;
	$tq["Feeds"] += $c->feedi;
	$tq["Spider/Bots"] += $c->boti;
	if ($y == $mty) { //current year visitors
		$ty["Visitors"] += $c->regi;
		$ty["Feeds"] += $c->feedi;
		$ty["Spider/Bots"] += $c->boti;
		}
	}
foreach($yq as $d => $c)
	$chart2->addRowLineChart($d, $c["v"], $c["r"], $c["s"]);
$chart3->addSlicesPieChart($tq);
foreach($mq as $d => $c)
	$chart4->addRowLineChart($d, $c["v"], $c["r"], $c["s"]);
foreach($mq as $d => $c) {
	$c["v"] /= $xq[$d]["v"];
	$c["r"] /= $xq[$d]["r"];
	$c["s"] /= $xq[$d]["s"];
	$chart5->addRowLineChart($d, $c["v"], $c["r"], $c["s"]);
	}
$chart6->addSlicesPieChart($ty);
$charts[] = $chart1->addChart();
$charts[] = $chart2->addChart();
$charts[] = $chart3->addChart();
$charts[] = $chart4->addChart();
$charts[] = $chart5->addChart();
$charts[] = $chart6->addChart();
$this->header = "<div id='chartheader' style='float:left;width:600px;'><h1>Visitors</h1></div>\n";
$this->blurb = "<p>NEW interactive chart with Range Slider and Zoom Keys!&nbsp; The Google Api uses Flash to render the \"AnnotatedTimeLine\" chart.&nbsp; The short graph just above the slider is a log scale view of your Visitors.</p><p>One draw from Cumulative table for the page.</p>\n";
return $charts;
}
/****************************************************/
function __construct($ecstatic) {
$this->ecstatic = $ecstatic;
$chartParms = new chartParms(); //table dimentions and misc options
$this->cParms = $chartParms->cParms();
} //constructor
} //class cVisitors

/****************************************************/
class ecstatic_charter extends ecstatic {
public $blurb_boilerplate = "<br /><hr /><br /><p>For some charts, the extent of the display depends on how long data has been accumulating, AND your ecSTATic Purge settings.&nbsp; Longer Purge values leave more entries to chart, but also mean more data to plow through in all areas of the program.</p><p>Charts are still in development, and the underlying code and presentation will undoubtedly change.</p><p>Use the <a href='http://wordpress.org/support/plugin/ecstatic' title='WordPress ecSTATic Support Forum' target='_blank'>WordPress ecSTATic Support Forum</a> to request new charts, additional functionality, complain, etc.</p><p>Google Promises:&nbsp; No data leaves your computer!</p>";
/****************************************************/
function make_chart() {
$chart = "DVT";
$chartName = array("DVT" => "cVisitors", "DPT" => "cPages", "BROWS" => "cBrowsers", "KBROWS" => "cBrowsers", "REFS" => "cReferrers", "KREFS" => "cReferrers", "SEREFS" => "cReferrers", "KSEREFS" => "cReferrers", "SE" => "cSearchEngines", "KSE" => "cSearchEngines");
if (isset($_POST["chart1"]) AND isset($chartName[$_POST["chart1"]]))
	$chart = $_POST["chart1"];
$checked[$chart] = "checked";

echo <<<XXX
<div id="chartpage">
<div id="chartform">
<form method="post" action=""><br /><br /><br /><br />
<input type="radio" id="DVT" name="chart1" value="DVT" onClick="this.form.submit()" {$checked["DVT"]}>&nbsp; <label for="DVT">Visitors</label><br />
<input type="radio" id="DPT" name="chart1" value="DPT" onClick="this.form.submit()" {$checked["DPT"]}>&nbsp; <label for="DPT">Pages</label><br />
<input type="radio" id="SEREFS" name="chart1" value="SEREFS" onClick="this.form.submit()" {$checked["SEREFS"]}>&nbsp; <label for="SEREFS">SE Referrers</label><br />
<input type="radio" id="REFS" name="chart1" value="REFS" onClick="this.form.submit()" {$checked["REFS"]}>&nbsp; <label for="REFS">Non-SE Referrers</label><br />
<input type="radio" id="SE" name="chart1" value="SE" onClick="this.form.submit()" {$checked["SE"]}>&nbsp; <label for="SE">Search Engines</label><br />
<input type="radio" id="BROWS" name="chart1" value="BROWS" onClick="this.form.submit()" {$checked["BROWS"]}>&nbsp; <label for="BROWS">Browsers</label><br />
<input type="radio" id="KSEREFS" name="chart1" value="KSEREFS" onClick="this.form.submit()" {$checked["KSEREFS"]}>&nbsp; <label for="KSEREFS">Killed SE Referrers</label><br />
<input type="radio" id="KREFS" name="chart1" value="KREFS" onClick="this.form.submit()" {$checked["KREFS"]}>&nbsp; <label for="KREFS">Killed Non-SE Referrers</label><br />
<input type="radio" id="KSE" name="chart1" value="KSE" onClick="this.form.submit()" {$checked["KSE"]}>&nbsp; <label for="KSE">Killed Search Engines</label><br />
<input type="radio" id="KBROWS" name="chart1" value="KBROWS" onClick="this.form.submit()" {$checked["KBROWS"]}>&nbsp; <label for="KBROWS">Killed Browsers</label><br />
</form>\n
XXX;

$nchart = new $chartName[$chart]($this);

if ($chart == "KBROWS" OR $chart == "KREFS" OR $chart == "KSEREFS" OR $chart == "KSE")
	$nchart->switch = true; //use same routine for two charts
if ($chart == "SEREFS" OR $chart == "KSEREFS")
	$nchart->mode = true; //use same other routine for two charts

$mchart = $nchart->buildChart(); //returns all charts for the page

echo "<div id='chartblurb'>" . $nchart->blurb . $this->blurb_boilerplate . "</div>\n";
echo "</div><!--chartform-->\n";

echo "<div id='allcharts' style='float:left;width:80%;'>\n";
if ($nchart->header)
	echo $nchart->header;
foreach($mchart as $chart)
	foreach($chart->divs as $div)
		echo $div;
echo "</div><!--allcharts-->\n";
echo "</div><!--chartpage-->\n\n";

echo <<<XXX
<script type='text/javascript' src='https://www.google.com/jsapi'></script>
<script type='text/javascript'>\n
google.load('visualization', '1');
XXX;

if (isset($nchart->dashboard))
	echo "\ngoogle.load('visualization', '1', {'packages':['controls']});\n";

echo <<<XXX
google.setOnLoadCallback(drawCharts);\n
function drawCharts(){\n
XXX;
$x = 1;
foreach($mchart as $chart) {
if ($chart->type == "Table")
	break;

echo <<<XXX
var data{$x} = new google.visualization.DataTable();
{$chart->columns}
{$chart->rows}
{$chart->formats}
var chart{$x} = new google.visualization.ChartWrapper({
	dataTable: data{$x},
	containerId: 'chart{$x}',
	chartType: '{$chart->type}',
	options: {{$chart->options}},
	view: null
	});
chart{$x}.draw();\n\n
XXX;

$x++;
} //foreach
echo "} <!--drawCharts-->\n\n";

if ($mchart[$x-1]->type == "Table") {
echo <<<XXX
google.setOnLoadCallback(drawDashboard);

function drawDashboard() {
var data{$x} = new google.visualization.DataTable();
{$mchart[$x-1]->columns}
{$mchart[$x-1]->rows}
{$mchart[$x-1]->formats}
var recentView = new google.visualization.ChartWrapper({
	chartType: 'Table',
	containerId: 'chart{$x}',
	options: {sortColumn: 0, sortAscending: false, {$mchart[$x-1]->options}},
	view: {columns:[0,1,3,4]}
	});
var refPicker = new google.visualization.ControlWrapper({
controlType: 'CategoryFilter',
containerId: 'control',
options: {
	filterColumnLabel: 'Referrer',
	ui: {
		caption: 'Choose...',
		label: 'Referred By',
		labelStacking: 'vertical',
		allowTyping: false,
		allowMultiple: false
		}
	}
});
var pagePicker = new google.visualization.ControlWrapper({
controlType: 'CategoryFilter',
containerId: 'control2',
options: {
	filterColumnLabel:'page',
	ui: {
		caption: 'Choose...',
		label: 'Page Referred To',
		labelStacking: 'vertical',
		allowTyping: false,
		allowMultiple: false
		}
	}
});
var domainPicker = new google.visualization.ControlWrapper({
controlType: 'CategoryFilter',
containerId: 'control3',
options: {
	filterColumnLabel:'Domain',
	ui: {
		caption: 'Choose...',
		label: 'Domain Referred From',
		labelStacking: 'vertical',
		allowTyping: false,
		allowMultiple: false
		}
	}
});
var rgroup = new google.visualization.ChartWrapper({
	chartType: 'Table',
	containerId: 'groupRef',
	options: {sortColumn:1,sortAscending:false,page:'enable',pageSize:10},
	dataTable: google.visualization.data.group(data{$x},[1],[{column:1,aggregation:google.visualization.data.count,type:'number',label:'#'}])
	});
rgroup.draw();
var pgroup = new google.visualization.ChartWrapper({
	chartType: 'Table',
	containerId: 'groupPage',
	options: {sortColumn:1,sortAscending:false,page:'enable',pageSize:10,allowHtml:true},
	dataTable: google.visualization.data.group(data{$x},[3],[{column:3,aggregation:google.visualization.data.count,type:'number',label:'#'}])
	});
pgroup.draw();
var dgroup = new google.visualization.ChartWrapper({
	chartType: 'Table',
	containerId: 'groupDomain',
	options: {sortColumn:1,sortAscending:false,page:'enable',pageSize:10},
	dataTable: google.visualization.data.group(data{$x},[4],[{column:4,aggregation:google.visualization.data.count,type:'number',label:'#'}])
	});
dgroup.draw();

var dashboard = new google.visualization.Dashboard(document.getElementById('dashboard'));
dashboard.bind(refPicker,pagePicker).bind(pagePicker,domainPicker).bind(domainPicker,[recentView]);
dashboard.draw(data{$x});

google.visualization.events.addListener(recentView,'ready',function(event){
	rgroup.setDataTable(google.visualization.data.group(recentView.getDataTable(),[1],[{column:1,aggregation:google.visualization.data.count,type:'number',label:'#'}]));
	pgroup.setDataTable(google.visualization.data.group(recentView.getDataTable(),[3],[{column:3,aggregation:google.visualization.data.count,type:'number',label:'#'}]));
	dgroup.setDataTable(google.visualization.data.group(recentView.getDataTable(),[4],[{column:4,aggregation:google.visualization.data.count,type:'number',label:'#'}]));
	rgroup.draw();
	pgroup.draw();
	dgroup.draw();
	});
} <!--drawDashboard-->\n

XXX;
} //if Table
echo <<<XXX
</script>\n
XXX;
} //make_chart
//https://groups.google.com/forum/#!msg/google-visualization-api/IQO4B1bjZzs/jpb7CzqakwMJ
//https://groups.google.com/forum/?fromgroups=#!searchin/google-visualization-api/dashboard$20aggregation/google-visualization-api/ssZ_7swXzpo/_DeLI57e1JcJ
//https://groups.google.com/forum/?fromgroups=#!searchin/google-visualization-api/controls$20without$20wrapper/google-visualization-api/7DsRGeN1fZY/82p65APFlTUJ
/*
var pie = new google.visualization.ChartWrapper({
	'chartType': 'PieChart',
	'containerId': 'drivepie',
	'options': {'width':300,'height':300,'title':'Eat Pie','pieSliceText':'label'},
	'view':{'columns':[3,4]}
	});
*/
/****************************************************/
function __construct() {
parent::__construct();
ecstatic_ecstatic($this, "Beta Testing Google Chart API!"); //logo banner
} //constructor
} //class ecstatic_charter
?>