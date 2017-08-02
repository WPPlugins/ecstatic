<?php
/****************************************************/
class ecstatic_wnkstable {

private $ecstatic;
private $namebit = array("spider"=>1, "kill"=>2, "nolog"=>4, "wlist"=>8, "xwlist"=>16);

/****************************************************/
function checkered($arg, $test) {return ($arg & $test) ? " checked" : "";}
function checkand($arg, $test) {return ($arg & $test) ? "X" : "";}

/****************************************************/
function show() {
global $wpdb;

$ex = array("", "X");
$wnks = 0;
foreach ($this->namebit as $key=>$nb)
	if (isset($_POST[$key]) AND $_POST[$key] == "true")
		$wnks |= $nb;

if (!$wnks)
	$wnks = 0xff;

$orcheck = 1; //the number one radio button
$checked = array("", "checked");
$table_name = $this->ecstatic->make_table("aux_lists");
if (isset($_POST["xor"]) AND $_POST["xor"] == 0)
	$weQ = "SELECT * FROM $table_name WHERE wnks & $wnks ORDER BY lastseen DESC";
elseif (isset($_POST["xor"]) AND $_POST["xor"] == 1) {
	$weQ = "SELECT * FROM $table_name WHERE wnks = $wnks ORDER BY lastseen DESC";
	$orcheck = 0;
	}
else
	$weQ = "SELECT * FROM $table_name ORDER BY lastseen DESC";

if ($_POST['yo_ajax'] != true) { //crude, but effective
	echo "<div id='wnksleft'>";

	echo "<div class='wnksform'>\n";
	echo "<h3>aux_lists table</h3>\n";
	echo "<form id='wnksform' method='post' action=''>\n";
	echo "View/Export entities with<br /><br />\n";
	echo "<label>any of <input type='radio' name='xor' VALUE='0' {$checked[$orcheck]} /></label>&nbsp; &nbsp; \n";
	echo "<label>only <input type='radio' name='xor' VALUE='1' {$checked[$orcheck^1]} /></label><br /><br />\n";
	echo "the following flags set:<br />\n";
	echo "<p><label><input type='checkbox' name='spider' value='1' {$this->checkered($wnks, 1)} /> Spider/Bot</label><br />\n";
	echo "<label><input type='checkbox' name='kill' value='2' {$this->checkered($wnks, 2)} /> KILL</label><br />\n";
	echo "<label><input type='checkbox' name='nolog' value='4' {$this->checkered($wnks, 4)} /> NoShow</label><br />\n";
	echo "<label><input type='checkbox' name='wlist' value='8' {$this->checkered($wnks, 8)} /> WList</label><br />\n";
	echo "<label><input type='checkbox' name='xwlist' value='16' {$this->checkered($wnks, 16)} /> xWList</label></p><br />\n";
	echo "<input id='view' type='submit' name='view_aux_lists' value='View' />&nbsp; &nbsp; &nbsp; \n";
	echo "<input type='submit' name='ecstatit' value='Export' /><br /><br />\n";
	echo "</form></div><!--wnksform-->\n\n";

	echo "<div class='importform'>\n";
	echo "<h3>Import/Merge</h3>\n";
	echo "<form method='post' action='' enctype='multipart/form-data'>\n";
	echo "<input type='hidden' name='MAX_FILE_SIZE' value='524288' />\n"; //must precede <input />
	echo "<p>File to upload/import: <input size='11' name='importfile' type='file' /></p>\n";
	echo "<br /><input type='submit' name='ecstatit' value='Import' />\n";
	echo "</form><br />\n";
	echo "<p>Files must be XML compliant, with 'name', 'token', 'type', and 'wnks' tags, as in the Export files.</p>\n";
	echo "<p>Rows with empty or missing tags will be ignored.&nbsp; Entries with identical 'token' and 'type' will be merged, and their 'wnks' bit flags melded, with the <i>last encountered</i> 'name' used, otherwise new rows will be added to the 'aux_lists' table, tagged with the current date, and 'Count' set to zero.</p>\n";
	echo "</div><!--importform-->\n\n";

	echo "<div class='wnkshelp'>\n";
	echo "<h3>MiniHelp</h3>\n";
	echo "<p><b>IP ranges</b> &mdash; Use * and - , as in... </p><h4>38.*.*.*</h4><h5>or</h5><h4>64.191.0-127.*</h4>\n";
	echo "<p>NS = NoShow.&nbsp; WL = WhiteList.&nbsp; xW = X-WhiteList.&nbsp; With Version 0.90, visitors can be flagged with multiple tokens, so if one token has the WhiteList bit set, one might want another of the tokens to cancel the WhiteListing.  Use the xW bitflag for that.</p>\n";
	echo "<p>Click the table to edit entries in place.&nbsp; It's like a miracle.</p>\n";
	echo "<p></p>\n";
//	echo "<p></p>\n";
	echo "</div><!--wnkshelp-->\n\n";

	echo "</div><!--wnksleft-->\n\n";
	} //if

$rows = $wpdb->get_results($weQ);
echo "<div id='wnksj'>\n";
echo "<table id='wnkstable' class='sortable' summary='wnks table'>\n";
echo "<caption>(W)hitelist (N)oShow (K)ill (S)piderBot Table&nbsp; &nbsp; " . sizeof($rows) . " records</caption>\n";
echo "<colgroup><col /><col /><col /><col /><col /><col width='30' align='center' /><col width='30' align='center' /><col width='30' align='center' /><col width='30' align='center' /><col width='30' align='center' /></colgroup>\n";
echo "<thead><tr id='trX'><th>Name</th><th class='natsort'>Token</th><th>Type</th><th>Last&nbsp;Seen</th><th>Count</th><th>Bot</th><th>Kill</th><th>NS</th><th>WL</th><th>xW</th></tr></thead>\n";
echo "<tbody>\n";
if ($rows) {
	foreach($rows as $r) {
		echo "<tr id='{$r->id}'><td>$r->name</td><td>$r->token</td><td>$r->type</td>";
		if ($r->lastseen)
			echo "<td><!--{$r->lastseen}-->" . date("m/d/y", $r->lastseen) . "</td>";
		else
			echo "<td></td>";
		echo "<td>$r->hits</td>";
		for ($x=1;$x<32;$x*=2)
			echo "<td>" . $this->checkand($r->wnks, $x) . "</td>";
		echo "</tr>\n";
		} //foreach
	}
else
	echo "<tr><td colspan='10' style='height:8em;vertical-align:middle;text-align:center;'>(empty!)</td></tr>\n";

echo "</tbody>\n</table>\n";
echo "</div><!--wnksj-->\n";

echo <<<XXX
<script type="text/javascript">
var sorter = new TINY.table.sorter("sorter")
sorter.reverse = true;
sorter.init("wnkstable",3)
</script>


XXX;
} //show

/****************************************************/
function __construct($ecstatic) {
$this->ecstatic = $ecstatic;
}
} //class ecstatic_wnkstable

/****************************************************/
class assoc_record {
var $ip = "";
var $ua = "";
var $ref = array();
var $ruri = array();
var $spider = "";
var $kill = "";
var $wnks = "";
var $day = "";
var $time = "";
var $score = 0;
} //class assoc_record

/****************************************************/
class ecstatic_details extends ecstatic {

private $imash = "";
private $maxtoshow = 0;
private $seref;
private $who = array("ip" => "IP", "ua" => "User Agent", "ref" => "Referrer", "ruri" => "Req. URI", "dom" => "Domain", "srch" => "Search");
private $nstring = array();
private $qstring = array();
private $hstring = array(
"mash" => array("Referrer", "REQUEST_URI", "Meta&nbsp;URI", "Score")
,"ip" => array("User Agent", "Referrer", "Meta&nbsp;URI", "IP#", "UA#", "Score")
,"ua" => array("IP", "Referrer", "Meta&nbsp;URI", "Domain", "IP#", "UA#", "Score")
,"ref" => array("IP", "User Agent", "Meta&nbsp;URI", "Domain", "IP#", "UA#", "Score")
,"ruri" => array("IP", "User Agent", "Referrer", "Domain", "IP#", "UA#", "Score")
,"dom" => array("IP", "User Agent", "Referrer", "Meta&nbsp;URI", "IP#", "UA#", "Score")
,"srch" => array("IP", "User Agent", "Referrer", "Meta&nbsp;URI", "Domain", "IP#", "UA#", "Score")
);
private $hstring2 = array(
"mash" => array("Referrer", "REQUEST_URI", "Score")
,"ip" => array("User Agent", "Referrer", "REQUEST_URI", "IP#", "UA#", "Score")
,"ua" => array("IP", "Referrer", "REQUEST_URI", "Domain", "IP#", "UA#", "Score")
,"ref" => array("IP", "User Agent", "REQUEST_URI", "Domain", "IP#", "UA#", "Score")
,"ruri" => array("IP", "User Agent", "Referrer", "Domain", "IP#", "UA#", "Score")
,"dom" => array("IP", "User Agent", "Referrer", "REQUEST_URI", "IP#", "UA#", "Score")
,"srch" => array("IP", "User Agent", "Referrer", "REQUEST_URI", "Domain", "IP#", "UA#", "Score")
);
/************************************************************************************/
function build_assoc_record($hit) {
global $wpdb, $url_bobbed;

static $ipz = array();
static $uaz = array();
static $refz = array();
static $ruriz = array();
static $wnks = array();

$rec = new assoc_record();

if (isset($ipz[$hit["ip"]]))
	$rec->ip = $ipz[$hit["ip"]];
else {
$rec->ip = $wpdb->get_row($wpdb->prepare("SELECT ip, score, domain FROM {$this->iurr_tables["ip"]} WHERE id = %d", $hit["ip"]));
	$dom = new ecstatic_get_host();
	$rec->ip->domain = $dom->domain_name($rec->ip->domain);
	$ipz[$hit["ip"]] = $rec->ip;
	}

if (isset($uaz[$hit["ua"]]))
	$rec->ua = $uaz[$hit["ua"]];
else
	$uaz[$hit["ua"]] = $rec->ua = $wpdb->get_row($wpdb->prepare("SELECT ua, aux, score FROM {$this->iurr_tables["ua"]} WHERE id = %d", $hit["ua"]));

if (isset($refz[$hit["ref"]]))
	$rec->ref = $refz[$hit["ref"]];
else {
$rec->ref = $wpdb->get_row($wpdb->prepare("SELECT ref, score FROM {$this->iurr_tables["ref"]} WHERE id = %d", $hit["ref"]));
	$rec->ref->idx = $hit["ref"];
	if ($rec->ref->ref) {
		$se = $this->seref->referendum($rec->ref->ref);
		$imash = $hit["ip"] . "." . $hit["ua"] . "." . $hit["datetime"];
		$staref = "<a href='{$this->url}/wp-admin/admin.php?page=ecstatic_mash&amp;se={$rec->ref->idx}&amp;imash={$imash}' title='Add to or Edit Search Engine database table' target='_blank'><span style='color:green;'>&loz;</span></a>";
		if (isset($se->not_in_aux_se))
			$rec->ref->ref_anchor = " <span style='color:blue;'>" . $se->name . "</span>: " . $se->anchor . " " . $staref;
		else
			$rec->ref->ref_anchor = $se->anchor . " " . $staref;
		}
	$refz[$hit["ref"]] = $rec->ref;
	}

$bobl = $url_bobbed . "/";
if (isset($ruriz[$hit["ruri"]]))
	$rec->ruri = $ruriz[$hit["ruri"]];
else {
	$rec->ruri = $wpdb->get_row($wpdb->prepare("SELECT ruri, score FROM {$this->iurr_tables["ruri"]} WHERE id = %d", $hit["ruri"]));
	if ($rec->ruri->ruri == $url_bobbed OR $rec->ruri->ruri == $bobl)
		$rec->ruri->home = true;
	$rec->ruri->idx = $hit["ruri"];
	$ruriz[$hit["ruri"]] = $rec->ruri;
	}

if (isset($wnks[$hit["ip"]][$hit["ua"]][$hit["ref"]][$hit["ruri"]]))
	$rec->wnks = $wnks[$hit["ip"]][$hit["ua"]][$hit["ref"]][$hit["ruri"]];
elseif ($rec->wnks = $this->is_in_lists2($rec->ip->ip, $rec->ua->ua, $rec->ref->ref, $rec->ruri->ruri, 0x8f)) //wnks 10001111b - xwlist - - - wlist nolog kill spider
	$wnks[$hit["ip"]][$hit["ua"]][$hit["ref"]][$hit["ruri"]] = $rec->wnks;

$rec->day = date("m/d", $hit["datetime"]);
$rec->time =  date("H:i:s", $hit["datetime"]);

return $rec;
} //build_assoc_record

/****************************************************/
function assoc_panel($tab, $idx) {
global $wpdb, $plain_ruris;

$hits = array();

if ($tab == "mash") {
	$scorez = array();
	list($ipx, $uax, $ts) = explode(".", $idx);
	$num = $wpdb->get_var($wpdb->prepare("SELECT count(*) FROM $this->hits_table WHERE ip=%d AND ua=%d", $ipx, $uax)); //count 'em
	$hits = $wpdb->get_results($wpdb->prepare("SELECT * FROM $this->hits_table WHERE ip=%d AND ua=%d ORDER BY datetime DESC LIMIT {$this->maxtoshow}", $ipx, $uax), ARRAY_A); //get up to limit
	}
elseif ($tab == "dom") {
	$h = $this->hits_table;
	$p = $this->iurr_tables["ip"];
	$nn = "SELECT count({$h}.ip) FROM {$h},{$p} WHERE {$h}.ip={$p}.id and {$p}.domain=%s";
	$qq = "SELECT {$h}.* FROM {$h},{$p} WHERE {$h}.ip={$p}.id and {$p}.domain=%s ORDER BY {$h}.datetime DESC LIMIT {$this->maxtoshow}";
	$num = $wpdb->get_var($wpdb->prepare($nn, $idx));
	$hits = $wpdb->get_results($wpdb->prepare($qq, $idx), ARRAY_A);
	}
elseif ($tab == "srch") {
	$this->iurr_tables["domain"] = $this->iurr_tables["ip"]; //add to existing array, temporarily
	foreach ($this->iurr_tables as $key => $table) {
		$ss = "SELECT id FROM $table WHERE $key LIKE %s";
		if ($xids = $wpdb->get_results($wpdb->prepare($ss, "%".$idx."%"))) {
			foreach ($xids as $xid) {
				if ($key == "domain")
					$tt = "SELECT * FROM $this->hits_table WHERE ip=%d";
				else
					$tt = "SELECT * FROM $this->hits_table WHERE $key=%d";
				if ($blaps = $wpdb->get_results($wpdb->prepare($tt, $xid->id), ARRAY_A)) {
					foreach ($blaps as $blap)
						$hits[] = $blap;
					}
				}
			}
		}
	unset($this->iurr_tables["domain"]); //remove temp pointer
	$num = sizeof($hits);
	if ($num) {
		foreach ($hits as $key => $row) { //reorient rows into columns for multisort
			$datetime[$key] = $row['datetime'];
			$ip[$key] = $row['ip'];
			$ua[$key] = $row['ua'];
			$ref[$key] = $row['ref'];
			$ruri[$key] = $row['ruri'];
			$scorebits[$key] = $row['scorebits'];
			$score[$key] = $row['score'];
			}
		array_multisort($datetime, SORT_DESC, $hits);
		}
	else
		echo "<br />String not found<br /><br />\n\n";

	if (sizeof($hits) > $this->maxtoshow)
		array_splice($hits, $this->maxtoshow); //truncate excessively large array
	}
else {
	$num = $wpdb->get_var($wpdb->prepare($this->nstring[$tab], $idx)); //count 'em
	$hits = $wpdb->get_results($wpdb->prepare($this->qstring[$tab], $idx), ARRAY_A); //get up to limit
	}

if ($num == 1)
	$a_str = "Only 1 hit";
elseif ($num <= $this->maxtoshow)
	$a_str = "All {$num} hits";
else
	$a_str = "Latest {$this->maxtoshow} of {$num} hits";

$b = $popups = 0;
foreach ($hits as $hit) {
	$rec = $this->build_assoc_record($hit);
	if (!$b) {
		$klugeA = array("ip" => $rec->ip->ip, "ua" => ecstatic_href_ua($rec->ua->ua), "ref" => $rec->ref->ref_anchor, "ruri" => ecstatic_makelink($rec->ruri->ruri));
		$klugeB = array("ip" => $rec->ip->score, "ua" => $rec->ua->score, "ref" => $rec->ref->score, "ruri" => $rec->ruri->score);
		echo "<div class='tabhead'>";
		if ($tab == "mash")
			echo "<div class='tabinfo'>{$a_str} from the <b>IP #{$ipx}/User Agent #{$uax}</b> combination</div>\n";
		elseif ($tab == "dom")
			echo "<div class='tabinfo'>{$a_str} from Domain: <b>{$idx}</b></div>\n";
		elseif ($tab == "srch")
			echo "\n<div class='tabinfo'>{$a_str} containing search string: <b>{$idx}</b></div>\n";
		elseif ($tab == "ua" AND $rec->ua->ua == "") {
			echo "<div class='tabinfo'>{$this->who[$tab]} #{$idx}:&nbsp; <b>{$klugeA[$tab]}</b><br /><br />{$a_str}</div>\n\n";
			echo "<div class='scoreform'>";
			echo "{$this->who[$tab]} #{$idx}<br />\n";
			echo "Empty UA Score: {$this->options["empty_ua"]}";
			echo "</div>";
			}
		else {
			echo "<div class='tabinfo'>{$this->who[$tab]} #{$idx}:&nbsp; <b>{$klugeA[$tab]}</b><br /><br />{$a_str}</div>\n\n";
			echo "<form method='post' name='{$tab}scofo' action='' class='scoreform'>\n";
			echo "{$this->who[$tab]} #{$idx}<br />\n";
			echo "<input type='text' size='2' name='score' value='{$klugeB[$tab]}' />&nbsp; \n";
			echo "<input type='hidden' name='tab' value='{$tab}' />\n";
			echo "<input type='hidden' name='idx' value='{$idx}' />\n";
			echo "<input type='hidden' name='imash' value='{$this->imash}' />\n"; //for return
			echo "<input type='hidden' name='ecstatit' value='score' />\n";
			echo "<input type='submit' name='play_change' value='Score!' />\n";
			echo "</form>\n";
			}
		echo "</div><!--tabhead-->\n\n";

		if ($tab == "srch")
			echo "<table class='ectable' summary='{$tab} assoc'>\n";
		else
			echo "<table class='ectable ecpop' summary='{$tab} assoc'>\n";

		echo "<thead><tr><th>Date</th><th>Time</th>";
		$hstring = $this->hstring;
		if ($plain_ruris)
			$hstring = $this->hstring2;
		foreach ($hstring[$tab] as $htitle) {
			if ($htitle == "Score")
				echo "<th class='score'>{$htitle}</th>";
			else
				echo "<th>{$htitle}</th>";
			}
		echo "</tr></thead><tbody>\n";
		$b++;
		}
	echo "\t<tr>\n\t";
	echo "<td>" . $rec->day . "</td>";
	echo "<td>" . $rec->time . "</td>";
	if ($tab != "ip" AND $tab != "mash")
		echo "<td>" . $rec->ip->ip . "</td>";
	if ($tab != "ua" AND $tab != "mash")
		echo "<td>" . $rec->ua->ua . "</td>";
	if ($tab != "ref") {
		echo "<td>" . $rec->ref->ref_anchor . "</td>";
		}

	if ($tab == "mash" OR ($tab != "ruri" AND $plain_ruris))
		echo "<td>" . $rec->ruri->ruri .  "</td>";
	if ($tab != "ruri" AND !$plain_ruris)
		echo "<td>" . ecstatic_makelink($rec->ruri->ruri) . "</td>";


	if ($tab != "ip" AND $tab != "mash" AND $tab != "dom")
		echo "<td>" . $rec->ip->domain . "</td>";

	if ($tab != "mash")
		echo "<td>{$hit["ip"]}</td><td>{$hit["ua"]}</td>\n";

	$extra_class = "";
	if ($hit["score"] == -1) {
		$extra_class = "wl";
		$hit["score"] = "*";
		}

	$imash = $hit["ip"] . "." . $hit["ua"] . "." . $hit["datetime"];
	$popUpstring = buildpopUpstring($this, $hit["scorebits"], $rec->ip->score, $rec->ua->score, $rec->ref->score, $rec->ruri->score, $rec->wnks);

	if ($this->options["stop_popups"] AND (++$popups > $this->options["stop_popups_beyond"] OR $hit["score"] < $this->options["no_lowscore_popups"]))
		echo "<td class='NOtdPop {$extra_class}'><a href='" . $this->url . "/wp-admin/admin.php?page=ecstatic_mash&amp;imash={$imash}' title='{$popUpstring}' target='_blank'>{$hit["score"]}</a></td>\n";
	else
		echo "<td class='tdPop {$extra_class}'><a href='" . $this->url . "/wp-admin/admin.php?page=ecstatic_mash&amp;imash={$imash}' target='_blank'>{$hit["score"]}</a><span class='pop'>{$popUpstring}</span></td>\n";

	echo "\t</tr>\n";
	} //foreach
echo "</tbody></table>\n";
} //assoc_panel

/****************************************************/
function details() {
global $wpdb;

if (isset($_GET["status"])) {
	$q = $_GET["status"];
	echo "<div class='updated' style='margin: 1em 0; padding: 0.5em; text-align: center;'><p>{$q}</p></div>";
	}

if (isset($_POST["ignored_ids_change"])) { //woo hoo - user control of browser id
	$ignored_ids = get_option("ecstatic_ignored_ids");
	foreach($_POST["token"] as $token => $val) {
		$lowtoken = strtolower($token);
		$utoken = str_replace(" ", "_", $token);
		if (isset($_POST[$token]) OR isset($_POST[$utoken])) {
			if ($ignored_ids[$lowtoken])
				unset($ignored_ids[$lowtoken]);
			}
		else {
			if (!$ignored_ids[$lowtoken])
				$ignored_ids[$lowtoken] = 1;
			}
		}
	update_option("ecstatic_ignored_ids", $ignored_ids);
	} //if isset

$this->imash = $_GET["imash"];
list($ipidx, $uaidx, $timestamp) = explode(".", $this->imash);
if (!is_numeric($ipidx) OR !is_numeric($uaidx) OR !is_numeric($timestamp))
	exit("Bad imash");
$hits = $wpdb->get_results($wpdb->prepare("SELECT * FROM $this->hits_table WHERE ip=%d AND ua=%d ORDER BY datetime DESC", $ipidx, $uaidx), ARRAY_A);
foreach ($hits as $h) {
	if ($h["datetime"] == $timestamp) {
		$hits[0]["ref"] = $refidx = $h["ref"];
		$hits[0]["ruri"] = $ruriidx = $h["ruri"];
		break;
		}
	}

$orgcount = count($hits);
if ($orgcount <= $this->maxtoshow)
	$orgcountstring = "All {$orgcount}";
else
	$orgcountstring = "Last {$this->maxtoshow} of {$orgcount}";

$rec = $this->build_assoc_record($hits[0]);

$dom = new ecstatic_get_host($ipidx, $rec->ip->ip, $this);

echo "<div id='details'>\n";
echo "<b>IP:</b> <strong>{$rec->ip->ip}</strong>&nbsp;&nbsp;&nbsp;&nbsp;";
echo "<b>Domain:</b> <strong>{$dom->full_domain_name()}</strong><br />\n";
/*
echo "<pre>";
print_r($);
echo "</pre>";
*/
echo "<b>User Agent:</b> <strong>" . ecstatic_href_ua($rec->ua->ua) . "</strong><br />\n";

include_once(WP_PLUGIN_DIR . "/" . dirname(plugin_basename(__FILE__)) . '/ecstatic_ua_parser.php');
$ua_parser = new USER_AGENT();
$rob = $ua_parser->load_from_string($rec->ua->ua);
$aux = 0;
if ($rob->browser != "Unknown")
	$aux = 1;
for ($zed=0; $zed<sizeof($rec->wnks); $zed++) {
	if ($rec->wnks[$zed]->wnks & 1) //wnks 0001b - spider
		$aux = 2;
	}
$wpdb->query($wpdb->prepare("UPDATE {$this->iurr_tables["ua"]} SET aux=%d, browser=%s, os=%s, renderer=%s WHERE id=%d", $aux, $rob->browser, $rob->os, $rob->renderer, $uaidx));

echo "<b>Renderer:</b> <strong>{$rob->renderer}</strong>&nbsp;&nbsp; \n";
echo "<b>Browser:</b> <strong>{$rob->browser}</strong>&nbsp;&nbsp; \n";
echo "<b>OS:</b> <strong>{$rob->os}</strong>&nbsp; \n";

if (sizeof($rob->ua_ids)) {
	$uacheck = array("", "checked");
	echo "<form method='post' name='ua_ids' action='' style='float:right;margin-right:2em;font-size:8pt;'>\n";
	echo "<span style='color:green;'>Got Browser?</span>&nbsp; \n";
	foreach($rob->ua_ids as $id => $bool) {
		echo "<input id='{$id}' type='checkbox' name='{$id}' value='1' onClick='this.form.submit()' $uacheck[$bool] />&nbsp;<label for='{$id}'>$id</label>&nbsp; \n";
		echo "<input type='hidden' name='token[{$id}]' value='0' />\n";
		}
	echo "<input type='hidden' name='ignored_ids_change' value='lets_change' />\n";
	echo " <a id='browser_help' href='#' title='browser_id_help'>help</a></form>\n\n";
	echo "<div id='browser_id_help'>";
	echo "The ecSTATic User Agent parser derives Browser names by breaking User Agent strings into token/version pairs and iterating through the tokens, eventually selecting one.&nbsp; Many of the token/version pairs are immaterial to the Browser id, but the program doesn't automatically know which ones to ignore.&nbsp; The \"Got Browser?\" form above allows the user to train the parser in said regards, showing all the tokens found in the current User Agent.<br /><br />In practice, the parser will generally use the rightmost checked token as the Browser name, so an incorrectly identified Browser will match a checked token toward the right end of the form.&nbsp; Uncheck that token and the page will cycle, reprocessing the User Agent string.&nbsp; Uncheck as many spurious tokens as necessary, one at a time, until a correct Browser name is returned.<br /><br />The unchecked tokens are saved in the WordPress options database table.&nbsp; There are currently {$rob->num_ignored} tokens in the ignored_ids array.&nbsp; See them <a href='{$this->url}/wp-admin/admin.php?page=ecstatic_mash&amp;ignored_ids=true&amp;imash=true' target='_blank'>here</a>.\n";
	echo "</div>\n";
} //if sizeof

if (isset($rec->wnks)) {
	$flags = array(1=>"Spider/Bot", 2=>"KILL", 4=>"NoShow", 8=>"WhiteList", 16=>"<strike>WhiteList</strike>");
	for ($zed=0; $zed<sizeof($rec->wnks); $zed++) {
		echo "<br style='clear:both;' />Named <strong>{$rec->wnks[$zed]->name}</strong> with <strong style='color:green;'>" . strtoupper($rec->wnks[$zed]->type) . "</strong> token <strong style='color:indigo;'>{$rec->wnks[$zed]->token}</strong> and flag(s): <strong style='color:brown;'>";
		$double_colons = 0;
		for ($bb=1;$bb<32;$bb*=2) {
			if ($rec->wnks[$zed]->wnks & $bb) {
				if ($double_colons++)
					echo " :: ";
				echo $flags[$bb];
				}
			}
		echo "</strong>\n&nbsp; Seen <strong>{$rec->wnks[$zed]->hits}</strong> times\n";
		}
	}
if ($aux != 2 AND preg_match("/bot|spider|crawl/i", $rec->ua->ua))
	echo "<br /><strong style='color:red;'>Unrecognized Spider/Bot</strong>\n";
echo "</div><!--details-->\n\n";

include(WP_PLUGIN_DIR . "/" . dirname(plugin_basename(__FILE__)) . "/ecstatic_whois.php");
$netrange = ecstatic_whois($rec->ip->ip, $this->options["iporcidr"]); //oh, yeah.  makes its own <div>

echo "<div id='addspkbox'>\n";
echo <<<XXX
<form id="addwnks" method="post" action="">
<table summary="addspk" width="100%">
<thead><tr><th colspan="2">Add:&nbsp; <input type="checkbox" id="spbo1" name="spbo" value=""> <label for="spbo1" style="color:blue;">Spider/Bot</label>&nbsp;&nbsp; <input type="checkbox" id="kill1" name="kill" value=""> <label for="kill1" style="color:red;">Kill</label>&nbsp;&nbsp; <input type="checkbox" id="noshow1" name="nolog" value=""> <label for="noshow1" style="color:green;">NoShow</label>&nbsp;&nbsp; <input type="checkbox" id="wlist1" name="wlist" value=""> <label for="wlist1" style="color:#fff;">WhiteList</label></th></tr></thead>
<tbody><tr>
<td rowspan="4" align="center">Common Name<br /><input type="text" size="20" name="comname" value=""><br /><br />
<input type="submit" name="play_change" value="Submit Token"></td>
<td><input id="netrange" type="text" size="24" name="ip" value="{$netrange}"> <input type="radio" name="cat" VALUE="ip" checked> <a id="jQnetrange" href="#" title="{$netrange}">Range</a> or <a id="jQip" href="#" title="{$rec->ip->ip}">IP#</a></td></tr><tr>
<td><input type="text" size="24" name="ua" value="{$rec->ua->ua}"> <input type="radio" id="radioua" name="cat" VALUE="ua"> <label for="radioua">User Agent Token</label></td></tr><tr>
<td><input type="text" size="24" name="ref" value="{$rec->ref->ref}"> <input type="radio" id="radioref" name="cat" VALUE="ref"> <label for="radioref">Referrer Token</label></td></tr><tr>
<td><input type="text" size="24" name="ruri" value="{$rec->ruri->ruri}"> <input type="radio" id="radioruri" name="cat" VALUE="ruri"> <label for="radioruri">Req. URI Token</label></td>
</tr></tbody></table>
<input type="hidden" name="imash" value="{$this->imash}">
<input type="hidden" name="ecstatit" value="addspikillnolog">
</form>

XXX;
echo "</div><!--addspkbox-->\n\n";

echo "<div class='domtab'>\n";
echo "<ul class='domtabs'>\n";
echo "<li><a href='#mash'>IP/User Agent</a></li>\n";
echo "<li><a href='#ip'>IP - {$rec->ip->score}</a></li>\n";
if ($rec->ua->ua == "")
	echo "<li><a href='#ua'>User Agent - {$this->options["empty_ua"]}</a></li>\n";
else
	echo "<li><a href='#ua'>User Agent - {$rec->ua->score}</a></li>\n";
$refp = $rurip = $domp = 0;
if ($rec->ref->ref OR $rec->ref->idx != $refidx) { //yep
	echo "<li><a href='#ref'>Referrer - {$rec->ref->score}</a></li>\n";
	$refp++;
	}
if (!isset($rec->ruri->home) OR $rec->ruri->idx != $ruriidx) { //woo
	echo "<li><a href='#ruri'>Req. URI - {$rec->ruri->score}</a></li>\n";
	$rurip++;
	}
if (!$dom->is_domain_error()) {
	echo "<li><a href='#domain'>Domain</a></li>\n";
	$domp++;
	}
echo "<li><a href='#wnks'>WNKS</a></li>\n";

$netr = explode(".", $netrange); //prepare for the Near IPs tab
$nutr = $netr[0] . ".";
$nutrm = $netr[0]-1 . ".";
$nutrp = $netr[0]+1 . ".";
$ntr = $ntrf = $nsips = array();
//if (strpos($netrange, "/") === false) {
$this->load_ips($netrange); //$netrange to $this->ips[$netrange][min], $this->ips[$netrange][max], $this->ips previously loaded with WNKS ips - CIDR ranges are not loaded
foreach ($this->ips as $stoken => $ips) {
	if (strpos($stoken, $nutr) === 0 OR strpos($stoken, $nutrm) === 0 OR strpos($stoken, $nutrp) === 0) { //first octet of $netrange plus or minus one
		$ntr[] = $stoken;
		if (($ips["min"] >= $this->ips[$netrange]["min"] AND $ips["max"] <= $this->ips[$netrange]["max"]) OR ($this->ips[$netrange]["min"] >= $ips["min"] AND $this->ips[$netrange]["max"] <= $ips["max"])) //equal or one within the other
			$ntrf[$stoken] = 2;
		elseif (($this->ips[$netrange]["min"]-1 <= $ips["min"] AND $this->ips[$netrange]["max"]+1 >= $ips["min"]) OR ($this->ips[$netrange]["min"]-1 <= $ips["max"] AND $this->ips[$netrange]["max"]+1 >= $ips["max"]) OR ($this->ips[$netrange]["min"] < $ips["min"] AND $this->ips[$netrange]["max"] > $ips["max"])) //overlap or adjacent
			$ntrf[$stoken] = 1;
		else
			$ntrf[$stoken] = 0;
		}
	} //foreach

/*
if ($dom->is_domain_error())
	$nsipsq = $wpdb->get_results($wpdb->prepare("SELECT id, ip FROM {$this->iurr_tables["ip"]} WHERE ip LIKE %s AND score > 0", "{$nutr}%"));
else
	$nsipsq = $wpdb->get_results($wpdb->prepare("SELECT id, ip FROM {$this->iurr_tables["ip"]} WHERE (ip LIKE %s OR domain = %s) AND score > 0", "{$nutr}%", $dom->domain_name()));
if ($nsipsq)
	foreach($nsipsq as $nsipq)
		$nsips[$nsipq->id] = $nsipq->ip;
*/
if ($dom->is_domain_error())
	$nsipsq = $wpdb->get_results($wpdb->prepare("SELECT id, ip, score FROM {$this->iurr_tables["ip"]} WHERE ip LIKE %s", "{$nutr}%"));
else
	$nsipsq = $wpdb->get_results($wpdb->prepare("SELECT id, ip, score FROM {$this->iurr_tables["ip"]} WHERE (ip LIKE %s OR domain = %s)", "{$nutr}%", $dom->domain_name()));
if ($nsipsq) {
	foreach($nsipsq as $nsipq) {
//		if ($nsipq->score)
		$nsips[$nsipq->id] = $nsipq->ip;
		}
	}

if (sizeof($ntr) > 1 OR $nsipsq) {
	natsort($ntr);
	natsort($nsips);
	echo "<li><a href='#nips'>Near IPs</a></li>\n";
	}

echo "<li><a href='#search'>Search</a></li>\n";
echo "</ul>\n";

echo "<div class='tab'><a name='mash' id='mash'></a>\n";
$this->assoc_panel("mash", $this->imash);
echo "</div><!--mash domtab-->\n\n";

echo "<div class='tab'><a name='ip' id='ip'></a>\n";
$this->assoc_panel("ip", $ipidx);
echo "</div><!--ip domtab-->\n\n";

echo "<div class='tab'><a name='ua' id='ua'></a>\n";
$this->assoc_panel("ua", $uaidx);
echo "</div><!--ua domtab-->\n\n";

if ($refp) {
	echo "<div class='tab'><a name='ref' id='ref'></a>\n";
	$this->assoc_panel("ref", $refidx);
	echo "</div><!--referrer domtab-->\n\n";
	}
if ($rurip) {
	echo "<div class='tab'><a name='ruri' id='ruri'></a>\n";
	$this->assoc_panel("ruri", $ruriidx);
	echo "</div><!--ruri domtab-->\n\n";
	}
if ($domp) {
	echo "<div class='tab'><a name='domain' id='domain'></a>\n";
	$this->assoc_panel("dom", $dom->domain_name);
	echo "</div><!--domain domtab-->\n\n";
	}

echo "<div class='tab'><a name='wnks' id='wnks'></a>\n";
$wnkstable = new ecstatic_wnkstable($this);
$wnkstable->show();
echo "<div class='clear'></div>\n";
echo "</div><!--wnks domtab-->\n\n";

if (sizeof($ntr)) {
	echo "<div class='tab'><a name='nips' id='nips'></a>\n";
	echo "<div id='nipsleft'>";
	echo "<div class='nsipshelp'>\n";
	echo "<h3>Near Ips</h3>\n";
	echo "<p>Use the table to the right and the one below to help eliminate overlapping Ranges, or to consolidate adjacent Ranges.</p>\n";
	echo "<p>In the table to the right, Ranges that overlap or are adjacent to&nbsp; <b>{$netrange}&nbsp; </b> will be colored <b class='near'>brown</b>, while those \"covered\" by it will be <b class='vnear'>blue</b>.</p>\n";
	echo "<p>Edit in place, just like the WNKS table, though any editing in either table won't be reflected in the other until the entire page is refreshed.</p>\n";
	if ($dom->is_domain_error())
		echo "<p>The small table below (if shown) shows IPs with Scores greater than 0 near <b>{$netrange}</b> pulled from the IP Table.</p><br />\n";
	else
		echo "<p>The small table below (if shown) shows IPs from the IP Table near <b>{$netrange}</b> and <b>{$dom->domain_name}</b> entries, with Scores greater than 0.</p><br />\n";
	echo "</div><!--nsipsshelp-->\n\n";

if ($nsipsq) {
	echo "<div class='nsipshelp'>\n";
	if ($dom->is_domain_error())
		echo "<h4>Like <strong>{$rec->ip->ip}</strong> w/SCORES</h4>\n";
	else
		echo "<h4>Like <strong>{$rec->ip->ip}</strong> or <strong>{$dom->domain_name}</strong> w/SCORES</h4>\n";
	echo "<table id='nsipstable' class='sortable' summary='near scored ips'>\n";
	echo "<thead><tr><th>IP</th><th>Domain</th><th>S</th><th>Q</th><th>Last Seen</th></tr></thead>\n<tbody>\n";
	foreach($nsips as $id => $ip) {
		$iii = $wpdb->get_row($wpdb->prepare("SELECT score, domain FROM {$this->iurr_tables["ip"]} WHERE id = %d", $id));
		$jjj = $wpdb->get_row($wpdb->prepare("SELECT count(*) as cnt, max(datetime) as maxd FROM {$this->hits_table} WHERE ip = %d", $id));
		if ($rec->ip->ip == $ip)
			echo "<tr class='vnear'><td>{$ip}</td><td>" . $dom->domain_name($iii->domain) . "</td><td style='text-align:right;'>{$iii->score}</td><td style='text-align:right;'>{$jjj->cnt}</td><td>" . date("m/d/y", $jjj->maxd) . "</td></tr>\n";
		else
			echo "<tr><td>{$ip}</td><td>" . $dom->domain_name($iii->domain) . "</td><td style='text-align:right;'>{$iii->score}</td><td style='text-align:right;'>{$jjj->cnt}</td><td>" . date("m/d/y", $jjj->maxd) . "</td></tr>\n";
		}

	echo "</tbody>\n</table>\n";
	echo "<p><b>S</b>=Score&nbsp; <b>Q</b>=Hit Count</p><br />\n";
	echo "</div><!--nsipshelp-->\n\n";
	}
	echo "</div><!--nipsleft-->\n\n";

	echo "<div id='nipsd'>\n";
	echo "<table id='nipstable' class='sortable' summary='near ips table'>\n";
	echo "<caption>IPs in WNKS Table Near <strong>{$netrange}</strong></caption>\n";
	echo "<colgroup><col /><col /><col /><col /><col /><col width='30' align='center' /><col width='30' align='center' /><col width='30' align='center' /><col width='30' align='center' /><col width='30' align='center' /></colgroup>\n";
	echo "<thead><tr id='trZ'><th>Name</th><th>Token</th><th>Type</th><th>Last&nbsp;Seen</th><th>Count</th><th>Bot</th><th>Kill</th><th>NS</th><th>WL</th><th>xW</th></tr></thead>\n<tbody>\n";
	$alist = $this->make_table("aux_lists");
	foreach($ntr as $stoken) {
		$style = "";
		$r = $wpdb->get_row($wpdb->prepare("SELECT * FROM $alist WHERE token = %s", $stoken));
		if ($r) {
			if ($ntrf[$stoken] == 1)
				$style = "class='near'";
			elseif ($ntrf[$stoken] == 2)
				$style = "class='vnear'";
			echo "<tr id='n{$r->id}' {$style}><td>$r->name</td><td>$r->token</td><td>$r->type</td>"; //must give id that doesn't clash with wnkstable ids - the "n" is stripped in wnksplay() in ecstatic_forms
			if ($r->lastseen)
				echo "<td>" . date("m/d/y", $r->lastseen) . "</td>";
			else
				echo "<td></td>";
			echo "<td>$r->hits</td>";
			for ($x=1;$x<32;$x*=2)
				echo "<td>" . $wnkstable->checkand($r->wnks, $x) . "</td>";
			echo "</tr>\n";
			}
		}
	echo "</tbody>\n</table>\n";
	echo "</div><!--nipsd-->\n";
	echo "<div class='clear'></div>\n";
	echo "</div><!--near ip domtab-->\n\n";
	}

echo "<div class='tab'><a name='search' id='search'></a>\n";
echo "<form method='post' name='dmsearch' id='dsearch' action=''>";
echo "<input type='text' name='dminput' size='32' id='dsearchtext' value='' />";
echo "<input type='submit' name='dsearchsubmit' value='Search' />";
echo "&nbsp; &nbsp; &nbsp; <label for='dsearchtext'>[searches:&nbsp; ip &middot; user agent &middot; referrer &middot; requested uri &middot; domain]</label><br />";
echo "</form>";
echo "<div id='blurb' style='display:none;'><br />Searching...<br /><br /></div>\n";
echo "<div id='searched' class='ecpop'></div>\n";
echo "</div><!--search domtab-->\n\n";
echo "</div><!--domtab container-->\n\n";
} //details

/****************************************************/
function __construct() {
parent::__construct();
$this->seref = new ecstatic_seref($this); //search engine referrers
$this->maxtoshow = $this->options["maxtoshow"];
$this->nstring = array(
"ip" => "SELECT count(*) FROM $this->hits_table WHERE ip=%d"
,"ua" => "SELECT count(*) FROM $this->hits_table WHERE ua=%d"
,"ref" => "SELECT count(*) FROM $this->hits_table WHERE ref=%d"
,"ruri" => "SELECT count(*) FROM $this->hits_table WHERE ruri=%d"
);
$this->qstring = array(
"ip" => "SELECT * FROM $this->hits_table WHERE ip=%d ORDER BY datetime DESC LIMIT {$this->maxtoshow}"
,"ua" => "SELECT * FROM $this->hits_table WHERE ua=%d ORDER BY datetime DESC LIMIT {$this->maxtoshow}"
,"ref" => "SELECT * FROM $this->hits_table WHERE ref=%d ORDER BY datetime DESC LIMIT {$this->maxtoshow}"
,"ruri" => "SELECT * FROM $this->hits_table WHERE ruri=%d ORDER BY datetime DESC LIMIT {$this->maxtoshow}"
);
}
} //class ecstatic_details
?>