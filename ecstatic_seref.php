<?php
/****************************************************/
class ecstatic_seref_seref extends ecstatic_seref {
private $se_id = 0;
private $imash = "";
private $se_ref = "";
private $id = 0;
private $name = "";
private $token = "";
private $path = "";
private $found_qsig = "";
private $found_qvar = "";
private $xhost = "";
private $xqsig = "";
private $xvar = "";
private $qparts = array();

/****************************************************/
function big_ref_table() {
echo "<div id='reefer'>\n";
echo "<form method='post' action=''>\n";
echo "<table id='reefer_table' class='sortable' summary='search engine signatures'>\n";
echo "<caption>Search Engine Signatures</caption>\n";
echo "<thead>\n";
echo "<tr><th>Name</th><th>Token</th><th>Path</th><th>Sig</th><th class='nosort'>";
echo "<input type='submit' name='serefferer_delete' value='Delete' alt='Delete from SE Referrer table' /></th></tr>\n";
echo "</thead>\n";
echo "<tbody>\n";
foreach($this->sez as $reef) {
	echo "<tr><td>$reef->name</td><td>$reef->token</td><td>$reef->path</td><td>$reef->qsig</td>";
	echo "<td style='text-align: center;'><input type='checkbox' name='seref[]' value='{$reef->id}' /></td></tr>\n";

	if (strpos($reef->token, "*"))
		$tk = "~" . str_replace("*", ".+", $reef->token) . "~";
	else
		$tk = "~" . $reef->token . "~";
	if (preg_match($tk, $this->se_ref)) {
		foreach ($this->qparts as $qsig => $qvar) {
			if ($qsig == $reef->qsig AND $qvar != "") {
				$this->id = $reef->id;
				$this->name = $reef->name;
				$this->token = $reef->token;
				$this->path = $reef->path;
				$this->found_qsig = $qsig;
				$this->found_qvar = htmlentities(stripslashes($qvar));
				break;
	}}}}
echo "</tbody>\n</table>\n";
echo "<input type='hidden' name='se_id' value='{$this->se_id}' />\n";
echo "<input type='hidden' name='imash' value='{$this->imash}' />\n";
echo "<input type='hidden' name='ecstatit' value='serefferer_delete' />\n";
echo "</form>\n";
echo "<div style='position: relative; float: left'><form name='import' method='post' action=''>\n";
echo "<input type='submit' name='import_aux_se' value='Import SES table from XML file' alt='Import SES table from XML file' />\n";
echo "<input type='hidden' name='ecstatit' value='import_aux_se' />\n";
echo "</form></div>\n";
echo "<div style='position: relative; float: right'><form name='export' method='post' action=''>\n";
echo "<input type='submit' name='export_aux_se' value='Export SES table to XML file' alt='Export SES table to XML file' />\n";
echo "<input type='hidden' name='ecstatit' value='export_aux_se' />\n";
echo "</form></div>\n";
echo "</div><!--reefer-->\n\n";

echo <<<XXX
<script type="text/javascript">
var sorter = new TINY.table.sorter("sorter");
sorter.init("reefer_table",0);
</script>


XXX;
} //big_ref_table

/****************************************************/
function echo_ref() {
//echo "<br /><b>Referrer #{$this->se_id}:</b>&nbsp; <strong style='color:blue'>" . urldecode($this->se_ref) . "</strong><br /><br />";
echo "<br /><b>Referrer #{$this->se_id}:</b>&nbsp; <strong style='color:blue'>" . $this->se_ref . "</strong><br /><br />";
} //echo_ref

/****************************************************/
function echo_name_token() {
echo "<p>The \"Name\" and \"Token\" fields, if filled, indicate that both a Token string and Sig variable, as defined in the SES table, were found in the Referrer string. </p>\n";
echo "Name: <input type='text' size='20' name='ecstatname' value='{$this->name}' />&nbsp; &nbsp; \n";
echo "Token: <input type='text' size='30' name='ecstattoken' value='{$this->token}' /><br />\n";
echo "<input type='hidden' name='ecstatse_id' value='{$this->se_id}' />\n";
echo "<input type='hidden' name='imash' value='{$this->imash}' />\n";
} //echo_name_token

/****************************************************/
function echo_testlink() {
echo "<p>Below is a test link built from the host URL, with associated or designated Path (if any), and a variable Sig as defined in the SES table, or as guessed at by ecSTATic.&nbsp; Changes to the form fields further below automatically build a new test link. </p>\n";
echo "<div id='testlink'>\n";
echo $this->refparts["scheme"] . "://" . $this->refparts["host"] . "\n";
echo "</div><!--testlink-->\n\n";
} //echo_testlink

/****************************************************/
function echo_ref_parts() {
echo "<p>The host URL and Path are taken from the Referrer string, unless the Path is overridden by an entry in the SES table. </p>\n";
$this->xhost = $this->refparts["scheme"] . "://" . $this->refparts["host"]; //scheme, host, port, user, pass, path, query, fragment
echo "<input type='checkbox' name='url' value='host' checked disabled /> URL: <a href='{$this->xhost}' title='Test the Link' target='blank'>{$this->xhost}</a><br />\n";
if ($this->path) {
	if ($this->path == "none")
		$this->path = "";
	echo "<input type='checkbox' name='ecstatpath' value='path' checked onClick='enable_text(this.checked)' /> Path: <input type='text' size='32' name='ecstatpath_text' value='{$this->path}' onKeyUp='build_testlink()' /><br />\n";
	}
else
	echo "<input type='checkbox' name='ecstatpath' value='path' checked onClick='enable_text(this.checked)' /> Path: <input type='text' size='32' name='ecstatpath_text' value='{$this->refparts["path"]}' onKeyUp='build_testlink()' /><br />\n";
} //echo_ref_parts

/****************************************************/
function echo_variable_pairs() {
echo "<p>The variable pairs extracted from the Referrer string, with a selection made based on either a \"Sig\" match in the SES table, or best guess by ecSTATic. </p>\n";
$k = 0;
foreach ($this->qparts as $qsig => $qvar) {
	if (!$k AND $qsig == $this->found_qsig AND $qvar == $this->found_qvar) {
		echo "<input type='radio' name='ecstatsigvar' value='{$qsig}' onClick='build_testlink()' checked /> " . $qsig . "=" . $qvar . "<br />\n";
		$this->xqsig = $qsig;
		$this->xvar = $qvar;
		$k++;
		}
	elseif(!$k AND preg_match('~^(q|p|w|s|su|ask|search|searchfor|query|key|keywords|buscar|qry|pesquisa|question|word)$~i', $qsig) AND $qvar != "") {
		echo "<input type='radio' name='ecstatsigvar' value='{$qsig}' onClick='build_testlink()' checked /> " . $qsig . "=" . $qvar . "<br />\n";
		$this->xqsig = $qsig;
		$this->xvar = $qvar;
		}
	else {
		echo "<input type='radio' name='ecstatsigvar' value='{$qsig}' onClick='build_testlink()' /> " . $qsig . "=" . $qvar;
		if ($qsig == "prev" AND (strpos($this->refparts["host"], "google")) !== false) //google kluge
			echo "&nbsp;&nbsp;  <--//parm within extracted below";
		echo "<br />\n";
		}
	echo "<input type='hidden' id='{$qsig}' name='{$qsig}' value='" . urlencode($qvar) . "' />\n";
	}
echo "<input type='radio' name='ecstatsigvar' value='none' onClick='build_testlink()' /> none of the above<br />\n";
echo "<input type='hidden' id='none' value='' />\n";
} //echo_variable_pairs

/****************************************************/
function echo_javascript() {
echo <<<XXX
<script type="text/javascript">
function build_testlink() {
	var jpath, qsig, qvar, juri;
	var jhost = "{$this->xhost}";
	var sep = "?";
	if (selinker.ecstatpath.checked) {
		jpath = selinker.ecstatpath_text.value;
		if (jpath.indexOf("?") != -1)
			sep = "&";
		}
	else
		jpath = "";
	for (var i=0; i<selinker.ecstatsigvar.length; i++) {
		if (selinker.ecstatsigvar[i].checked) {
			qsig = selinker.ecstatsigvar[i].value;
			qvar = document.getElementById(qsig).value;
			break;
			}
		}
	if (qsig == "none" || qsig == undefined)
		juri = jhost+jpath;
	else
		juri = jhost+jpath+sep+qsig+"="+qvar;
	document.getElementById('testlink').innerHTML = "<a href='" + juri + "' title='Test Link - Opens in New Window' target='_blank'>" + juri + "</a>";
}
function enable_text(status) {
	status=!status;
	document.selinker.ecstatpath_text.disabled = status;
	build_testlink();
}
build_testlink();
</script>

XXX;
} //echo_javascript

/****************************************************/
function echo_help() {
echo "<h3>Angles</h3>\n";
echo "<p>If all one is interested in is having Search Engine parameter strings showing nice in the Details pages, pick a proper Name, enter an appropriate identifying Token, select the correct Query Signature, and go.&nbsp; If, however, one wants to click on those search words to see the results of the search that led to one's web site, it can get a little trickier.&nbsp; It may take some experimenting to figure out how to reproduce some searches.&nbsp; Some engines may not allow users to bypass their front page. </p>\n";
echo "<p>Google Referrer strings can be complicated.&nbsp; Google image searches can go to both \"www.google.com/images\" and \"images.google.com/images\", but neither Referrer will show an \"/images\" Path.&nbsp; Google web search Referrers may have a \"/url\" or a \"/search\" or other path in them, but only \"/search\" works on the outward leg. </p>\n";
echo "<p>Yahoo can be similarly seemingly arbitrarily anti-perfunctory, but most engines are rather more well behaved. </p>\n";
echo "<h3>The Fields</h3>\n";
echo "<p>Tokens should be longer rather than shorter, but there's only so much host/path one can use.&nbsp; One may insert an asterik \"*\" in the token string to act as a wild card, forcing a regular expression match for the unspecified characters.&nbsp; The token \"google.*/search\" will match \"google.com/search\" and \"google.co.uk/search\".&nbsp; Even so, it may not be possible to match all possible permutations of some search engine referrer strings.&nbsp; Remember, a match is made when both a Token and Sig are found in the Referrer. </p>\n";
echo "<p>If the Path checkbox is unchecked, ie., not used, the Path in the Referrer string, if one exists, is used.&nbsp; If the Path checkbox is checked, but the Path text field is empty, ecSTATic assumes the user wants to override any existing Path string with a blank, and sends \"none\" to the pertinent Path field of the SES table.&nbsp; Later, when it reads the \"none\" from the table, the program is flagged to ignore the path in the associated Referrer string. </p>\n";
echo "<p>One may enter one's own sig/variable pairs in the Path text box, supplementing the single sig/var pair selectable via the radio buttons.&nbsp; For an example, see the Technorati entries in the SES table. </p>\n";
echo "<p>If that weren't enough, if one selects the \"none of the above\" radio button and then appends a parameter signature to the Path string, in the form of \"?arg=misc string\", ecSTATic will file the \"arg\" in the Sig field, with the Path text in the Path field, which might be useful in the unlikely eventuality that a Referrer does not contain the argument used when it conducts searches.&nbsp; If one wishes to go that route and still have \"none\" for a Path, as above, one has to fill the Path form field as \"none?arg=misc string\".&nbsp; The \"=\" and the \"misc string\" are optional. </p>\n";
} //echo_help

/****************************************************/
function __construct($ecstatic) {
global $wpdb;
$this->se_id = $_GET["se"];
$this->imash = $_GET["imash"];
$ref_table = $ecstatic->make_table("refs");
$se_count = $wpdb->get_var("SELECT count(*) FROM $ref_table");
if ($this->se_id < 1 OR !ctype_digit($this->se_id))
	die("Invalid se_id: {$this->se_id}");
else
	$this->se_ref = $wpdb->get_var("SELECT ref FROM $ref_table WHERE id={$this->se_id}");
$this->refparts = parse_url($this->se_ref);
if (array_key_exists("query", $this->refparts)) {
	$query = $this->refparts["query"];
	$this->qparts = $this->better_parse_str($query);
	}
parent::__construct($ecstatic); //call parent constructor - gargle aux_se db table into array
} //ecstatic_seref_seref
} //class ecstatic_seref_seref

/****************************************************/
class ecstatic_se_ref extends ecstatic {
/****************************************************/
function edit_se_ref() {
$reefer = new ecstatic_seref_seref($this); //load search engine signature strings
$reefer->echo_ref();
$reefer->big_ref_table();
echo "<div id=jaref_form>\n";
echo "<form name='selinker' method='post' action=''>\n";
$reefer->echo_name_token();
$reefer->echo_testlink();
$reefer->echo_ref_parts();
$reefer->echo_variable_pairs();
echo "<p>The \"Name\" and \"Token\" fields must be filled in, and one variable pair selected.&nbsp; The form <em>always</em> adds a new entry to the SES table, regardless of duplicates.</p>\n";
echo "<input type='submit' name='serefferer_edit' value='ADD to SES Referrer Table' alt='edit SES Referrer table' />\n";
echo "<input type='hidden' name='ecstatit' value='serefferer_edit' />\n";
echo "</form>\n\n";
$reefer->echo_help();
echo "</div><!--jaref_form-->\n\n";
$reefer->echo_javascript();
} //edit_se_ref

/****************************************************/
function __construct() {
parent::__construct();
ecstatic_ecstatic($this, "Search Engine Referrer Stylin'");
}
} //class ecstatic_se_ref
?>