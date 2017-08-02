<?php
/***********************************************************************************/
class ecstatic_options extends ecstatic {

/***************************************/
function checked($val2check) {
if ($this->options["dom_method"] & $val2check)
	return "checked";
else
	return "";
} //checked

/***********************************************************************************/
function option_form() {
global $wpdb;
if (isset($_GET["submit"])) {
	$qa = array("NOT<br />", "");
	$q = $_GET["submit"];
	echo "<div class='updated' style='position:relative;float:left;margin:5em 0 0 2em;text-align:center;'><p>Options<br />{$qa[$q]}Saved!</p></div>";
	}
$checked = array("0" => "", "1" => "checked");
$panel1 = $panel2 = $panel3 = array("reg" => "" , "feed" => "", "bot" => "");
$panel1[$this->options["panel1"]] = "selected";
$panel2[$this->options["panel2"]] = "selected";
$panel3[$this->options["panel3"]] = "selected";
$anonref = array("0" => "", "1" => "", "2" => "", "3" => "", "4" => "", "5" => "");
$anonref[$this->options["anonref"]] = "selected";

echo <<<XXX
<div id='option_wrap'>
<form method='post' action=''>
<h3>Banner and Graph Display</h3>
<div class='option_form_box'>
<p><input type="checkbox" name="showbannergraph" value="1" {$checked[$this->options['showbannergraph']]} /> Show Banner and Graph at top of pages</p>

<div style='position:relative;float:left;margin-left:9em;padding:0.25em 0;text-align:left;'>
	<input type="checkbox" name="showgraphregi" value="1" {$checked[$this->options['showgraphregi']]} /> Graph Individual Visitors<br />
	<input type="checkbox" name="showgraphregp" value="1" {$checked[$this->options['showgraphregp']]} /> Graph Visitor Pageviews<br />
</div>
<div style='position: relative; float: left; margin-left: 2em; padding: 0.25em 0;text-align: left; '>
	<input type="checkbox" name="showgraphfeedi" value="1" {$checked[$this->options['showgraphfeedi']]} /> Graph Individual Feeds<br />
	<input type="checkbox" name="showgraphfeedp" value="1" {$checked[$this->options['showgraphfeedp']]} /> Graph Feed Pageviews<br />
</div>
<div style='position: relative; float: left; margin-left: 2em;margin-right:auto; padding: 0.25em 0;text-align: left; '>
	<input type="checkbox" name="showgraphboti" value="1" {$checked[$this->options['showgraphboti']]} /> Graph Individual Spider/Bots<br />
	<input type="checkbox" name="showgraphbotp" value="1" {$checked[$this->options['showgraphbotp']]} /> Graph Spider/Bot Pageviews<br />
</div>

<div class='clear'></div>
<p>Show <input type="text" size="2" name="daystograph" value='{$this->options["daystograph"]}' />days in top banner Graph - range 7 - 90 but not all values may be aesthetically pleasing</p>
</div><!--option_form_box-->

<h3>ecSTATic (Main) Page Panels</h3>
<div class='option_form_box'>
<p><b>Change what shows in the panels in the main 'ecSTATic' page</b></p>
<div style='position: relative; float: left; margin-left: 3em; padding: 0.05em 0;'>
<input type="checkbox" name="showreg" value="1" {$checked[$this->options['showreg']]} /> Show Visitor listing in Main page
</div>
<div style='position: relative; float: left; margin-left: 1em; padding: 0.05em 0;'>
<input type="checkbox" name="showfeed" value="1" {$checked[$this->options['showfeed']]} /> Show Feed listing in Main page
</div>
<div style='position: relative; float: left; margin-left: 1em; padding: 0.05em 0;'>
<input type="checkbox" name="showbot" value="1" {$checked[$this->options['showbot']]} /> Show Spider/Bot listing in Main page<br />
</div>
<div style='width: 100%; margin: 0; padding-top: 2em; text-align: center;'>
<p><b>Change the order of what shows in Main page</b></p>
<div style='position: relative; width: 32%; float: left; margin-top: -1em; padding: 0;'>
<p><b>First in Main page</b></p>
<select name="panel1" size="1">
<option value="reg" {$panel1["reg"]}> Regular Visitors </option><option value="feed" {$panel1["feed"]}> Feed Visitors </option><option value="bot" {$panel1["bot"]}> Bot/Spiders </option>
</select>
</div>
<div style='position: relative; width: 32%; float: left; margin-top: -1em; padding: 0;'>
<p><b>Second in Main page</b></p>
<select name="panel2" size="1">
<option value="reg" {$panel2["reg"]}> Regular Visitors </option><option value="feed" {$panel2["feed"]}> Feed Visitors </option><option value="bot" {$panel2["bot"]}> Bot/Spiders </option>
</select>
</div>
<div style='position: relative; width: 32%; float: left; margin-top: -1em; padding: 0;'>
<p><b>Last in Main page</b></p>
<select name="panel3" size="1">
<option value="reg" {$panel3["reg"]}> Regular Visitors </option><option value="feed" {$panel3["feed"]}> Feed Visitors </option><option value="bot" {$panel3["bot"]}> Bot/Spiders </option>
</select>
</div>
</div>
<div class='clear'></div>
</div>

<h3>Miscellaneous Settings</h3>
<div class='option_form_box'>
<p><input type="checkbox" name="collectloggeduser" value="1" {$checked[$this->options["collectloggeduser"]]} /> Collect logged in users&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="logsubadmin" value="1" {$checked[$this->options["logsubadmin"]]} /> Collect logged  users not Administrators</p>
<p>Count as New Visitor after <input type="text" size="2" name="newvisitorminutes" value="{$this->options["newvisitorminutes"]}" /> minutes</p>
<p><input type="checkbox" name="manual_purge" value="1" {$checked[$this->options["manual_purge"]]} /> Manual Purge&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Purge NON-spider/bots more than <input type="text" size="2" name="purgeolderthan" value="{$this->options["purgeolderthan"]}" /> days old</p>
<p>Purge Feed Reads more than <input type="text" size="2" name="purgerssolderthan" value="{$this->options["purgerssolderthan"]}" /> days old&nbsp;&nbsp;&nbsp;&nbsp;Purge Spider/Bots more than <input type="text" size="2" name="purgebotolderthan" value="{$this->options["purgebotolderthan"]}" /> days old</p>
<p>Show <input type="text" size="3" name="daystoshow" value="{$this->options["daystoshow"]}" /> days of stats in stat pages. ("1" = 24 hours of hits.  ".5" displays last 12 hours of hits.  Etc.)</p>
<p>Show maximum of <input type="text" size="3" name="maxtoshow" value="{$this->options["maxtoshow"]}" /> hits in Details page (range 1 - 9999, Default: 64)</p>

<p style='line-height:2em;'><input type="checkbox" name="iporcidr" value="1" {$checked[$this->options["iporcidr"]]} /> Prefer CIDR notation&nbsp; &nbsp; &nbsp; &nbsp;
<input type="checkbox" name="plain_ruris" value="1" {$checked[$this->options["plain_ruris"]]} /> Show raw Requested_URIs instead of shortened ones.</p>

<p style='line-height:2em;'><input type="checkbox" name="enablewidget" value="1" {$checked[$this->options["enablewidget"]]} /> Enable Widget&nbsp; &nbsp; &nbsp; &nbsp;
<input type="checkbox" name="noprefetch" value="1" {$checked[$this->options["noprefetch"]]} /> Block "link prefetching" by Mozilla&nbsp; &nbsp; &nbsp; &nbsp;
<input type="checkbox" name="block_new_bots" value="1" {$checked[$this->options["block_new_bots"]]} /> Auto-Block NEW Spider/Bots</p>

<p><input type="checkbox" name="stop_popups" value="1" {$checked[$this->options["stop_popups"]]} /> Inhibit Score PopUps&nbsp; &nbsp; &nbsp; &nbsp;
Show first <input type="text" size="3" name="stop_popups_beyond" value="{$this->options["stop_popups_beyond"]}" /> PopUps&nbsp; &nbsp; &nbsp; &nbsp;
No PopUps for scores less than <input type="text" size="3" name="no_lowscore_popups" value="{$this->options["no_lowscore_popups"]}" /></p>

<p>Referrer Link Anonymizer &nbsp;
<select name="anonref" size="1">
<option value="0" {$anonref[0]}> none </option><option value="1" {$anonref[1]}> anonym.to </option><option value="2" {$anonref[2]}> surfsneaky </option><option value="3" {$anonref[3]}> linkscheck </option><option value="4" {$anonref[4]}> nullrefer </option><option value="5" {$anonref[5]}> urlink2 </option>
</select>&nbsp; (uses third party redirection, performance may vary.)
</p>

</div>

<h3>Sequential View Settings</h3>
<div class='option_form_box'>
<!--
<p><b>Columns to show</b></p>
<p><input type="checkbox" name="mdate" value="1" {$checked[$estats->mdate]} /> Date&nbsp; &nbsp;
<input type="checkbox" name="mip" value="1" {$checked[$estats->mip]} /> IP&nbsp; &nbsp;
<input type="checkbox" name="mipq" value="1" {$checked[$estats->mipq]} /> IPQ&nbsp; &nbsp;
<input type="checkbox" name="mdomain" value="1" {$checked[$estats->mdomain]} /> Domain&nbsp; &nbsp;
<input type="checkbox" name="mbrowser" value="1" {$checked[$estats->mbrowser]} /> Browser&nbsp; &nbsp;
<input type="checkbox" name="mrenderer" value="1" {$checked[$estats->mrenderer]} /> Renderer&nbsp; &nbsp;
<input type="checkbox" name="mos" value="1" {$checked[$estats->mos]} /> OS&nbsp; &nbsp;
<input type="checkbox" name="mruri" value="1" {$checked[$estats->mruri]} /> Req. Page&nbsp; &nbsp;
<input type="checkbox" name="mref" value="1" {$checked[$estats->mref]} /> Referrer&nbsp; &nbsp;
<input type="checkbox" name="mscore" value="1" {$checked[$estats->mscore]} /> Score</p>
<p class='option_text'><b>Turning the Domain column Off does not turn the Reverse IP lookup Off.  The Domain information will still be retrieved and stored, but will not be shown.  Use the setting below to turn both off. </b></p>
-->
<p><input type="checkbox" name="skip_rip" value="1" {$checked[$this->options["skip_rip"]]} /> Skip Reverse IP Lookup in Sequential View (faster, but NO Domain info is retrieved or saved)</p>
<p class='option_text'><b>If one wants the Domain information, but not the delay caused by multiple Domain name requests during the Sequential View page load, see the note respecting the "Domain Check" option in the "Anti-Maleagant Scoring" section, below. </b></p><br />
<!--
<p><input type="checkbox" name="noshowbots" value="1" {$checked[$this->options["noshowbots"]]} /> Do not show Spider/Bots in the Sequential View.&nbsp; A toggle is available on the Sequential View page.</p>
-->
</div>


XXX;

$etime = $this->options["estats"];
if ($etime)
	$enable_email = 1;
else
	$enable_email = 0;
$this->estats_table = $this->make_table("estats");
$estats = $wpdb->get_row("SELECT * FROM $this->estats_table");

echo <<<XXX
<h3>Sequential Log eMail</h3>
<div class='option_form_box'>
<p><b>Send a copy of the Sequential Page in an eMail</b></p>
<p><input type="checkbox" name="enable_email" value="1" {$checked[$enable_email]} /> Enable Email Stats&nbsp; &nbsp; &nbsp;
Send every <input type="text" size="2" name="email_every" value="{$estats->email_every}" /> Day(s)&nbsp; &nbsp; &nbsp;
Send at <input type="text" size="2" name="email_time" value="{$estats->email_time}" /> o&#39;clock. (0 = midnight, 1 = 1:00 a.m., etc.)</p>
<p>Send eMail to: <input type="text" size="72" name="emailaddys" value="{$estats->addys}" /><br />Separate multiple addresses with commas.</p>
<p>eMail Subject Line: <input type="text" size="72" name="subject" value="{$estats->subject}" /><br />
Available tags (with brackets): [date] [visitors] [pages] [feeds] [bots] [totalv] [totalp] [logins]</p>
<p>Extra header --  X-ecSTATic: <input type="text" size="36" name="xheader" value="{$estats->xheader}" /><br />
Adds an extra key:value pair to the eMail header that some eMail clients can filter on.</p>
<p>Columns:
<input type="checkbox" name="mdate" value="1" {$checked[$estats->mdate]} /> Date/Time&nbsp; &nbsp;
<input type="checkbox" name="mip" value="1" {$checked[$estats->mip]} /> IP&nbsp; &nbsp;
<input type="checkbox" name="mipq" value="1" {$checked[$estats->mipq]} /> IPQ&nbsp; &nbsp;
<input type="checkbox" name="mbrowser" value="1" {$checked[$estats->mbrowser]} /> Browser&nbsp; &nbsp;
<input type="checkbox" name="mos" value="1" {$checked[$estats->mos]} /> OS&nbsp; &nbsp;
<input type="checkbox" name="mruri" value="1" {$checked[$estats->mruri]} /> Requested Page&nbsp; &nbsp;
<input type="checkbox" name="mref" value="1" {$checked[$estats->mref]} /> Referrer&nbsp; &nbsp;
<input type="checkbox" name="mscore" value="1" {$checked[$estats->mscore]} /> Score<br />
The table makes up the main body of the eMail.  Choose the columns you want included.</p>
<p><input type="checkbox" name="mlinks" value="1" {$checked[$estats->mlinks]} /> Include clickable links in the emailed table.</p>
<br /><p><b>Note:</b>  Does not create a Cron Job running at the exact same time every day or night.<br />eMails are triggered by the first visitor hit <i>after</i> the designated "Send" time.<br /><br /></p>
<p><input type="checkbox" name="testtesttest" value="1" /> Send One Time eMail now, using parameters above.</p>


XXX;
if ($etime)
	echo "<p>Next eMail scheduled for " . date("F d, Y H:i:s", $etime) . "</p>";
else
	echo "<p>No eMail currently scheduled</p>";

echo <<<XXX
</div>

<h3>SomeStats Graph Settings</h3>
<div class='option_form_box'>
<p><b>Sort order for small graphs</b></p>
Alphabetical <input type="radio" name="graphsort" VALUE="0" {$checked[$this->options["graphsort"]^1]} />&nbsp;&nbsp;&nbsp;
By rank <input type="radio" name="graphsort" VALUE="1" {$checked[$this->options["graphsort"]^0]} /><br />
<p>Show top <input type="text" size="2" name="topentriestoshow" value="{$this->options["topentriestoshow"]}" /> entries in small graphs (Default: 25)</p>
</div>

<h3>Reverse IP Lookup Method</h3>
<div class='option_form_box'>
<p>host <input type="radio" name="dom_method" VALUE="1" {$this->checked(1)} />&nbsp;&nbsp;&nbsp;&nbsp;
nslookup <input type="radio" name="dom_method" VALUE="2" {$this->checked(2)} />&nbsp;&nbsp;&nbsp;&nbsp;
dig <input type="radio" name="dom_method" VALUE="4" {$this->checked(4)} />&nbsp;&nbsp;&nbsp;&nbsp;
gethostbyaddr() <input type="radio" name="dom_method" VALUE="8" {$this->checked(8)} />&nbsp;&nbsp;&nbsp;&nbsp;
dns_get_record() <input type="radio" name="dom_method" VALUE="16" {$this->checked(16)} /></p>
<p class='option_text'><b>Host, nslookup, and dig are quick, and return meaningful error codes.&nbsp; They also require shell access to the server, and may or may not be available on all systems.&nbsp; Host and nslookup are coded with three second timeouts, limiting hangs.&nbsp; On the other hand, gethostbyaddr() and dns_get_record() are standard PHP functions and should work on most systems, but do not have timeouts and do not give reasons when they fail to return the requested data.&nbsp; The dns_get_record() function is only available with PHP v. 5.0, and only available on Windows systems running PHP v. 5.3.</b></p><br /><br />
</div>

<h3>Anti-Maleagant Scoring</h3>
<div class='option_form_box'>
<p><b>A total score of 10 blocks the visitor</b></p>
<p>Blank/Invalid IP: <input type="text" size="2" name="mal_ip" value="{$this->options["mal_ip"]}" />&nbsp;&nbsp;&nbsp;
Empty User Agent: <input type="text" size="2" name="empty_ua" value="{$this->options["empty_ua"]}" />&nbsp;&nbsp;&nbsp;
Empty Referrer String: <input type="text" size="2" name="empty_ref" value="{$this->options["empty_ref"]}" /></p>
<p>Req. URI with 'lostpassword': <input type="text" size="2" name="lostpassword" value="{$this->options["lostpassword"]}" />&nbsp;&nbsp;&nbsp;
<!-- Failed Login: <input type="text" size="2" name="login" value="{$this->options["login"]}" />&nbsp;&nbsp;&nbsp; -->
Req. URI with 'trackback': <input type="text" size="2" name="trackback" value="{$this->options["trackback"]}" /></p>

<p><b>Login Locker</b></p>
<p class='option_text'><b>Block excess *failed* Login attempts.&nbsp; Set "Allow" parameter to zero to disable.&nbsp; For something different, set the "Allow" parameter to, say, 10, but then set the third parameter (blocking minutes) to 0 (zero).&nbsp; Then set the second parameter to, say, 1440, which is the number of minutes in a day.&nbsp; With 0 as the third parameter, the program will calculate the blocking period on a sliding scale, based on the time it took the visitor to execute X number of failed Logins.&nbsp; With 1440 as an example, a visitor executing 10 failed Logins in 15 minutes or less will be blocked for 15 minutes, an amount that will increase up to 12 hours blocked for 10 failed logins in 1440 minutes.</b></p>
<p>Allow <input type="text" size="2" name="login_limit" value="{$this->options["login_limit"]}" /> Failed LOGIN attempts within <input type="text" size="2" name="login_window" value="{$this->options["login_window"]}" /> minutes before blocking the IP for <input type="text" size="2" name="login_lock_duration" value="{$this->options["login_lock_duration"]}" /> minutes.</p>
<p><b>Domain Checker</b></p>
<p class='option_text'><b>If "Domain Check" is enabled, and the score already accumulated equals or is greater than the "Domain Check At" value, a reverse DNS lookup will be performed, using the method designated in the "Reverse IP Lookup Method" option section just above.&nbsp; If the server returns an error ("timed out", NXDOMAIN, SERVFAIL, etc.), the "Domain Check Score" will be added to the existing score.</b></p><br />
<p class='option_text'><b>If "Domain Check" is enabled and "Domain Check At" is set to 0 (zero), the reverse IP lookup will be performed at every hit, rather than in bulk later when, say, the Sequential View page runs, thereby spreading the costs of Domain lookup to the many visitors, rather than lumping them on the poor administrator.</b></p>
<p><input type="checkbox" name="dom_check" value="1" {$checked[$this->options["dom_check"]]} /> Domain Check&nbsp; &nbsp; &nbsp; &nbsp;Domain Check At: <input type="text" size="2" name="dom_check_at" value="{$this->options["dom_check_at"]}" />&nbsp;&nbsp;&nbsp;Domain Check Score: <input type="text" size="2" name="dom_check_score" value="{$this->options["dom_check_score"]}" /></p>
<p><b>Way Too Fast</b></p>
<p class='option_text'><b>The WTF (Way Too Fast) routine is designed to block ill-mannered bots, iller-mannered scrapers, and to inhibit near-simultaneous, repeat login attempts.&nbsp; When WTF is enabled, the program examines the recent history (if any) of the visitor, and if that history is equal to or greater than the "WTF sample" size, and if all respective hits in that sample occured at an AVERAGE "WTF seconds" per hit, the visitor is blocked.&nbsp; For instance, with the default "WTF sample" of 10 and "WTF seconds" at 2, a visitor would need to make ten accesses in less than twenty seconds to be blocked.&nbsp; Further, once a visitor has earned the WTF block, any subsequent visit within fifteen minutes of their last access will also be blocked.&nbsp; Visitors will be unblocked if they wait at least fifteen minutes before trying again.&nbsp; Use judiciously.</b></p>
<p><input type="checkbox" name="wtf" value="1" {$checked[$this->options["wtf"]]} /> Enable WTF&nbsp; &nbsp; &nbsp; &nbsp;WTF sample: <input type="text" size="2" name="wtf_x" value="{$this->options["wtf_x"]}" /> (range 2 - 99)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;WTF seconds: <input type="text" size="2" name="wtf_secs" value="{$this->options["wtf_secs"]}" /> (range 1 - 8)</p>
</div><br />

<input type="submit" name="esubmit" value="Save ecSTATic Changes" />
<input type="hidden" name="ecstatit" value="ecstatic_options" />
</form>
</div><br /><br /><br /><br />

XXX;
} //option_form

/***********************************************************************************/
function __construct() {
parent::__construct();
ecstatic_ecstatic($this, "Settings"); //logo and page name
} //ecstatic_options
} //class ecstatic_options

/***********************************************************************************/
class ecstatic_export_table {
private $file_name = "";
private $xml = "";

/***********************************************************************************/
function export_dialogue() {
$size = strlen($this->xml);
header("Pragma: public");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Cache-Control: public");
header("Content-Description: File Transfer");
header("Content-Type: text/xml");
header("Content-Disposition: attachment; filename=$this->file_name");
header("Content-Transfer-Encoding: binary");
header("Content-Length: {$size}");
echo $this->xml;
exit();
} //export_dialogue

/***********************************************************************************/
function mysql_to_xml($ecstatic, $table) {
global $wpdb;
$namebit = array("spider"=>1, "kill"=>2, "nolog"=>4, "wlist"=>8, "xwlist"=>16);
$wnks = 0;
if ($table == "aux_lists") {
	$table = date("Ymd", $ecstatic->datetime) . "_" . $table;
	foreach ($namebit as $key=>$nb) {
		if (isset($_POST[$key]) AND $_POST[$key] == $nb) {
			$wnks |= $nb;
			$table .= "_" . $key;
			}
		}
	$table_name = $ecstatic->make_table("aux_lists");
	if (isset($_POST["xor"]) AND $_POST["xor"] == "0")
		$weQ = "SELECT * FROM $table_name WHERE wnks & $wnks ORDER BY name ASC";
	elseif (isset($_POST["xor"]) AND $_POST["xor"] == "1") {
		$weQ = "SELECT * FROM $table_name WHERE wnks = $wnks ORDER BY name ASC";
		$table .= "_" . "XOR";
		}
	}
else {
	$table_name = $ecstatic->make_table($table);
	$weQ = "SELECT * FROM $table_name ORDER BY name ASC";
	}

$txml = "";
$row = $col = array();
$res = mysql_query("SHOW COLUMNS FROM $table_name");

$this->xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
$this->xml .= "<!DOCTYPE table [\n";
$this->xml .= "<!ELEMENT table (row+)>\n";
$this->xml .= "<!ELEMENT row (";

while ($cols = mysql_fetch_assoc($res)) {
	if ($cols["Field"] == "id" OR $cols["Field"] == "lastseen" OR $cols["Field"] == "hits") //fields not exported
		continue;
	if (isset($col[0]))
		$this->xml .= ", ";
	$col[] = $cols["Field"]; //case sensitive
	$this->xml .= $cols["Field"];
	$txml .= "<!ELEMENT {$cols["Field"]} (#PCDATA)>\n";
	}
$this->xml .= ")>\n";
$this->xml .= $txml;
$this->xml .= "]>\n";
$this->xml .= "<table>\n";
if ($row = $wpdb->get_results($weQ, ARRAY_A)) { //gotta go old school
	foreach($row as $r) {
		$this->xml .= "<row>";
		foreach($col as $c) {
			if ($c == "wnks" AND $wnks)
				$r[$c] &= $wnks;
			$this->xml .= "<" . $c . ">" . str_replace("&", "&amp;", $r[$c]) . "</" . $c . ">";
			}
		$this->xml .= "</row>\n";
		}
	}
$this->xml .= "</table>\n";

$this->file_name = $table . ".xml";
$this->export_dialogue();
} //mysql_to_xml
} //class ecstatic_export_table

/****************************************************/
class ecstatic_export_ignored_ids {
private $file_name = "";
private $ids = "";

/***********************************************************************************/
function export_dialogue() {
$size = strlen($this->ids);
header("Pragma: public");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Cache-Control: public");
header("Content-Description: File Transfer");
header("Content-Type: text/php");
header("Content-Disposition: attachment; filename=$this->file_name");
header("Content-Transfer-Encoding: binary");
header("Content-Length: {$size}");
echo $this->ids;
exit();
} //export_dialogue

/****************************************************/
function ignored_ids() {
$ignored_ids = get_option("ecstatic_ignored_ids");
$this->ids = "<?php\n\$ignored_ids = array(\n";
$c = 0; //comma control
foreach($ignored_ids as $token => $set) {
	if ($c++)
		$this->ids .= ",";
	$this->ids .= "'{$token}' => '1'\n";
	}
$this->ids .= ");\nupdate_option('ecstatic_ignored_ids', \$ignored_ids);\n?>";
$this->file_name = "ecstatic_ignored_ids.php";
$this->export_dialogue();
} //save_ids
} //ecstatic_export_ignored_ids

/****************************************************/
class ecstatic_ignored_ids extends ecstatic {
/****************************************************/
function ignored_ids() {
global $wpdb;
$igs = $not_igs = array();
$ignored_ids = get_option("ecstatic_ignored_ids");
include('ecstatic_ua_parser.php');
$ua_parser = new USER_AGENT();
$u = $this->iurr_tables["ua"];
$rq = "SELECT ua FROM $u";
$uas = $wpdb->get_results($rq);
foreach($uas as $ua) {
	$rob = $ua_parser->load_from_string($ua->ua);
	foreach($rob->ua_ids as $key => $val) {
		$key = strtolower($key);
		if (isset($ignored_ids[$key]))
			$igs[$key]++;
		else
			$not_igs[$key]++;
		}
	}
$found = sizeof($igs);
arsort($igs);
arsort($not_igs);
foreach($ignored_ids as $i => $g) { //catch ignored_ids not in any user agents
	if (!isset($igs[$i]))
		$igs[$i] = 0;
	}
echo "<table class='ignored_ids' summary='ignored ids'><caption>Found " . $found . " (out of " . sizeof($igs) . ") ignored_ids tokens in " . sizeof($uas) . " User Agents in User Agent table</caption><tr><th>Token: num</th><th>Token: num</th><th>Token: num</th><th>Token: num</th><th>Token: num</th><th>Token: num</th></tr>\n<tr>";
$r = 0;
foreach($igs as $i => $g) {
	echo "<td>" . $i . ": " . $g . "</td>";
	if (++$r > 5) {
		$r = 0;
		echo "</tr>\n<tr>";
		}
	}
echo "</tr></table>";

echo "<div style='width:50%;margin:auto;'><form id='saveids' method='post' action=''>";
echo "<input type='hidden' name='ecstatit' value='export_ignored_ids' /><input type='submit' name='wesave' value='Export (to include with ecSTATic distributions)' />";
echo "</form></div>";

echo "<table class='ignored_ids' summary='not ignored ids'><caption>" . sizeof($not_igs) . " UA tokens found in " . sizeof($uas) . " User Agents in User Agent table, but NOT in Ignored IDs array</caption><tr><th>Token: num</th><th>Token: num</th><th>Token: num</th><th>Token: num</th><th>Token: num</th><th>Token: num</th></tr>\n<tr>";
$r = 0;
foreach($not_igs as $i => $g) {
	echo "<td>" . $i . ": " . $g . "</td>";
	if (++$r > 5) {
		$r = 0;
		echo "</tr>\n<tr>";
		}
	}
echo "</tr></table>";
} //ignored_ids
} //ecstatic_ignored_ids

/****************************************************/
class ecstatic_formproc extends ecstatic {

/****************************************************/
function sanctify_textboxes($arg, $min, $max, $default) { //called by play_change, process_options_changes
$test = $_POST[$arg];
if ($test < $min or $test > $max)
	$test = $default;
return $test;
} //sanctify_textboxes

/***********************************************************************************/
function sanctify_checkboxes($arg) { //called by process_options_changes
$test = $_POST[$arg];
if ($test != 0)
	$test = 1;
return $test;
} //sanctify_checkboxes

/***********************************************************************************/
function sanctify_select($arg, $default) { //called by process_options_changes
$test = $_POST[$arg];
if ($test != "reg" AND $test != "feed" AND $test != "bot" AND $default == "reg")
	$test = "reg";
elseif ($test != "reg" AND $test != "feed" AND $test != "bot" AND $default == "feed")
	$test = "feed";
elseif ($test != "reg" AND $test != "feed" AND $test != "bot" AND $default == "bot")
	$test = "bot";
elseif ($test != "reg" AND $test != "feed" AND $test != "bot")
	$test = "reg";
return $test;
} //sanctify_select

/***********************************************************************************/
function process_option_changes() {
global $wpdb;

$showbannergraph = $this->sanctify_checkboxes("showbannergraph");
$showgraphregi = $this->sanctify_checkboxes("showgraphregi");
$showgraphregp = $this->sanctify_checkboxes("showgraphregp");
$showgraphfeedi = $this->sanctify_checkboxes("showgraphfeedi");
$showgraphfeedp = $this->sanctify_checkboxes("showgraphfeedp");
$showgraphboti = $this->sanctify_checkboxes("showgraphboti");
$showgraphbotp = $this->sanctify_checkboxes("showgraphbotp");
$showreg = $this->sanctify_checkboxes("showreg");
$showfeed = $this->sanctify_checkboxes("showfeed");
$showbot = $this->sanctify_checkboxes("showbot");
$collectloggeduser = $this->sanctify_checkboxes("collectloggeduser");
$logsubadmin = $this->sanctify_checkboxes("logsubadmin");
$dom_check = $this->sanctify_checkboxes("dom_check");
$wtf = $this->sanctify_checkboxes("wtf");
$block_new_bots = $this->sanctify_checkboxes("block_new_bots");
$skip_rip = $this->sanctify_checkboxes("skip_rip");
$stop_popups = $this->sanctify_checkboxes("stop_popups");
$plain_ruris = $this->sanctify_checkboxes("plain_ruris");
$manual_purge = $this->sanctify_checkboxes("manual_purge");
$iporcidr = $this->sanctify_checkboxes("iporcidr");

$daystograph = $this->sanctify_textboxes("daystograph", 7, 90, $this->option_defaults["daystograph"]);
$purgeolderthan = $this->sanctify_textboxes("purgeolderthan", 0, 365, $this->option_defaults["purgeolderthan"]);
$purgerssolderthan = $this->sanctify_textboxes("purgerssolderthan", 0, 365, $this->option_defaults["purgerssolderthan"]);
$purgebotolderthan = $this->sanctify_textboxes("purgebotolderthan", 0, 365, $this->option_defaults["purgebotolderthan"]);
$daystoshow = $this->sanctify_textboxes("daystoshow", .04, 255, $this->option_defaults["daystoshow"]);
$maxtoshow = $this->sanctify_textboxes("maxtoshow", 1, 9999, $this->option_defaults["maxtoshow"]);
$newvisitorminutes = $this->sanctify_textboxes("newvisitorminutes", 1, 1440, $this->option_defaults["newvisitorminutes"]);
$dom_check_at = $this->sanctify_textboxes("dom_check_at", 0, 10, $this->option_defaults["dom_check_at"]);
$wtf_x = $this->sanctify_textboxes("wtf_x", 2, 99, $this->option_defaults["wtf_x"]);
$wtf_secs = $this->sanctify_textboxes("wtf_secs", 1, 8, $this->option_defaults["wtf_secs"]);
$stop_popups_beyond = $this->sanctify_textboxes("stop_popups_beyond", 0, 5000, $this->option_defaults["stop_popups_beyond"]);
$no_lowscore_popups = $this->sanctify_textboxes("no_lowscore_popups", 0, 40, $this->option_defaults["no_lowscore_popups"]);

$mal_ip = $this->sanctify_textboxes("mal_ip", 0, 10, $this->option_defaults["mal_ip"]);
$empty_ua = $this->sanctify_textboxes("empty_ua", 0, 10, $this->option_defaults["empty_ua"]);
$empty_ref = $this->sanctify_textboxes("empty_ref", 0, 10, $this->option_defaults["empty_ref"]);
$trackback = $this->sanctify_textboxes("trackback", 0, 10, $this->option_defaults["trackback"]);
$lostpassword = $this->sanctify_textboxes("lostpassword", 0, 10, $this->option_defaults["lostpassword"]);
$dom_check_score = $this->sanctify_textboxes("dom_check_score", 0, 10, $this->option_defaults["dom_check_score"]);
$login_limit = $this->sanctify_textboxes("login_limit", 0, 32, $this->option_defaults["login_limit"]);
$login_window = $this->sanctify_textboxes("login_window", 0, 43200, $this->option_defaults["login_window"]);
$login_lock_duration = $this->sanctify_textboxes("login_lock_duration", 0, 43200, $this->option_defaults["login_lock_duration"]);

$panel1 = $this->sanctify_select("panel1", "reg");
$panel2 = $this->sanctify_select("panel2", "feed");
$panel3 = $this->sanctify_select("panel3", "bot");

$topentriestoshow = $this->sanctify_textboxes("topentriestoshow", 0, 255, $this->option_defaults["topentriestoshow"]);
$graphsort = $this->sanctify_checkboxes("graphsort");
$noprefetch = $this->sanctify_checkboxes("noprefetch");
$enablewidget = $this->sanctify_checkboxes("enablewidget");
$dom_method = $this->sanctify_textboxes("dom_method", 1, 16, $this->option_defaults["dom_method"]);
$anonref = $this->sanctify_textboxes("anonref", 0, 5, $this->option_defaults["anonref"]);

$enable_email = $this->sanctify_checkboxes("enable_email");
$email_every = $this->sanctify_textboxes("email_every", 1, 365, $this->option_defaults["email_every"]);
$email_time = $this->sanctify_textboxes("email_time", 0, 24, $this->option_defaults["email_time"]);
if ($enable_email) {
	if ($email_time != $this->options["email_time"] OR $email_every != $this->options["email_every"])
		$next_email_time = mktime($email_time, 0, 0, date("m"), date("d")+$email_every, date("Y"));
	else
		$next_email_time = $this->options["estats"];
	}
else
	$next_email_time = "0";

$qed = $wpdb->query($wpdb->prepare("UPDATE $this->options_table SET showbannergraph=%d, showgraphregi=%d, showgraphregp=%d, showgraphfeedi=%d, showgraphfeedp=%d, showgraphboti=%d, showgraphbotp=%d, showreg=%d, showfeed=%d, showbot=%d, daystograph=%d, purgeolderthan=%d, purgerssolderthan=%d, purgebotolderthan=%d, daystoshow=%s, collectloggeduser=%d, newvisitorminutes=%d, panel1=%s, panel2=%s, panel3=%s, mal_ip=%d, empty_ua=%d, empty_ref=%d, trackback=%d, lostpassword=%d, topentriestoshow=%d, graphsort=%d, maxtoshow=%d, noprefetch=%d, enablewidget=%d, logsubadmin=%d, dom_check=%d, dom_check_at=%d, dom_check_score=%d, dom_method=%d, wtf=%d, wtf_x=%d, wtf_secs=%d, block_new_bots=%d, skip_rip=%d, stop_popups=%d, stop_popups_beyond=%d, no_lowscore_popups=%d, estats=%s, login_limit=%d, login_window=%d, login_lock_duration=%d, plain_ruris=%d, manual_purge=%d, iporcidr=%d, anonref=%d", $showbannergraph, $showgraphregi, $showgraphregp, $showgraphfeedi, $showgraphfeedp, $showgraphboti, $showgraphbotp, $showreg, $showfeed, $showbot, $daystograph, $purgeolderthan, $purgerssolderthan, $purgebotolderthan, $daystoshow, $collectloggeduser, $newvisitorminutes, $panel1, $panel2, $panel3, $mal_ip, $empty_ua, $empty_ref, $trackback, $lostpassword, $topentriestoshow, $graphsort, $maxtoshow, $noprefetch, $enablewidget, $logsubadmin, $dom_check, $dom_check_at, $dom_check_score, $dom_method, $wtf, $wtf_x, $wtf_secs, $block_new_bots, $skip_rip, $stop_popups, $stop_popups_beyond, $no_lowscore_popups, $next_email_time, $login_limit, $login_window, $login_lock_duration, $plain_ruris, $manual_purge, $iporcidr, $anonref));

$mdate = $this->sanctify_checkboxes("mdate");
$mip = $this->sanctify_checkboxes("mip");
$mipq = $this->sanctify_checkboxes("mipq");
$mbrowser = $this->sanctify_checkboxes("mbrowser");
$mos = $this->sanctify_checkboxes("mos");
$mruri = $this->sanctify_checkboxes("mruri");
$mref = $this->sanctify_checkboxes("mref");
$mscore = $this->sanctify_checkboxes("mscore");
$mlinks = $this->sanctify_checkboxes("mlinks");
$ttt = $this->sanctify_checkboxes("testtesttest");
$this->estats_table = $this->make_table("estats");
$qr = $wpdb->query($wpdb->prepare("UPDATE $this->estats_table SET email_every=%d, email_time=%d, addys=%s, subject=%s, xheader=%s, mdate=%d, mip=%d, mipq=%d, mbrowser=%d, mos=%d, mruri=%d, mref=%d, mscore=%d, mlinks=%d", $email_every, $email_time, $_POST["emailaddys"], $_POST["subject"], $_POST["xheader"], $mdate, $mip, $mipq, $mbrowser, $mos, $mruri, $mref, $mscore, $mlinks));
$qed |= $qr;
if ($ttt == 1) { //send test email
	$ecstatic = $this; //Quelle wot!
	include_once(WP_PLUGIN_DIR . "/" . dirname(plugin_basename(__FILE__)) . '/ecstatic_estats.php');
	}

wp_redirect($this->url . "/wp-admin/admin.php?page=ecstatic_options&submit=$qed");
exit();
} //process_option_changes

/***********************************************************************************/
function reset_bot_aux($token, $type) { //called by play_change and wnksplay
global $wpdb;
$table = $this->iurr_tables[$type];
switch ($type) {
	case "ip":
		$octpart = "";
		$octets = explode(".", $token);
		$p = 0;
		foreach ($octets as $octet) {
			if (!preg_match("/[-?*]/", $octet)) {
				$octpart .= $octet;
				if ($p++ < 3)
					$octpart .= ".";
				}
			else
				break;
			}
		$wpdb->query($wpdb->prepare("UPDATE $table SET aux=aux & 0x4 WHERE ip LIKE %s", $octpart . "%")); //preserve the 0x4 bit, if set
		break;
	case "ua":
		$uabots = $wpdb->get_results("SELECT id, ua FROM $table WHERE aux=2");
		foreach ($uabots as $uabot) {
			$squash = str_replace(" ", "", strtolower($uabot->ua));
			if (strpos($squash, $token) !== false)
				$wpdb->query($wpdb->prepare("UPDATE $table SET aux=0 WHERE id=%d", $uabot->id));
			}
		break;
	case "ref": //never used, but here, anyway
		$wpdb->query($wpdb->prepare("UPDATE $table SET aux=0 WHERE ref LIKE %s", "%". $token . "%"));
		break;
	} //switch
} //reset_bot_aux

/***********************************************************************************/
function play_change() {
global $wpdb;
$dummy_array = array();  //kluge for call to ip_ranges

$status = 0;
$imash = $_POST["imash"];
$pressed = $_POST["play_change"];

if (strpos($pressed, "Token") !== false) { //add to aux_lists table
	$status_string = array("Entry success", "Entry failed", "Token/Type already exists.  Name updated.", "Failed:&nbsp; At least one Add checkbox must be checked");
	$wnks = 0;
	if (isset($_POST["spbo"]))
		$wnks |= 1;
	if (isset($_POST["kill"]))
		$wnks |= 2;
	if (isset($_POST["nolog"]))
		$wnks |= 4;
	if (isset($_POST["wlist"]))
		$wnks |= 8;

	$type = $_POST["cat"];
	if ($type == "ip" OR $type == "ua" OR $type == "ref" OR $type == "ruri") {
		$token = $_POST[$type];
		if ($type == "ua")
			$token = str_replace(" ", "", strtolower($token));

		elseif ($type == "ip" AND strpos($token, "/") !== false AND !$this->options["iporcidr"]) { //someone sent a CIDR range into the mix when ectatic notation (vs CIDR) is set - convert it
			include(WP_PLUGIN_DIR . "/" . dirname(plugin_basename(__FILE__)) . "/ecstatic_whois.php"); //holds our rarely used class
			$nr = new ipranges("0.0.0.0", $dummy_array, 0); //dummy values for class constructor
			$token = $nr->CIDR2NetRange($token);
			}

		$name = $_POST["comname"];
		if ($id = $wpdb->get_var($wpdb->prepare("SELECT id FROM $this->aux_lists_table WHERE token=%s and type=%s", $token, $type))) {
			$status = 2;
			$wpdb->query($wpdb->prepare("UPDATE $this->aux_lists_table SET name=%s WHERE id=%d", $name, $id));
			}
		else {
			if ($wpdb->query($wpdb->prepare("INSERT INTO $this->aux_lists_table (id, type, token, name, lastseen, wnks, hits) VALUES (null, %s, %s, %s, %d, %d, %d)", $type, $token, $name, $this->datetime, $wnks, 0))) {
				if ($wnks & 1) //reset aux categorization (0=unknown, 1=non-bot, 2=bot) in ua, ip, and ref tables
					$this->reset_bot_aux($token, $type);
				}
			else
				$status = 1;
			}
		}
	}
elseif (strpos($pressed, "Score") !== false) {
	$status_string = array("Update FAIL", "Update Success", "Score rejected. To Score empty UAs and Referrers visit the Options page.");
	$tab = $_POST["tab"]; //ip, ua, ref, ruri
	$idx = $_POST["idx"];
	if (is_numeric($idx) AND isset($this->iurr_tables[$tab])) {
		$table = $this->iurr_tables[$tab];
		$parm = $wpdb->get_var($wpdb->prepare("SELECT $tab FROM $table WHERE id=%d", $idx));
		if (strlen($parm) != 0) { //don't assign scores to empty ua, empty ref, or empty ruri
			$score = $this->sanctify_textboxes("score", 0, 10, 0); //sanitize the data - parm, min, max, default
			$maybe = $wpdb->query($wpdb->prepare("UPDATE $table SET score=%d WHERE id=%d", $score, $idx));
			$status |= $maybe;
			}
		else
			$status = 2;
		}
	} //elseif
$status = urlencode($status_string[$status]);
wp_redirect($this->url . "/wp-admin/admin.php?page=ecstatic_mash&imash={$imash}&status={$status}");
exit();
} //play_change

/***********************************************************************************/
function wnksplay() {
global $wpdb;
$namebit = array("spider"=>1, "kill"=>2, "nolog"=>4, "wlist"=>8, "xwlist"=>16);

if (isset($_POST["id"])) {
	$id = $_POST["id"];
	$id = str_replace("n", "", $id); //fix faked id needed to fool jQuery javascript routines
	if (!is_numeric($id) OR $id < 1) {
		echo "#ERR - id not numeric or out of range: " . $id;
		return;
		}
	}
else {
	echo "#ERR - id not set.";
	return;
	}

if (isset($_POST["wnks"]) AND $_POST["wnks"] == "doDelete") {
	$wpdb->query($wpdb->prepare("DELETE FROM $this->aux_lists_table WHERE id=%d LIMIT 1", $id));
	echo "<td>!Deleted!</td><td>{$_POST['token']}</td><td>{$_POST['type']}</td><td></td><td></td><td>{$_POST['spider']}</td><td>{$_POST['kill']}</td><td>{$_POST['nolog']}</td><td>{$_POST['wlist']}</td><td>{$_POST['xwlist']}</td>";
	$this->reset_bot_aux($_POST['token'], $_POST['type']);
	return;
	} //doDelete
//Save falls through here
$updated_row = "";
if (isset($_POST["name"])) {
	$name = $_POST["name"];
	if (strlen($name) < 1 OR strlen($name) > 255) {
		echo "#ERR - 'name' invalid, missing, or too long.\n";
		return;
		}
	$updated_row .= "<td>$name</td>";
	}
if (isset($_POST["token"])) {
	$token = $_POST["token"];
	if (strlen($token) < 1 OR strlen($token) > 255) {
		echo "#ERR - 'token' invalid, missing, or too long.\n";
		return;
		}
	$updated_row .= "<td>$token</td>";
	}
if (isset($_POST["type"])) {
	$type = $_POST["type"];
	if ($type != "ua" AND $type != "ip" AND $type != "ref" AND $type != "ruri" AND $type != "mix") {
		echo "#ERR - invalid 'type'\n";
		return;
		}
	$updated_row .= "<td>$type</td>";
	}
$updated_row .= "<td>{$_POST["lastseen"]}</td>";
$updated_row .= "<td>{$_POST["count"]}</td>";
$wnks = 0;
foreach ($namebit as $key=>$nb)
	if (isset($_POST[$key]) AND $_POST[$key] == "true") {
		$wnks |= $nb;
		$updated_row .= "<td>X</td>";
		}
	else
		$updated_row .= "<td></td>";
if (!$wnks) {
	echo "#ERR - At least one of the flags must be set:\nBot Kill NoShow Whitelist XWhitelist\n";
	return;
	}
if ($wpdb->get_var($wpdb->prepare("SELECT count(*) FROM $this->aux_lists_table WHERE token=%s AND type=%s AND id!=%d", $token, $type, $id))) {
	echo "#ERR - token and type already exist in another record\n";
	return;
	}
else {
	$z = $wpdb->get_row($wpdb->prepare("SELECT lastseen, hits FROM $this->aux_lists_table WHERE id=%d", $id)); //in case of activity
	$wpdb->query($wpdb->prepare("UPDATE $this->aux_lists_table SET name=%s, token=%s, type=%s, lastseen=%d, wnks=%d, hits=%d WHERE id=%d", $name, $token, $type, $z->lastseen, $wnks, $z->hits, $id));
	$this->reset_bot_aux($token, $type);
	echo $updated_row;
	}
} //wnksplay

/***********************************************************************************/
function form_processor() {
global $wpdb;
switch ($_POST["ecstatit"]) {
	case "wnksplay":
		$this->wnksplay();
		return;
		break;
	case "Import":
		$errorno = $_FILES['importfile']['error'];
		$tmpfile = $_FILES['importfile']['tmp_name']; //path to temporary file on the server.
		$userfile = $_FILES['importfile']['name'];    //path of user uploaded file.
		$filename = WP_PLUGIN_DIR . "/" . dirname(plugin_basename(__FILE__)) . "/" . $userfile;
		if (move_uploaded_file($_FILES['importfile']['tmp_name'], $filename)) {
			include_once(WP_PLUGIN_DIR . "/" . dirname(plugin_basename(__FILE__)) . '/ecstatic_tables.php');
			ecstatic_xml_parser_loader($this, "aux_lists", $filename);
//////Need some feedback (success/failure) here, somewhere
			}
		else
			$fail = "Upload/Import Failed. (Userfile: {$userfile} -- Tmpfile: {$tmpfile} -- Destname: {$filename} -- Error#: {$errorno})";
		break;
	case "import_aux_se":
		include_once(WP_PLUGIN_DIR . "/" . dirname(plugin_basename(__FILE__)) . '/ecstatic_tables.php');
		$s = ecstatic_xml_parser_loader($this, "aux_se");
		break;
	case "Export":
		$export = new ecstatic_export_table();
		$export->mysql_to_xml($this, "aux_lists");
		break;
	case "export_aux_se":
		$export = new ecstatic_export_table();
		$export->mysql_to_xml($this, "aux_se");
		break;
	case "export_ignored_ids":
		$export = new ecstatic_export_ignored_ids();
		$export->ignored_ids();
		break;
	case "score":
	case "addspikillnolog":
		$this->play_change();
		break;
	case "ecstatic_options":
		$this->process_option_changes();
		break;
	case "serefferer_edit":
		$se_id = $_POST["ecstatse_id"];
		$imash = $_POST["imash"];
		$status = "pre";
		if (isset($_POST["ecstatname"]) AND isset($_POST["ecstattoken"]) AND isset($_POST["ecstatsigvar"])) {
			$name = $_POST["ecstatname"];
			$token = $_POST["ecstattoken"];
			$qsig = $_POST["ecstatsigvar"];
			if ($qsig == "none")
				$qsig = "";
			if (isset($_POST["ecstatpath"])) {
				if (isset($_POST["ecstatpath_text"])) {
					if ($_POST["ecstatpath_text"] != "") {
						$path_text = $_POST["ecstatpath_text"];
						if (!$qsig AND preg_match("/^(.*)\?([^=]+)/", $path_text, $match)) {
							$path_text = $match[1];
							$qsig = $match[2];
							}
						}
					else
						$path_text = "none";
					}
				}
			else
				$path_text = "";
			$table_name = $this->make_table("aux_se");
			$wpdb->query($wpdb->prepare("INSERT INTO $table_name VALUES (null, %s, %s, %s, %s)", $token, $name, $qsig, $path_text));
			$status = "maybe";
			}
		wp_redirect($this->url . "/wp-admin/admin.php?page=ecstatic_mash&se=$se_id&imash=$imash&status={$status}");
		exit();
		break;
	case "serefferer_delete":
		$fs = 0;
		$se_id = $_POST["se_id"];
		$imash = $_POST["imash"];
		if (isset($_POST["seref"])) {
			$idz = $_POST["seref"];
			$table_name = $this->make_table("aux_se");
			foreach ($idz as $id) {
				if (ctype_digit($id)) {
					$fs += $wpdb->query($wpdb->prepare("DELETE FROM $table_name WHERE id=%d", $id));
					}
				}
			}
		$status = "Deleted+{$fs}+record(s)";
		wp_redirect($this->url . "/wp-admin/admin.php?page=ecstatic_mash&se=$se_id&imash=$imash&status={$status}");
		exit();
		break;
/*
	case "manual_purge": //boy, is this convoluted
		$caller = $_POST["caller"];
		include_once(WP_PLUGIN_DIR . "/" . dirname(plugin_basename(__FILE__)) . '/ecstatic_interface.php');
		wp_redirect($this->url . "/wp-admin/admin.php?page=ecstatic_manual_purge&caller={$caller}");
		exit();
		break;
	case "continue": //continue after manual purge
		$caller = $_POST["continue"];
		wp_redirect($this->url . "/wp-admin/admin.php?page={$caller}");
		exit();
		break;
*/
	case "fixBrowsers": //find Unknowns and Special Cases - routine since moved to ecstatic_tables.php
		include_once(WP_PLUGIN_DIR . "/" . dirname(plugin_basename(__FILE__)) . '/ecstatic_ua_parser.php');
		$ua_parser = new USER_AGENT();
		$u = $this->iurr_tables["ua"];
		$qs = "SELECT id, ua, browser FROM $u WHERE aux=2 OR browser=%s";
		$rq = $wpdb->get_results($wpdb->prepare($qs, "Unknown"));
		foreach($rq as $r) {
			$rob = $ua_parser->load_from_string($r->ua);
			if ($r->browser != $rob->browser) {
				$qs = "UPDATE $u SET browser=%s WHERE id=%d";
				$wpdb->query($wpdb->prepare($qs, $rob->browser, $r->id));
				echo "Changed <span style='color:blue;'>'{$r->browser}'</span> to <span style='color:red;'>'{$rob->browser}'</span> for UA: $r->ua<br />";
				}
			else
				echo "<span style='font-size:small;'>Browser $r->browser was not changed.</span><br />";
			}
		echo "<br /><form id='toChart' method='post' action='../wp-admin/admin.php?page=ecstatic_charts'><input type='hidden' name='chart1' value='SE' /><input type='submit' name='toChart' value='Return to Chart' /></form>";
		exit();
		break;
	default:
		wp_redirect($this->url . "/wp-admin/admin.php?page=ecstatic/ecstatic_interface.php");
		exit();
		break;
	}
} //form_processor

/***********************************************************************************/
function __construct() {
parent::__construct();
} //ecstatic_formproc
} //class ecstatic_formproc
?>