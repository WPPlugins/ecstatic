<?php
/***********************************************************************************/
function ecstatic_create_table($ecstatic, $tana) {
global $wpdb;
//unsigned tinyint->255, smallint->65,535, mediumint->16,777,215, int->4,294,967,295, bigint->18,446,744,073,709,551,615
$tables = array("options" =>
"version TINYTEXT NOT NULL
,collectloggeduser TINYINT UNSIGNED NOT NULL DEFAULT '{$ecstatic->option_defaults["collectloggeduser"]}'
,newvisitorminutes SMALLINT UNSIGNED NOT NULL DEFAULT '{$ecstatic->option_defaults["newvisitorminutes"]}'
,purgeolderthan SMALLINT UNSIGNED NOT NULL DEFAULT '{$ecstatic->option_defaults["purgeolderthan"]}'
,purgerssolderthan SMALLINT UNSIGNED NOT NULL DEFAULT '{$ecstatic->option_defaults["purgerssolderthan"]}'
,purgebotolderthan SMALLINT UNSIGNED NOT NULL DEFAULT '{$ecstatic->option_defaults["purgebotolderthan"]}'
,daystoshow FLOAT NOT NULL DEFAULT '{$ecstatic->option_defaults["daystoshow"]}'
,daystograph TINYINT UNSIGNED NOT NULL DEFAULT '{$ecstatic->option_defaults["daystograph"]}'
,showreg TINYINT UNSIGNED NOT NULL DEFAULT '{$ecstatic->option_defaults["showreg"]}'
,showfeed TINYINT UNSIGNED NOT NULL DEFAULT '{$ecstatic->option_defaults["showfeed"]}'
,showbot TINYINT UNSIGNED NOT NULL DEFAULT '{$ecstatic->option_defaults["showbot"]}'
,showbannergraph TINYINT UNSIGNED NOT NULL DEFAULT '{$ecstatic->option_defaults["showbannergraph"]}'
,panel1 TINYTEXT NOT NULL
,panel2 TINYTEXT NOT NULL
,panel3 TINYTEXT NOT NULL
,showgraphregi TINYINT UNSIGNED NOT NULL DEFAULT '{$ecstatic->option_defaults["showgraphregi"]}'
,showgraphregp TINYINT UNSIGNED NOT NULL DEFAULT '{$ecstatic->option_defaults["showgraphregp"]}'
,showgraphfeedi TINYINT UNSIGNED NOT NULL DEFAULT '{$ecstatic->option_defaults["showgraphfeedi"]}'
,showgraphfeedp TINYINT UNSIGNED NOT NULL DEFAULT '{$ecstatic->option_defaults["showgraphfeedp"]}'
,showgraphboti TINYINT UNSIGNED NOT NULL DEFAULT '{$ecstatic->option_defaults["showgraphboti"]}'
,showgraphbotp TINYINT UNSIGNED NOT NULL DEFAULT '{$ecstatic->option_defaults["showgraphbotp"]}'
,mal_ip TINYINT UNSIGNED NOT NULL DEFAULT '{$ecstatic->option_defaults["mal_ip"]}'
,empty_ua TINYINT UNSIGNED NOT NULL DEFAULT '{$ecstatic->option_defaults["empty_ua"]}'
,empty_ref TINYINT UNSIGNED NOT NULL DEFAULT '{$ecstatic->option_defaults["empty_ref"]}'
,trackback TINYINT UNSIGNED NOT NULL DEFAULT '{$ecstatic->option_defaults["trackback"]}'
,lostpassword TINYINT UNSIGNED NOT NULL DEFAULT '{$ecstatic->option_defaults["lostpassword"]}'
,topentriestoshow TINYINT UNSIGNED NOT NULL DEFAULT '{$ecstatic->option_defaults["topentriestoshow"]}'
,maxtoshow SMALLINT UNSIGNED NOT NULL DEFAULT '{$ecstatic->option_defaults["maxtoshow"]}'
,graphsort TINYINT UNSIGNED NOT NULL DEFAULT '{$ecstatic->option_defaults["graphsort"]}'
,noprefetch TINYINT UNSIGNED NOT NULL DEFAULT '{$ecstatic->option_defaults["noprefetch"]}'
,enablewidget TINYINT UNSIGNED NOT NULL DEFAULT '{$ecstatic->option_defaults["enablewidget"]}'
,logsubadmin TINYINT UNSIGNED NOT NULL DEFAULT '{$ecstatic->option_defaults["logsubadmin"]}'
,dom_check TINYINT UNSIGNED NOT NULL DEFAULT '{$ecstatic->option_defaults["dom_check"]}'
,dom_check_at TINYINT UNSIGNED NOT NULL DEFAULT '{$ecstatic->option_defaults["dom_check_at"]}'
,dom_check_score TINYINT UNSIGNED NOT NULL DEFAULT '{$ecstatic->option_defaults["dom_check_score"]}'
,dom_method TINYINT UNSIGNED NOT NULL DEFAULT '{$ecstatic->option_defaults["dom_method"]}'
,wtf TINYINT UNSIGNED NOT NULL DEFAULT '{$ecstatic->option_defaults["wtf"]}'
,wtf_x TINYINT UNSIGNED NOT NULL DEFAULT '{$ecstatic->option_defaults["wtf_x"]}'
,wtf_secs TINYINT UNSIGNED NOT NULL DEFAULT '{$ecstatic->option_defaults["wtf_secs"]}'
,block_new_bots TINYINT UNSIGNED NOT NULL DEFAULT '{$ecstatic->option_defaults["block_new_bots"]}'
,skip_rip TINYINT UNSIGNED NOT NULL DEFAULT '{$ecstatic->option_defaults["skip_rip"]}'
,stop_popups TINYINT UNSIGNED NOT NULL DEFAULT '{$ecstatic->option_defaults["stop_popups"]}'
,stop_popups_beyond SMALLINT UNSIGNED NOT NULL DEFAULT '{$ecstatic->option_defaults["stop_popups_beyond"]}'
,no_lowscore_popups TINYINT UNSIGNED NOT NULL DEFAULT '{$ecstatic->option_defaults["no_lowscore_popups"]}'
,estats TINYTEXT NOT NULL
,login_limit TINYINT UNSIGNED NOT NULL DEFAULT '{$ecstatic->option_defaults["login_limit"]}'
,login_window SMALLINT UNSIGNED NOT NULL DEFAULT '{$ecstatic->option_defaults["login_window"]}'
,login_lock_duration SMALLINT UNSIGNED NOT NULL DEFAULT '{$ecstatic->option_defaults["login_lock_duration"]}'
,plain_ruris TINYINT UNSIGNED NOT NULL DEFAULT '{$ecstatic->option_defaults["plain_ruris"]}'
,manual_purge TINYINT UNSIGNED NOT NULL DEFAULT '{$ecstatic->option_defaults["manual_purge"]}'
,iporcidr TINYINT UNSIGNED NOT NULL DEFAULT '{$ecstatic->option_defaults["iporcidr"]}'
,anonref TINYINT UNSIGNED NOT NULL DEFAULT '{$ecstatic->option_defaults["anonref"]}'"

,"hits" =>
"datetime TINYTEXT NOT NULL
,ip INT UNSIGNED NOT NULL
,ua MEDIUMINT UNSIGNED NOT NULL
,ref MEDIUMINT UNSIGNED NOT NULL
,ruri MEDIUMINT UNSIGNED NOT NULL
,scorebits SMALLINT UNSIGNED NOT NULL DEFAULT '0'
,score TINYINT UNSIGNED NOT NULL DEFAULT '0'
,INDEX (ip), INDEX (ua), INDEX (ref), INDEX (ruri)"

,"ips" =>
"id INT UNSIGNED NOT NULL AUTO_INCREMENT
,ip TINYTEXT NOT NULL
,aux TINYINT UNSIGNED NOT NULL DEFAULT '0'
,score TINYINT UNSIGNED NOT NULL DEFAULT '0'
,domain TINYTEXT NOT NULL
,UNIQUE KEY id (id)"

,"user_agents" =>
"id MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT
,ua TEXT NOT NULL
,aux TINYINT UNSIGNED NOT NULL DEFAULT '0'
,browser TINYTEXT NOT NULL
,os TINYTEXT NOT NULL
,renderer TINYTEXT NOT NULL
,score TINYINT UNSIGNED NOT NULL DEFAULT '0'
,UNIQUE KEY id (id)"

,"refs" =>
"id MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT
,ref TEXT NOT NULL
,aux TINYINT UNSIGNED NOT NULL DEFAULT '0'
,score TINYINT UNSIGNED NOT NULL DEFAULT '0'
,UNIQUE KEY id (id)"

,"ruris" =>
"id MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT
,ruri TEXT NOT NULL
,aux MEDIUMINT UNSIGNED NOT NULL DEFAULT '0'
,score TINYINT UNSIGNED NOT NULL DEFAULT '0'
,UNIQUE KEY id (id)"

,"cumulative" =>
"day DATE NOT NULL
,regi MEDIUMINT UNSIGNED NOT NULL DEFAULT '0'
,regp MEDIUMINT UNSIGNED NOT NULL DEFAULT '0'
,feedi MEDIUMINT UNSIGNED NOT NULL DEFAULT '0'
,feedp MEDIUMINT UNSIGNED NOT NULL DEFAULT '0'
,boti MEDIUMINT UNSIGNED NOT NULL DEFAULT '0'
,botp MEDIUMINT UNSIGNED NOT NULL DEFAULT '0'"

,"aux_lists" =>
"id MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT
,type TINYTEXT NOT NULL
,token TINYTEXT NOT NULL
,name TINYTEXT NOT NULL
,lastseen INT NOT NULL
,wnks TINYINT UNSIGNED NOT NULL DEFAULT '0'
,hits INT UNSIGNED NOT NULL DEFAULT '0'
,UNIQUE KEY id (id)"

,"aux_se" =>
"id MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT
,token TINYTEXT NOT NULL
,name TINYTEXT NOT NULL
,qsig TINYTEXT NOT NULL
,path TINYTEXT NOT NULL
,UNIQUE KEY id (id)"

,"estats" =>
"email_every TINYINT UNSIGNED NOT NULL DEFAULT '1'
,email_time TINYINT UNSIGNED NOT NULL DEFAULT '0'
,addys TEXT NOT NULL
,subject TEXT NOT NULL
,xheader TEXT NOT NULL
,mdate TINYINT UNSIGNED NOT NULL DEFAULT '1'
,mip TINYINT UNSIGNED NOT NULL DEFAULT '1'
,mipq TINYINT UNSIGNED NOT NULL DEFAULT '1'
,mbrowser TINYINT UNSIGNED NOT NULL DEFAULT '1'
,mos TINYINT UNSIGNED NOT NULL DEFAULT '1'
,mruri TINYINT UNSIGNED NOT NULL DEFAULT '1'
,mref TINYINT UNSIGNED NOT NULL DEFAULT '1'
,mscore TINYINT UNSIGNED NOT NULL DEFAULT '1'
,mlinks TINYINT UNSIGNED NOT NULL DEFAULT '1'"
);
$table_name = $wpdb->prefix . "ecstatic_" . $tana;
$createtablestring = "CREATE TABLE " . $table_name . "(" . $tables[$tana] . ") ENGINE = MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
$wpdb->query($createtablestring);
if ($tana == "user_agents") //insert inevitable blank user_agent marked as bot (2) which saves an "if" on every hit forever
	$wpdb->query($wpdb->prepare("INSERT INTO $table_name VALUES (null, %s, %d, %s, %s, %s, %d)", "", 2, "(empty)", "(empty)", "(empty)", 0));
if ($tana == "refs") //insert inevitable blank ref marked as blank (aux=3) which saves mulitple "if"s later
	$wpdb->query($wpdb->prepare("INSERT INTO $table_name VALUES (null, %s, %d, %d)", "", 3, 0)); //id, ref, aux, score
} //ecstatic_create_table

/***********************************************************************************/
function ecstatic_populate_table($ecstatic, $table) {
global $wpdb;
switch ($table) {
	case "aux_se":
	case "aux_lists":
		ecstatic_xml_parser_loader($ecstatic, $table);
		break;
	case "options":
		$table_name = $wpdb->prefix . "ecstatic_" . $table;
		$wpdb->query($wpdb->prepare("INSERT INTO $table_name (version, panel1, panel2, panel3, estats) VALUES (%s, %s, %s, %s, %s)", $ecstatic->version, "reg", "feed", "bot", "0"));
		break;
	case "estats":
		$admin_email = $ecstatic->blogname . " <" . get_option('admin_email') . ">";
		$table_name = $wpdb->prefix . "ecstatic_" . $table;
		$wpdb->query($wpdb->prepare("INSERT INTO $table_name (addys, subject, xheader) VALUES (%s, %s, %s)", $admin_email, "ecSTATic [visitors][feeds][bots][totalv]", $ecstatic->blogname));
		break;
	}
} //ecstatic_populate_table

/***********************************************************************************/
class ecstatic_xml_parser {
public $darray = array();
private $row = 0;
private $data = "";
/***********************************************************************************/
function data_handler($parser, $data){
$this->data .= $data;
}
/***********************************************************************************/
function startTag($parser, $tag){
$this->data = "";
}
/***********************************************************************************/
function endTag($parser, $tag){
switch ($tag) {
	case "table":
		break;
	case "row":
		$this->row++;
		break;
	default:
		$this->darray[$this->row][$tag] = $this->data;
	}
}
} //class ecstatic_xml_parser

/***********************************************************************************/
function ecstatic_xml_parser_loader($ecstatic, $table, $filename="") {
global $wpdb;

if (isset($ecstatic->options["version"]) AND $ecstatic->options["version"] < 0.90) //mustn't import old versions
	return;

if ($filename == "")
	$filename = WP_PLUGIN_DIR . "/" . dirname(plugin_basename(__FILE__)) . '/' . $table . ".xml";

if (file_exists($filename)) {
	$ecstatic_parse = new ecstatic_xml_parser();
	$xml_parser = xml_parser_create();

//	if (defined('PHP_MAXPATHLEN')) //introduced in php v. 5.3
		xml_set_object($xml_parser, $ecstatic_parse);
//	else
//		xml_set_object($xml_parser, &$ecstatic_parse); //deprecated php v. 5.3 - Thanks to s. buser for the tip 20110306 ecSTATic 0.91
	xml_set_element_handler($xml_parser, "startTag", "endTag");
	xml_set_character_data_handler($xml_parser, "data_handler");
	xml_parser_set_option($xml_parser, XML_OPTION_CASE_FOLDING, 0); //case folding sucks, man
	if ($fp = @fopen($filename, "r")) {
		while ($data = fread($fp, 4096))
			xml_parse($xml_parser, $data, feof($fp));
		fclose($fp);
		}
	xml_parser_free($xml_parser);

	$table_name = $wpdb->prefix . "ecstatic_" . $table;
	if ($table == "aux_se") {
		foreach($ecstatic_parse->darray as $row) {
			if (!$wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE token=%s AND qsig=%s", $row["token"], $row["qsig"])))
				$peaches = $wpdb->query($wpdb->prepare("INSERT INTO $table_name SET token=%s, name=%s, qsig=%s, path=%s", $row["token"], $row["name"], $row["qsig"], $row["path"]));
			}
		}
	else {
		foreach($ecstatic_parse->darray as $row) {
			if (isset($row["name"]) AND isset($row["token"]) AND isset($row["type"]) AND isset($row["wnks"])) {
				if ($wnks = $wpdb->get_var($wpdb->prepare("SELECT wnks FROM $table_name WHERE token=%s AND type=%s", $row["token"], $row["type"]))) { //must have unique token/type combo
					$wnks |= (int)$row["wnks"];
					$peaches = $wpdb->query($wpdb->prepare("UPDATE $table_name SET name=%s, wnks=%d WHERE token=%s AND type=%s", $row["name"], $wnks, $row["token"], $row["type"]));
					}
				else
					$peaches = $wpdb->query($wpdb->prepare("INSERT INTO $table_name (type, token, name, lastseen, wnks) VALUES (%s, %s, %s, %d, %d)", $row["type"], $row["token"], $row["name"], $ecstatic->datetime, $row["wnks"]));
				}
			}
		}
	} //if
} //ecstatic_xml_parser_loader

/***********************************************************************************/
function ecstatic_update_browser_ids($ecstatic) { //one time or occassional run looking for "Unknown" or special case User Agents
global $wpdb;
include_once(WP_PLUGIN_DIR . "/" . dirname(plugin_basename(__FILE__)) . '/ecstatic_ua_parser.php');
$ua_parser = new USER_AGENT();
$u = $ecstatic->iurr_tables["ua"];
$qs = "SELECT id, ua, browser FROM $u WHERE aux=2 OR browser=%s";
$rq = $wpdb->get_results($wpdb->prepare($qs, "Unknown"));
foreach($rq as $r) {
	$rob = $ua_parser->load_from_string($r->ua);
	if ($r->browser != $rob->browser) {
		$qs = "UPDATE $u SET browser=%s WHERE id=%d";
		$wpdb->query($wpdb->prepare($qs, $rob->browser, $r->id));
		}
	}
} //ecstatic_update_browser_ids

/***********************************************************************************/
function ecstatic_versionista($ecstatic) {
global $wpdb;
$v = $ecstatic->options["version"];
$opt_tbl = $ecstatic->make_table("options");
switch ($v) {
	case ($v < 0.11):
		$tbl = $ecstatic->make_table("aux_spider");
		$wpdb->query("ALTER TABLE $tbl ADD lastseen TINYTEXT NOT NULL");
		$tbl = $ecstatic->make_table("aux_kill");
		$wpdb->query("ALTER TABLE $tbl ADD lastseen TINYTEXT NOT NULL");
	case ($v < 0.12):
		$wpdb->query("ALTER TABLE $opt_tbl DROP timezoneshift");
	case ($v < 0.20):
		$wpdb->query($wpdb->prepare("ALTER TABLE $opt_tbl MODIFY purgeolderthan SMALLINT UNSIGNED NOT NULL DEFAULT %d, MODIFY purgebotolderthan SMALLINT UNSIGNED NOT NULL DEFAULT %d, MODIFY maxtoshow SMALLINT UNSIGNED NOT NULL DEFAULT %d", $ecstatic->option_defaults["purgeolderthan"], $ecstatic->option_defaults["purgebotolderthan"], $ecstatic->option_defaults["maxtoshow"]));
		$wpdb->query($wpdb->prepare("ALTER TABLE $opt_tbl ADD noprefetch TINYINT UNSIGNED NOT NULL DEFAULT %d", $ecstatic->option_defaults["noprefetch"]));
		$tbl = $ecstatic->make_table("hits");
		$wpdb->query("ALTER TABLE $tbl MODIFY ip INT UNSIGNED NOT NULL, MODIFY ua MEDIUMINT UNSIGNED NOT NULL, MODIFY ref MEDIUMINT UNSIGNED NOT NULL, MODIFY ruri MEDIUMINT UNSIGNED NOT NULL, MODIFY score TINYINT UNSIGNED NOT NULL DEFAULT '0'");
		$tbl = $ecstatic->make_table("ips");
		$wpdb->query("ALTER TABLE $tbl MODIFY id INT UNSIGNED NOT NULL AUTO_INCREMENT, MODIFY aux TINYINT UNSIGNED NOT NULL DEFAULT '0', MODIFY score TINYINT UNSIGNED NOT NULL DEFAULT '0'");
		$tbl = $ecstatic->make_table("user_agents");
		$wpdb->query("ALTER TABLE $tbl MODIFY id MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT, MODIFY aux TINYINT UNSIGNED NOT NULL DEFAULT '0', MODIFY score TINYINT UNSIGNED NOT NULL DEFAULT '0'");
		$tbl = $ecstatic->make_table("refs");
		$wpdb->query("ALTER TABLE $tbl MODIFY id MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT, MODIFY aux TINYINT UNSIGNED NOT NULL DEFAULT '0', MODIFY score TINYINT UNSIGNED NOT NULL DEFAULT '0'");
		$tbl = $ecstatic->make_table("ruris");
		$wpdb->query("ALTER TABLE $tbl MODIFY id MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT, MODIFY aux MEDIUMINT UNSIGNED NOT NULL DEFAULT '1', MODIFY score TINYINT UNSIGNED NOT NULL DEFAULT '0'");
		$tbl = $ecstatic->make_table("cumulative");
		$wpdb->query("ALTER TABLE $tbl MODIFY regi MEDIUMINT UNSIGNED NOT NULL DEFAULT '0', MODIFY regp MEDIUMINT UNSIGNED NOT NULL DEFAULT '0', MODIFY feedi MEDIUMINT UNSIGNED NOT NULL DEFAULT '0', MODIFY feedp MEDIUMINT UNSIGNED NOT NULL DEFAULT '0', MODIFY boti MEDIUMINT UNSIGNED NOT NULL DEFAULT '0', MODIFY botp MEDIUMINT UNSIGNED NOT NULL DEFAULT '0'");
		$droptbl = $ecstatic->make_table("qstrs");
		$wpdb->query("DROP TABLE $droptbl");
	case ($v < 0.21):
		$wpdb->query($wpdb->prepare("ALTER TABLE $opt_tbl ADD mal_uri TINYINT UNSIGNED NOT NULL DEFAULT %d", $ecstatic->option_defaults["mal_uri"]));
		$tbl = $ecstatic->make_table("ips");
		$wpdb->query("ALTER TABLE $tbl ADD domain TINYTEXT NOT NULL");
	case ($v < 0.50):
		$tbl = $ecstatic->make_table("aux_se");
		$wpdb->query("ALTER TABLE $tbl ADD id MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT FIRST, ADD UNIQUE (id)");
		$wpdb->query("ALTER TABLE $tbl ADD path TINYTEXT NOT NULL");
	case ($v < 0.51):
		$tbl = $ecstatic->make_table("cumulative");
		$wpdb->query("ALTER TABLE $tbl MODIFY day DATE NOT NULL");
	case ($v < 0.60):
		$wpdb->query($wpdb->prepare("ALTER TABLE $opt_tbl ADD enablewidget TINYINT UNSIGNED NOT NULL DEFAULT %d", $ecstatic->option_defaults["enablewidget"]));
	case ($v < 0.71):
		$tbl = $ecstatic->make_table("hits");
		$wpdb->query($wpdb->prepare("ALTER TABLE $tbl MODIFY score TINYINT NOT NULL DEFAULT %d", 0));
		$res = mysql_query("SHOW COLUMNS FROM $tbl");
		while ($cols = mysql_fetch_assoc($res)) {
			if ($cols["Field"] == "qstr")
				$wpdb->query("ALTER TABLE $tbl DROP qstr");
			}
	case ($v < 0.75):
		$wpdb->query($wpdb->prepare("ALTER TABLE $opt_tbl ADD purgerssolderthan SMALLINT UNSIGNED NOT NULL DEFAULT %d", $ecstatic->option_defaults["purgerssolderthan"]));
	case ($v < 0.90):
		$tbl = $ecstatic->make_table("cumulative");
		$wpdb->query("ALTER TABLE $tbl MODIFY day DATE NOT NULL"); //buggered.  repeat of version .51 change

		$wpdb->query($wpdb->prepare("ALTER TABLE $opt_tbl ADD logsubadmin TINYINT UNSIGNED NOT NULL DEFAULT %d", $ecstatic->option_defaults["logsubadmin"]));

		$nltbl = $ecstatic->make_table("aux_lists"); //major db change - combine four tables into one
		$old = array("aux_spider", "aux_kill", "aux_nolog", "aux_wlist");
		$bits = array(1, 2, 4, 8);
		for ($i=0;$i<4;$i++) {
			$tbl = $ecstatic->make_table($old[$i]);
			$rows = $wpdb->get_results("SELECT * FROM $tbl");
			foreach ($rows as $r) {
				if ($wnks = $wpdb->get_var("SELECT wnks FROM $nltbl WHERE token='$r->token' AND type='$r->type'")) {
					$wnks |= $bits[$i];
					$wpdb->query("UPDATE $nltbl SET wnks = '$wnks' WHERE token = '$r->token' AND type = '$r->type'");
					}
				else
					$wpdb->query($wpdb->prepare("INSERT INTO $nltbl (type, token, name, lastseen, wnks) VALUES (%s, %s, %s, %d, %d)", $r->type, $r->token, $r->name, $r->lastseen, $bits[$i]));
				}
			}
	case ($v < 0.92):
		$wpdb->query($wpdb->prepare("ALTER TABLE $opt_tbl ADD dom_check TINYINT UNSIGNED NOT NULL DEFAULT %d", $ecstatic->option_defaults["dom_check"]));
		$wpdb->query($wpdb->prepare("ALTER TABLE $opt_tbl ADD dom_check_at TINYINT UNSIGNED NOT NULL DEFAULT %d", $ecstatic->option_defaults["dom_check_at"]));
		$wpdb->query($wpdb->prepare("ALTER TABLE $opt_tbl ADD dom_check_score TINYINT UNSIGNED NOT NULL DEFAULT %d", $ecstatic->option_defaults["dom_check_score"]));
	case ($v < 0.93):
		$tbl = $ecstatic->make_table("hits");
		$wpdb->query("ALTER TABLE $tbl DROP cookie");
		$wpdb->query("ALTER TABLE $tbl ADD scorebits TINYINT UNSIGNED NOT NULL DEFAULT '0' AFTER ruri");
	case ($v < 0.94):
		$wpdb->query("ALTER TABLE $opt_tbl DROP mal_uri");
		$wpdb->query($wpdb->prepare("ALTER TABLE $opt_tbl ADD dom_method TINYINT UNSIGNED NOT NULL DEFAULT %d", $ecstatic->option_defaults["dom_method"]));
		$wpdb->query($wpdb->prepare("ALTER TABLE $opt_tbl ADD wtf TINYINT UNSIGNED NOT NULL DEFAULT %d", $ecstatic->option_defaults["wtf"]));
		$wpdb->query($wpdb->prepare("ALTER TABLE $opt_tbl ADD wtf_x TINYINT UNSIGNED NOT NULL DEFAULT %d", $ecstatic->option_defaults["wtf_x"]));
		$wpdb->query($wpdb->prepare("ALTER TABLE $opt_tbl ADD wtf_secs TINYINT UNSIGNED NOT NULL DEFAULT %d", $ecstatic->option_defaults["wtf_secs"]));
		$tbl = $ecstatic->make_table("hits");
		$wpdb->query("ALTER TABLE $tbl ADD INDEX ip (ip)");
		$wpdb->query("ALTER TABLE $tbl ADD INDEX ua (ua)");
		$wpdb->query("ALTER TABLE $tbl ADD INDEX ref (ref)");
		$wpdb->query("ALTER TABLE $tbl ADD INDEX ruri (ruri)");
	case ($v < 0.941):
		$tbl = $ecstatic->make_table("hits");
		$wpdb->query($wpdb->prepare("ALTER TABLE $tbl MODIFY scorebits SMALLINT NOT NULL DEFAULT %d", 0));
	case ($v < 0.942):
		$wpdb->query($wpdb->prepare("ALTER TABLE $opt_tbl ADD block_new_bots TINYINT UNSIGNED NOT NULL DEFAULT %d", $ecstatic->option_defaults["block_new_bots"]));
		$wpdb->query($wpdb->prepare("ALTER TABLE $opt_tbl ADD skip_rip TINYINT UNSIGNED NOT NULL DEFAULT %d", $ecstatic->option_defaults["skip_rip"]));
	case ($v < 0.95):
		$wpdb->query($wpdb->prepare("ALTER TABLE $opt_tbl ADD stop_popups TINYINT UNSIGNED NOT NULL DEFAULT %d", $ecstatic->option_defaults["stop_popups"]));
		$wpdb->query($wpdb->prepare("ALTER TABLE $opt_tbl ADD stop_popups_beyond SMALLINT UNSIGNED NOT NULL DEFAULT %d", $ecstatic->option_defaults["stop_popups_beyond"]));
		$wpdb->query($wpdb->prepare("ALTER TABLE $opt_tbl ADD no_lowscore_popups TINYINT UNSIGNED NOT NULL DEFAULT %d", $ecstatic->option_defaults["no_lowscore_popups"]));
	case ($v < 0.97):
		$wpdb->query("ALTER TABLE $opt_tbl ADD estats TINYTEXT NOT NULL");
	case ($v < 0.98):
		$estats = $wpdb->prefix . "ecstatic_estats";
		if ($wpdb->get_var("SHOW TABLES LIKE '{$estats}'") == $estats) { //complicated situation.  .972 created estats, but it's never called until it's used, so it can't be known if it can be modified unless it's known to have been created.
			$wpdb->query("ALTER TABLE $estats ADD xheader TEXT NOT NULL AFTER subject"); //if it has not been created before, it will be created properly when it's needed
			$wpdb->query($wpdb->prepare("UPDATE $estats SET xheader=%s", $ecstatic->blogname));
			}
		$tbl = $ecstatic->make_table("hits");
		$wpdb->query($wpdb->prepare("ALTER TABLE $tbl MODIFY scorebits SMALLINT UNSIGNED NOT NULL DEFAULT %d", 0)); //missed the unsigned up above
		$wpdb->query("ALTER TABLE $opt_tbl DROP login");
		$wpdb->query($wpdb->prepare("ALTER TABLE $opt_tbl ADD login_limit TINYINT UNSIGNED NOT NULL DEFAULT %d", $ecstatic->option_defaults["login_limit"]));
		$wpdb->query($wpdb->prepare("ALTER TABLE $opt_tbl ADD login_window TINYINT UNSIGNED NOT NULL DEFAULT %d", $ecstatic->option_defaults["login_window"]));
		$wpdb->query($wpdb->prepare("ALTER TABLE $opt_tbl ADD login_lock_duration TINYINT UNSIGNED NOT NULL DEFAULT %d", $ecstatic->option_defaults["login_lock_duration"]));
		$wpdb->query($wpdb->prepare("ALTER TABLE $opt_tbl MODIFY maxtoshow SMALLINT UNSIGNED NOT NULL DEFAULT %d", $ecstatic->option_defaults["maxtoshow"]));
	case ($v < 0.981):
		$wpdb->query($wpdb->prepare("ALTER TABLE $opt_tbl MODIFY login_window SMALLINT UNSIGNED NOT NULL DEFAULT %d", $ecstatic->option_defaults["login_window"]));
		$wpdb->query($wpdb->prepare("ALTER TABLE $opt_tbl MODIFY login_lock_duration SMALLINT UNSIGNED NOT NULL DEFAULT %d", $ecstatic->option_defaults["login_lock_duration"]));
	case ($v < 0.984):
		$wpdb->query($wpdb->prepare("ALTER TABLE $opt_tbl ADD plain_ruris TINYINT UNSIGNED NOT NULL DEFAULT %d", $ecstatic->option_defaults["plain_ruris"]));
	case ($v < 0.985):
		$wpdb->query($wpdb->prepare("ALTER TABLE $opt_tbl ADD manual_purge TINYINT UNSIGNED NOT NULL DEFAULT %d", $ecstatic->option_defaults["manual_purge"]));
		$wpdb->query($wpdb->prepare("ALTER TABLE $opt_tbl ADD iporcidr TINYINT UNSIGNED NOT NULL DEFAULT %d", $ecstatic->option_defaults["iporcidr"]));
	case ($v < 0.9872):
		$wpdb->query($wpdb->prepare("ALTER TABLE $opt_tbl ADD anonref TINYINT UNSIGNED NOT NULL DEFAULT %d", $ecstatic->option_defaults["anonref"]));
	case ($v < 0.9920):
		$tbl = $ecstatic->make_table("refs");
		$wpdb->query($wpdb->prepare("UPDATE $tbl SET aux=%d WHERE ref=''", 3)); //mark blank referrer as blank: to be ignored by some routines
	case ($v < 0.9931):
		ecstatic_update_browser_ids($ecstatic);
		$estats = $wpdb->prefix . "ecstatic_estats";
		if ($wpdb->get_var("SHOW TABLES LIKE '{$estats}'") == $estats) { //complicated situation.  .972 created estats, but it's never called until it's used, so it can't be known if it can be modified unless it's known to have been created.
			$wpdb->query($wpdb->prepare("ALTER TABLE $estats ADD mlinks TINYINT UNSIGNED NOT NULL DEFAULT %d", 1)); //if it has not been created before, it will be created properly when it's needed
			}
	} //switch
$wpdb->query($wpdb->prepare("UPDATE $opt_tbl SET version=%s", $ecstatic->version));
$ecstatic->options["version"] = $ecstatic->version;
} //ecstatic_versionista
?>