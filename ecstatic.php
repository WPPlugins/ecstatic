<?php
/*
Plugin Name: ecSTATic
Plugin URI: http://www.kayak2u.com/blog/ecstatic/
Description: Faster, Smaller, Non-Ecological Visitor Stats and Management for Wordpress Blogs
Version: 0.9933
Author: MikeSoja, BVD
Author URI: http://www.kayak2u.com/blog/ecstatic/
*/
$plain_ruris = 0; //Option kluge
$url_bobbed = $schost = ""; //redundancy elimination kluge
/***********************************************************************************/
class ecstatic {
public $version = '0.9933';
public $blogname = "";
public $url = "";
public $datetime = '';
public $timezoneoffset = 0;
public $zero_am_today;
public $logsubadmin = false;
public $score = 0;
public $options = array();
public $option_defaults = array("showbannergraph" => 1, "showgraphregi" => 1, "showgraphregp" => 1, "showgraphfeedi" => 1, "showgraphfeedp" => 0, "showgraphboti" => 1, "showgraphbotp" => 0, "showreg" => 1, "showfeed" => 1, "showbot" => 1, "collectloggeduser" => 0, "daystograph" => 32, "purgeolderthan" => 60, "purgerssolderthan" => 15, "purgebotolderthan" => 30, "daystoshow" => 1, "maxtoshow" => 64, "newvisitorminutes" => 60, "mal_ip" => 10, "empty_ua" => 4, "empty_ref" => 0, "trackback" => 0, "lostpassword" => 1, "panel1" => "reg", "panel2" => "feed", "panel3" => "bot", "topentriestoshow" => 25, "graphsort" => 0, "noprefetch" => 0, "enablewidget" => 0, "logsubadmin" => 0, "dom_check" => 0, "dom_check_at" => 4, "dom_check_score" => 4, "dom_method" => 1, "wtf" => 0, "wtf_x" => 10, "wtf_secs" => 2, "block_new_bots" => 0, "skip_rip" => 0, "stop_popups" => 0, "stop_popups_beyond" => 500, "no_lowscore_popups" => 7, "estats" => 0, "login_limit" => 10, "login_window" => 10, "login_lock_duration" => 60, "plain_ruris" => 0, "manual_purge" => 0, "iporcidr" => 0, "anonref" => 0);
//changes to options must be made in five places (six, for string options)
//$option_defaults above
//ecstatic_tables.php ecstatic_create_table
//ecstatic_tables.php ecstatic_populate_table - if the option is a string
//ecstatic_tables.php ecstatic_versionista
//ecstatic_forms.php option_form - augment the input form
//ecstatic_forms.php process_option_changes - validation plus two places in the big save
public $options_table;
public $hits_table;
public $cumulative_table;
public $aux_se_table;
public $aux_lists_table;
public $iurr_tables = array();

/***********************************************************************************/
function get_options() {
global $wpdb, $plain_ruris, $url_bobbed, $schost;
$this->blogname = get_option("blogname");
$this->options = $wpdb->get_row("SELECT * FROM $this->options_table", ARRAY_A);
foreach ($this->option_defaults as $key => $value)
	if (!isset($this->options[$key]))
		$this->options[$key] = $value;

$plain_ruris = $this->options["plain_ruris"]; //a crude workaround to get a variable to ecstatic_makelink() in ecstatic_interface.php

if ($this->options["logsubadmin"] AND !(current_user_can('administrator')))
	$this->logsubadmin = true;

$this->url = get_option("siteurl");
$base = parse_url($this->url);
$schost = $base["scheme"] . "://" . $base["host"];
$len = strlen($schost);
$url_bobbed = substr($this->url, $len);

$this->timezoneoffset = get_option("gmt_offset");
if ($this->version != $this->options["version"]) {
	include_once(WP_PLUGIN_DIR . "/" . dirname(plugin_basename(__FILE__)) . '/ecstatic_tables.php');
	ecstatic_versionista($this);
	}

$this->zero_am_today = $this->datetime - ($this->datetime + (60 * 60 * $this->timezoneoffset)) % (60*60*24); //midnight zero
} //get_options

/***********************************************************************************/
function make_table($tana) {
global $wpdb;
$table_name = $wpdb->prefix . "ecstatic_" . $tana;
if ($wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") != $table_name) {
	include_once(WP_PLUGIN_DIR . "/" . dirname(plugin_basename(__FILE__)) . '/ecstatic_tables.php');
	ecstatic_create_table($this, $tana);
	ecstatic_populate_table($this, $tana);
	}
return $table_name;
} //make_table

public $ips = array();

/***********************************************************************************/
function ipCIDRCheck ($IP, $CIDR) { //copied from http://www.php.net/manual/en/ref.network.php#74656
list($net, $mask) = split("/", $CIDR);
$ip_net = ip2long($net);
$ip_mask = ~((1 << (32 - $mask)) - 1);
$ip_ip = ip2long($IP);
$ip_ip_net = $ip_ip & $ip_mask;
return ($ip_ip_net == $ip_net);
} //ipCIDRCheck

/***********************************************************************************/
function load_ips($token) {
$min = str_replace("*", "0", $token);
$min = str_replace("?", "0", $min);
$max = str_replace("*", "255", $token);
$max = str_replace("?", "9", $max);
$minocts = explode(".", $min);
$maxocts = explode(".", $max);
for ($i=0; $i<4; $i++) {
	if (strpos($minocts[$i],"-") !== false)
		list($minocts[$i], $maxocts[$i]) = explode("-", $minocts[$i]);
	if ($minocts[$i] > 255)
		$minocts[$i] = 0;
	if ($maxocts[$i] > 255)
		$maxocts[$i] = 255;
	}
$this->ips[$token]["min"] = (float)sprintf("%u", ip2long($minocts[0] . "." . $minocts[1] . "." . $minocts[2] . "." . $minocts[3]));
$this->ips[$token]["max"] = (float)sprintf("%u", ip2long($maxocts[0] . "." . $maxocts[1] . "." . $maxocts[2] . "." . $maxocts[3]));
} //load_ips

/***********************************************************************************/
function is_in_lists2($ip2check, $ua2check, $ref2check, $ruri2check, $wnks) { //wlist, noshow, kill, spider
global $wpdb;
$arobj = array();
static $record;
if (!$record) {
	$record = $wpdb->get_results("SELECT name, token, type, wnks, hits FROM $this->aux_lists_table");
	foreach ($record as $r)
		if ($r->type == "ip")
			if (strpos($r->token, "/") === false) //not amenable to CIDR notation
				$this->load_ips($r->token);
	}
$ip2checkf = (float)sprintf("%u", ip2long($ip2check));
$ua2check = str_replace(" ", "", strtolower($ua2check));
foreach ($record as $rec) {
	if ($rec->wnks & $wnks) {
		switch ($rec->type) {
			case "ip":
				if (strpos($rec->token, "/") === false) {
					if ($ip2check != "" AND $ip2checkf >= $this->ips[$rec->token]["min"] AND $ip2checkf <= $this->ips[$rec->token]["max"])
						$arobj[] = $rec;
					}
				elseif ($this->ipCIDRCheck($ip2check, $rec->token))
					$arobj[] = $rec;
				break;
			case "ua":
				if (strpos($ua2check, $rec->token) !== false)
					$arobj[] = $rec;
				break;
			case "ref":
				if (strpos($ref2check, $rec->token) !== false)
					$arobj[] = $rec;
				break;
			case "ruri":
				if (strpos($ruri2check, $rec->token) !== false)
					$arobj[] = $rec;
				break;
			case "mix": // ua:googlebot&ruri:login
				$test = $bit = 0;
				$t = explode("&", $rec->token);
				foreach($t as $tt){
					$u = explode(":", $tt);
					switch($u[0]){
						case "ua":
							$test |= 0x2;
							if (strpos($ua2check, $u[1]) !== false)
								$bit |= 0x2;
							break;
						case "ruri":
							$test |= 0x8;
							if (strpos($ruri2check, $u[1]) !== false)
								$bit |= 0x8;
							break;
						}
					}
				if ($test == ($test & $bit))
					$arobj[] = $rec;
				break;
			} //switch
		} //if
	} //foreach
return $arobj;
} //is_in_lists2

/******************************************CONSTRUCTOR****************************/
function __construct() {
if (function_exists("date_default_timezone_set"))
	date_default_timezone_set(get_option('timezone_string'));
$this->datetime = time();
$this->options_table = $this->make_table("options");
$this->hits_table = $this->make_table("hits");
$this->cumulative_table = $this->make_table("cumulative");
$this->aux_se_table = $this->make_table("aux_se");
$this->aux_lists_table = $this->make_table("aux_lists");
$this->iurr_tables["ip"] = $this->make_table("ips");
$this->iurr_tables["ua"] = $this->make_table("user_agents");
$this->iurr_tables["ref"] = $this->make_table("refs");
$this->iurr_tables["ruri"] = $this->make_table("ruris");
$this->get_options(); //initialize more variables
} //ecstatic constructor
} //class ecstatic
/***********************************************************************************/
/***********************************************************************************/
class ecstatic_get_host {
private $raw_nsr = "";
private $full_domain_name = "";
public $domain_name = "";
/****************************************************/
function full_domain_name(){
if ($this->host_error[$this->full_domain_name])
	return $this->host_error[$this->full_domain_name];
else
	return $this->full_domain_name;
} //full_domain_name
/****************************************************/
function domain_name($dm=""){
if ($dm)
	$this->domain_name = $dm;
if ($this->host_error[$this->domain_name])
	return $this->host_error[$this->domain_name];
else
	return $this->domain_name;
} //domain_name
/****************************************************/
function raw_nsr(){ //for debuggin'
return $this->raw_nsr;
} //raw_nsr
/****************************************************/
function is_domain_error(){
if (!$this->domain_name OR $this->host_error[$this->domain_name])
	return true;
else
	return false;
} //is_domain_error
private $host_error = array("1(FORMERR)"=>"Format Error", "2(SERVFAIL)"=>"(ServerFail)", "3(NXDOMAIN)"=>"(Non-existent)", "4(NOTIMP)"=>"Not Implemented", "5(REFUSED)"=>"Query Refused", "6(YXDOMAIN)"=>"Name Exists when it should not", "7(YXRRSET)"=>"RR Set Exists when it should not", "8(NXRRSET)"=>"RR Set that should exist does not", "9(NOTAUTH)"=>"Server Not Authoritative for zone", "10(NOTZONE)"=>"Name not contained in zone", "TO"=>"(timed out)", "timed out"=>"(timed out)", "(empty)" => "(Empty)", "hosterr" => "(Host error)", "NA"=>"(NotAvailable)");
private $nslookup_error = array("FORMERR"=>"1(FORMERR)", "SERVFAIL"=>"2(SERVFAIL)", "NXDOMAIN"=>"3(NXDOMAIN)", "NOTIMP"=>"4(NOTIMP)", "REFUSED"=>"5(REFUSED)", "YXDOMAIN"=>"6(YXDOMAIN)", "YXRRSET"=>"7(YXRRSET)", "NXRRSET"=>"8(NXRRSET)", "NOTAUTH"=>"9(NOTAUTH)", "NOTZONE"=>"10(NOTZONE)", "TO"=>"TO", "timed out"=>"timed out", "(empty)"=>"(Empty)", "hosterr"=>"'host' error");
/***************************************/
function ghba($ip) {
$this->raw_nsr = gethostbyaddr($ip);
if ($this->raw_nsr == $ip)
	$this->full_domain_name = $this->domain_name = "NA";
else
	$this->full_domain_name = $this->raw_nsr;
} //ghba
/***************************************/
function dgr($ip) {
$ptr= implode(".", array_reverse(explode(".", $ip))) . ".in-addr.arpa";
$this->raw_nsr = @dns_get_record($ptr, DNS_PTR);
if ($this->raw_nsr == null)
	$this->full_domain_name = $this->domain_name = "NA";
else
	$this->full_domain_name = $raw[0]["target"];
} //dgr
/***************************************/
function dig($ip) {
$ptr = implode(".", array_reverse(explode(".", $ip))) . ".in-addr.arpa";
$raw = $this->raw_nsr = shell_exec("dig ptr {$ptr}");
if (strpos($raw, "timed out") !== false)
	$this->full_domain_name = "TO";
elseif (preg_match('/;; ANSWER SECTION.*\n.*PTR\s+(.*).\n/', $raw, $matches))
	$this->full_domain_name = $matches[1];
elseif (preg_match('/status[:\s]*(.*),/', $raw, $matches))
	$this->full_domain_name = $this->nslookup_error[$matches[1]];
} //dig
/***************************************/
function nslookup($ip) {
$raw = $this->raw_nsr = shell_exec("nslookup -timeout=3 -retry=0 {$ip}");
if (strpos($raw, "timed out") !== false)
	$this->full_domain_name = "TO";
elseif (preg_match('/name = (.*).\n/', $raw, $matches))
	$this->full_domain_name = $matches[1];
elseif (preg_match('/in-addr\.arpa[.:]* (.*)\n/', $raw, $matches))
	$this->full_domain_name = $this->nslookup_error[$matches[1]];
} //nslookup
/***************************************/
function host($ip) {
$raw = $this->raw_nsr = shell_exec("host -W 3 {$ip}");
if (strpos($raw, "timed out") !== false)
	$this->full_domain_name = "TO";
else {
	$raw = (($raw ? end(explode(' ', $raw)) : "hosterr"));
	$this->full_domain_name = rtrim($raw, " .\n\r\0"); //get the bugger off the end
	}
if (strlen($this->full_domain_name) < 2)
	$this->full_domain_name = "(empty)";
} //host
/****************************************************/
function __construct($ipx="", $ip="", $ecstatic="") {
global $wpdb;
if ($ipx AND $ip AND $ecstatic) {
	switch ($ecstatic->options["dom_method"]) {
		case 2:
			$this->nslookup($ip);
			break;
		case 4:
			$this->dig($ip);
			break;
		case 8:
			$this->ghba($ip); //gethostbyaddr()
			break;
		case 16:
			$this->dgr($ip); //dns_get_record()
			break;
		case 1:
		default:
			$this->host($ip);
			break;
		}
	$fdn = array_reverse(explode(".", $this->full_domain_name));
	if (count($fdn) > 1) {
		if (strlen($fdn[0]) == 2 AND count($fdn) > 2) //them furrin' two letter domain end designations
			$this->domain_name = $fdn[2] . "." . $fdn[1] . "." . $fdn[0];
		else
			$this->domain_name = $fdn[1] . "." . $fdn[0];
		}
	else
		$this->domain_name = $fdn[0];
	$wpdb->query($wpdb->prepare("UPDATE {$ecstatic->iurr_tables["ip"]} SET domain=%s WHERE id=%d", $this->domain_name, $ipx));
	}
} //constructor
} //class ecstatic_get_host
/***********************************************************************************/
/***********************************************************************************/
class act extends ecstatic {

public $ip = ''; //REMOTE_ADDR
public $ua = ''; //HTTP_USER_AGENT
public $ref = ''; //HTTP_REFERER
public $ruri = ''; //REQUEST_URI
private $scorebits = 0;
private $listbits = 0;
private $loginbits = 0;
private $idz = array("ip" => 0, "ua" => 0, "ref" => 0, "ruri" => 0);

/***********************************************************************************/
function is_in_lists() { //wlist, nolog, kill, spider
global $wpdb;
$ip2checkf = (float)sprintf("%u", ip2long($this->ip));
$ua2check = str_replace(" ", "", strtolower($this->ua));
$record = $wpdb->get_results("SELECT id, token, type, wnks FROM $this->aux_lists_table");
foreach ($record as $rec) {
	switch ($rec->type) {
		case "ip":
			if (strpos($rec->token, "/") === false) {
				$this->load_ips($rec->token);
				if ($ip2checkf >= $this->ips[$rec->token]["min"] AND $ip2checkf <= $this->ips[$rec->token]["max"]) {
					$this->listbits |= $rec->wnks;
					$wpdb->query($wpdb->prepare("UPDATE $this->aux_lists_table SET hits=hits+1, lastseen=%d WHERE id=%d", $this->datetime, $rec->id));
					}
				}
			elseif ($this->ipCIDRCheck($this->ip, $rec->token)) {
				$this->listbits |= $rec->wnks;
				$wpdb->query($wpdb->prepare("UPDATE $this->aux_lists_table SET hits=hits+1, lastseen=%d WHERE id=%d", $this->datetime, $rec->id));
				}
			break;
		case "ua":
			if (strpos($ua2check, $rec->token) !== false) {
				$this->listbits |= $rec->wnks;
				$wpdb->query($wpdb->prepare("UPDATE $this->aux_lists_table SET hits=hits+1, lastseen=%d WHERE id=%d", $this->datetime, $rec->id));
				}
			break;
		case "ref":
			if (strpos($this->ref, $rec->token) !== false) {
				$this->listbits |= $rec->wnks;
				$wpdb->query($wpdb->prepare("UPDATE $this->aux_lists_table SET hits=hits+1, lastseen=%d WHERE id=%d", $this->datetime, $rec->id));
				}
			break;
		case "ruri":
			if (strpos($this->ruri, $rec->token) !== false) {
				$this->listbits |= $rec->wnks;
				$wpdb->query($wpdb->prepare("UPDATE $this->aux_lists_table SET hits=hits+1, lastseen=%d WHERE id=%d", $this->datetime, $rec->id));
				}
			break;
		case "mix": // ua:googlebot&ruri:login
			$test = $bit = 0;
			$t = explode("&", $rec->token);
			foreach($t as $tt){
				$u = explode(":", $tt);
				switch($u[0]){
					case "ua":
						$test |= 0x2;
						if (strpos($ua2check, $u[1]) !== false)
							$bit |= 0x2;
						break;
					case "ruri":
						$test |= 0x8;
						if (strpos($this->ruri, $u[1]) !== false)
							$bit |= 0x8;
						break;
					}
				}
			if ($test == ($test & $bit)) {
				$this->listbits |= $rec->wnks;
				$wpdb->query($wpdb->prepare("UPDATE $this->aux_lists_table SET hits=hits+1, lastseen=%d WHERE id=%d", $this->datetime, $rec->id));
				}
			break;
		} //switch
	} //foreach
} //is_in_lists

/***********************************************************************************/
function ip_validate() {
if ($this->ip == "") { //does this ever happen???
	$this->ip = "0.0.0.0";
	return false;
	}
if (function_exists(filter_var)) { //PHP 5.2+
	if (!filter_var($this->ip, FILTER_VALIDATE_IP))
		return false;
	}
else {
	if (strpos($this->ip, ":") === false) { //IPv4
		if ($this->ip != long2ip(ip2long($this->ip)))
			return false;
		}
	else { //old school IPv6
		if(!preg_match('/^(((?=(?>.*?(::))(?!.+\3)))\3?|([\dA-F]{1,4}(\3|:(?!$)|$)|\2))(?4){5}((?4){2}|(25[0-5]|(2[0-4]|1\d|[1-9])?\d)(\.(?7)){3})\z/i', $this->ip))
			return false;
		}
	}
return true;
} //ip_validate

/***********************************************************************************/
function hit_parade() {
global $schost;

$this->ip = $_SERVER['REMOTE_ADDR'];
$this->ua = (isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '');
$this->ref = (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '');
if (isset($_SERVER['REQUEST_URI']))
	$this->ruri = $_SERVER['REQUEST_URI'];
elseif (isset($_SERVER['QUERY_STRING']))
	$this->ruri = $_SERVER['SCRIPT_NAME'] .'?'. $_SERVER['QUERY_STRING'];
elseif (isset($_SERVER['argv']))
		$this->ruri = $_SERVER['SCRIPT_NAME'] .'?'. $_SERVER['argv'][0]; //substr($_SERVER['argv'][0], strpos($_SERVER['argv'][0], ';') + 1)
else
	$this->ruri = $_SERVER['SCRIPT_NAME'];
$this->ruri = '/'. ltrim($this->ruri, '/');

if (isset($_SERVER['HTTP_X_MOZ'])) {
	if (strtolower($_SERVER['HTTP_X_MOZ']) == 'prefetch' AND $this->options["noprefetch"]) {
//	    header('HTTP/1.0 403 Forbidden');
//    	exit("403: Forbidden<br><br>Prefetching not allowed.");
		$this->score += 10;
		$this->scorebits |= MOZ_PREFETCH_BIT;
    	}
	}

if (!$this->ip_validate()) {
	$this->score += $this->options["mal_ip"];
	$this->scorebits |= BAD_IP_BIT;
	}

$this->is_in_lists(); //sets $this->listbits invalid wlist nolog kill spider 10001111b
if ($this->listbits & XWNKS_KILL_BIT) //00000010b - kilt
	$this->score += 10;

if ($this->ua == "") {
	$this->score += $this->options["empty_ua"];
	$this->scorebits |= EMPTY_UA_BIT; //00000010b
	}
if (strpos($this->ref, $schost) === 0) //don't record one's own referrers
	$this->ref = "";
if ($this->ref == "") {
	$this->score += $this->options["empty_ref"];
	$this->scorebits |= EMPTY_REF_BIT; //00000100b
	}
if (strpos($this->ruri, "lostpassword") !== false) {
	$this->score += $this->options["lostpassword"];
	$this->scorebits |= LOST_PASSWORD_BIT; //00100000b
	}
if (strpos($this->ruri, "wp-trackback") !== false) {
	$this->score += $this->options["trackback"];
	$this->scorebits |= TRACKBACK_BIT; //01000000b
	}
/*
if (preg_match("/.css$|.js$|.ico$/i", $this->ruri))
	return false;
if (stristr($this->ruri, "/wp-content/plugins") == true)
	return false;
if (stristr($this->ruri, "/wp-content/themes") == true)
	return false;
if ($this->ip == "127.0.0.1")
	return false;
return true;
*/
} //hit_parade

/***********************************************************************************/
function append_hit() {
global $wpdb;
$iz = array("ip" => $this->ip, "ua" => $this->ua, "ref" => $this->ref, "ruri" => $this->ruri);

$type = "reg";
if (strpos($this->ruri, "feed") !== false)
	$type = "feed";
if (preg_match("/bot|spider|crawl/i", $this->ua) OR ($this->listbits & XWNKS_BOT_BIT)) { //wnks 0001b - spider/bot
	$type = "bot";
	if ($this->options["block_new_bots"] AND !($this->listbits & XWNKS_BOT_BIT)) {
		$this->score += 10;
		$this->scorebits |= NEW_BOT_BIT; //New bot 1 0000 0000b
		}
	}

if ($this->options["wtf"]) {
	$fifteen_minutes_ago = $this->datetime - 900;
	$wtf = $wpdb->get_results("SELECT datetime, ip, ua, ref, scorebits FROM {$this->hits_table} WHERE datetime > '{$fifteen_minutes_ago}' ORDER BY datetime DESC");
	}
foreach ($this->iurr_tables as $key=>$table) {
	switch ($key) {
		case "ip":
			$row = $wpdb->get_row($wpdb->prepare("SELECT id, aux, score FROM $table WHERE $key=%s", $iz[$key]));
			$login_aux = $row->aux;
			break;
		default:
			$row = $wpdb->get_row($wpdb->prepare("SELECT id, score FROM $table WHERE $key=%s", $iz[$key]));
		}
	if ($row->id) {
		$this->idz[$key] = $row->id;
		$this->score += $row->score;
		if ($this->options["wtf"] AND $this->score < 10 AND ($key == "ip" OR $key == "ua" OR ($key == "ref" AND $this->ref != ""))) { //check for speed scrapers, etc.
			$x = 0;
			foreach ($wtf as $wt) {
				if ($wt->$key == $this->idz[$key]) {
					$x++;
					$t = $this->datetime - $wt->datetime;
					if (($x >= ($this->options["wtf_x"]-1) AND ($t/$x) <= $this->options["wtf_secs"]) OR $wt->scorebits & WTF_BIT) {
						$this->score += 10;
						$this->scorebits |= WTF_BIT; //00001000b wtf bit - time seconds / hits
						break;
						}
					}
				}
			}
		}
//	elseif (!($this->listbits & XWNKS_NOSHOW_BIT)) { //wnks 0100b - NoLog
	else {
		switch ($key) {
			case "ua": //id, ua, aux, browser, os, renderer, score
				$wpdb->query($wpdb->prepare("INSERT INTO $table VALUES (null, %s, DEFAULT, %s, %s, %s, DEFAULT)", $this->ua, "", "", ""));
				break;
			case "ip": //id, ip, aux, score, domain
				$wpdb->query($wpdb->prepare("INSERT INTO $table VALUES (null, %s, DEFAULT, DEFAULT, %s)", $this->ip, ""));
				break;
			case "ref": //id, ref, aux, score
				$wpdb->query($wpdb->prepare("INSERT INTO $table VALUES (null, %s, DEFAULT, DEFAULT)", $this->ref));
				break;
			case "ruri": //id, ruri, aux, score
				$wpdb->query($wpdb->prepare("INSERT INTO $table VALUES (null, %s, %d, DEFAULT)", $this->ruri, 0)); //must set the default aux to 0
				break;
			} //switch
		if (mysql_affected_rows() > 0)
			$this->idz[$key] = mysql_insert_id();
		}
	} //foreach

if ($this->options["dom_check"] AND $this->score >= $this->options["dom_check_at"] AND $this->score < 10) { //extra domain test
	$dom = new ecstatic_get_host($this->idz["ip"], $this->ip, $this);
	if ($dom->is_domain_error()) {
		$this->score += $this->options["dom_check_score"];
		$this->scorebits |= BAD_DOMAIN_BIT; //10000000b bad dom
		}
	}
if (($this->listbits & XWNKS_WHITELIST_BIT) AND (!($this->listbits & XWNKS_XWHITELIST_BIT)))
	$this->score = -1;

if ($this->loginbits OR ($login_aux & 0x4)) //0100b=has previously tripped the login lockout switch
	$this->process_login_fails();

if ($this->score < 10 OR current_user_can('administrator')) {
	$ind = array("reg" => "UPDATE $this->cumulative_table SET regi=regi+1 WHERE day=%s", "bot" => "UPDATE $this->cumulative_table SET boti=boti+1 WHERE day=%s", "feed" => "UPDATE $this->cumulative_table SET feedi=feedi+1 WHERE day=%s");
	$page = array("reg" => "UPDATE $this->cumulative_table SET regp=regp+1 WHERE day=%s", "bot" => "UPDATE $this->cumulative_table SET botp=botp+1 WHERE day=%s", "feed" => "UPDATE $this->cumulative_table SET feedp=feedp+1 WHERE day=%s");
	$today_start_time = $this->zero_am_today; //midnight zero
	$today = date("Y-m-d", $today_start_time);
	$cutoff = $this->datetime - ($this->options["newvisitorminutes"] * 60);
	if (!$wpdb->get_var($wpdb->prepare("SELECT count(*) FROM $this->cumulative_table WHERE day=%s", $today))) //see if the today row has been created
		$wpdb->query($wpdb->prepare("INSERT INTO $this->cumulative_table VALUES (%s, DEFAULT, DEFAULT, DEFAULT, DEFAULT, DEFAULT, DEFAULT)", $today)); //create today row
	$wpdb->query($wpdb->prepare($page[$type],$today));
	if ($type != "bot")
		$wpdb->query($wpdb->prepare("UPDATE {$this->iurr_tables["ruri"]} SET aux=aux+1 WHERE id=%d", $this->idz["ruri"])); //page visits
	if ($type != "reg" AND !$wpdb->get_var($wpdb->prepare("SELECT count(*) FROM $this->hits_table WHERE ua=%d AND datetime>%d",$this->idz["ua"],$today_start_time)))
		$wpdb->query($wpdb->prepare($ind[$type],$today));
	elseif ($type == "reg" AND !$wpdb->get_var($wpdb->prepare("SELECT count(*) FROM $this->hits_table WHERE ip=%d AND ua=%d AND datetime>%d",$this->idz["ip"],$this->idz["ua"],$cutoff)))
		$wpdb->query($wpdb->prepare($ind[$type],$today));
	} //score
if ($this->listbits & XWNKS_NOSHOW_BIT)
	$this->scorebits |= NO_SHOW_BIT;
$wpdb->query($wpdb->prepare("INSERT INTO $this->hits_table (datetime, ip, ua, ref, ruri, scorebits, score) VALUES (%s, %d, %d, %d, %d, %d, %d)", $this->datetime, $this->idz["ip"], $this->idz["ua"], $this->idz["ref"], $this->idz["ruri"], $this->scorebits, $this->score));
} //append_hit

/***********************************************************************************/
function process_login_fails() {
global $wpdb;
if ($this->options["login_limit"]) {
	$this->scorebits |= $this->loginbits; //coincidentally, it works
	$login_window = $this->datetime - ($this->options["login_window"] * 60);
	if ($x = $wpdb->get_var($wpdb->prepare("SELECT count(*) FROM {$this->hits_table} WHERE ip = %d AND datetime > %d AND scorebits & %d", $this->idz["ip"], $login_window, LOGIN_ERROR_BITS))) {
		if ($this->options["login_lock_duration"]) //straightforward use of login_lock_duration
			$login_lock_duration = $this->datetime - ($this->options["login_lock_duration"] * 60);
		else { //calculate the login_lock_duration on a sliding scale based on visitor history
			$first_fail = $wpdb->get_var($wpdb->prepare("SELECT MIN(datetime) FROM {$this->hits_table} WHERE ip = %d AND datetime > %d AND scorebits & %d", $this->idz["ip"], $login_window, LOGIN_ERROR_BITS));
			$fail_range = $this->datetime - $first_fail;
			$login_lock_duration = (1 / (($fail_range / $login_window) + 1)) * $fail_range; //inverse function y = 1/x * fail_range with x constrained to between 1 and 2
			if ($login_lock_duration < 15)
				$login_lock_duration = 15; //minimum minutes to block user
			}
		$y = $wpdb->get_var($wpdb->prepare("SELECT count(*) FROM {$this->hits_table} WHERE ip = %d AND datetime > %d AND scorebits & %d", $this->idz["ip"], $login_lock_duration, LOGIN_LOCK_BIT));
		if ($x >= $this->options["login_limit"] OR $y) {
			if ($this->score == -1)
				$this->score++;
			$this->score += 10;
			$this->scorebits |= LOGIN_LOCK_BIT; //00010000b - the login lock bit
			$wpdb->query($wpdb->prepare("UPDATE {$this->iurr_tables["ip"]} SET aux=aux|0x4 WHERE id=%d", $this->idz["ip"]));
			}
		}
	}
else
	$this->loginbits = 0; //oh, what a tangled web we weave
} //process_login_fails

/***********************************************************************************/
function login_failed() { //comes in after the rest of the class has run, so requires some extraordinary juggling
if ($this->options["login_limit"]) {
	global $wpdb;
	$this->loginbits |= 0x800; //1000 0000 0000b - failed manual login
	$this->process_login_fails();
/*
	if (($this->listbits & XWNKS_NOSHOW_BIT)) //wnks 0100b - NoLog - Failed logins are always logged, but a nolog visit never had its hit recorded, so we do it here...
		$wpdb->query($wpdb->prepare("INSERT INTO $this->hits_table (datetime, ip, ua, ref, ruri, scorebits, score) VALUES (%s, %d, %d, %d, %d, %d, %d)", $this->datetime, $this->idz["ip"], $this->idz["ua"], $this->idz["ref"], $this->idz["ruri"], $this->scorebits, $this->score));
	else
*/
	$this->scorebits &= ~NO_SHOW_BIT; //always show failed logins
	$wpdb->query($wpdb->prepare("UPDATE $this->hits_table SET scorebits=%d, score=%d WHERE datetime=%s AND ip=%d", $this->scorebits, $this->score, $this->datetime, $this->idz["ip"]));
	}
} //login_failed

/***********************************************************************************/
function __construct($loginbits) {
parent::__construct();
$this->loginbits = $loginbits;
}
} //class act

/***********************************************************************************/
function ecstatic_new_hit() {
global $loginbits;
if (isset($_POST["ecstatit"])) { //goes here to beat any header output
	include_once(WP_PLUGIN_DIR . "/" . dirname(plugin_basename(__FILE__)) . '/ecstatic_forms.php');
	$ecstatic = new ecstatic_formproc();
	$ecstatic->form_processor(); //function exit()s and doesn't return
	}

$ecstatic = new act($loginbits);

add_action('wp_login_failed', array(&$ecstatic, 'login_failed')); //this login related hook seems to fire later than the others (further below), so its target is jammed into the class above where it plays catch up

$ecstatic->hit_parade();

if (!is_user_logged_in() OR $ecstatic->options["collectloggeduser"] OR $ecstatic->logsubadmin OR $loginbits)
	$ecstatic->append_hit();

if ($ecstatic->options["estats"] > 12345 AND $ecstatic->options["estats"] < $ecstatic->datetime) //eMail stats
	include_once(WP_PLUGIN_DIR . "/" . dirname(plugin_basename(__FILE__)) . '/ecstatic_estats.php');

if ($ecstatic->score > 9 AND !(current_user_can('administrator'))) {
    header('HTTP/1.0 403 Forbidden');
	exit("<br><br><br><center>403:&nbsp; Forbidden</center><br><br>");
	}
} //ecstatic_new_hit

$loginbits = 0;
/***********************************************************************************/
function ecstatic_auth_cookie_bad_hash() {
global $loginbits;
$loginbits |= 0x200;
} //ecstatic_auth_cookie_bad_hash
/***********************************************************************************/
function ecstatic_auth_cookie_bad_username() {
global $loginbits;
$loginbits |= 0x400;
} //ecstatic_auth_cookie_bad_username
/***********************************************************************************/
function ecstatic_integrate() {
include_once(WP_PLUGIN_DIR . "/" . dirname(plugin_basename(__FILE__)) . '/ecstatic_interface.php');
ecstatic_integrate_menu();
} //ecstatic_integrate
/***********************************************************************************/
function ecstatic_widget_fidget() {
$ecstatic = new ecstatic();
if ($ecstatic->options["enablewidget"]) {
	include_once(WP_PLUGIN_DIR . "/" . dirname(plugin_basename(__FILE__)) . '/ecstatic_widget.php');
	register_widget('ecstatic_widget');
	}
} //ecstatic_widget_fidget
/****************************************************/
function ecstatic_details_search() { //AJAX mania - called from ecstatic.js
include_once(WP_PLUGIN_DIR . "/" . dirname(plugin_basename(__FILE__)) . '/ecstatic_interface.php');
include_once(WP_PLUGIN_DIR . "/" . dirname(plugin_basename(__FILE__)) . '/ecstatic_details.php');
$ecstatic = new ecstatic_details();
$ecstatic->assoc_panel("srch", $_POST['detsrch']);
exit;
} //ecstatic_details_search
/****************************************************/
function ecstatic_wnks_filter() { //AJAX mania - ditto above
include_once(WP_PLUGIN_DIR . "/" . dirname(plugin_basename(__FILE__)) . '/ecstatic_details.php');
$ecstatic = new ecstatic();
$wnkstable = new ecstatic_wnkstable($ecstatic);
$wnkstable->show();
exit;
} //ecstatic_wnks_filter
/****************************************************/
function ecstatic_dummy_function() { //more AJAXania - fool Wordpress AJAX interface - function preempted by "ecstatit" in ecstatic_new_hit() above
exit;
} //ecstatic_dummy_function

/************************** hookers ************************************************/
add_action('auth_cookie_bad_hash', 'ecstatic_auth_cookie_bad_hash', 0);
add_action('auth_cookie_bad_username', 'ecstatic_auth_cookie_bad_username', 0);
add_action('init', 'ecstatic_new_hit');
add_action('widgets_init', 'ecstatic_widget_fidget');
add_action('admin_menu', 'ecstatic_integrate');
add_action('wp_ajax_details_search', 'ecstatic_details_search');
add_action('wp_ajax_wnks_filter', 'ecstatic_wnks_filter');
add_action('wp_ajax_dummy_function', 'ecstatic_dummy_function');

/************************** defines *************************************************/
//scorebits
define('BAD_IP_BIT', 0x1);			//0000 0000 0000 0001b
define('EMPTY_UA_BIT', 0x2);			//0000 0000 0000 0010b
define('EMPTY_REF_BIT', 0x4);			//0000 0000 0000 0100b
define('WTF_BIT', 0x8);				//0000 0000 0000 1000b
define('LOGIN_LOCK_BIT', 0x10);		//0000 0000 0001 0000b
define('LOST_PASSWORD_BIT', 0x20);	//0000 0000 0010 0000b
define('TRACKBACK_BIT', 0x40);		//0000 0000 0100 0000b
define('BAD_DOMAIN_BIT', 0x80);		//0000 0000 1000 0000b
define('NEW_BOT_BIT', 0x100);			//0000 0001 0000 0000b
define('LOGIN_ERROR_BITS', 0xe00);	//0000 1110 0000 0000b - failed manual login, bad cookie username, bad cookie hash
define('NO_SHOW_BIT', 0x1000);		//0001 0000 0000 0000b
define('MOZ_PREFETCH_BIT', 0x2000);	//0010 0000 0000 0000b

//listbits - xwhitelist, whitelist, nolog, kill, spider/bot - 1 1111b
define('XWNKS_BOT_BIT', 0x1);
define('XWNKS_KILL_BIT', 0x2);
define('XWNKS_NOSHOW_BIT', 0x4);
define('XWNKS_WHITELIST_BIT', 0x8);
define('XWNKS_XWHITELIST_BIT', 0x10);
/*
define('_BIT', 0x);	//0000 0000 0000 0000b
*/
/************************** Pre-2.6 compatibility **********************************/
if (!defined('WP_CONTENT_URL')) define('WP_CONTENT_URL', get_option('siteurl') . '/wp-content');
if (!defined('WP_CONTENT_DIR')) define('WP_CONTENT_DIR', ABSPATH . 'wp-content');
if (!defined('WP_PLUGIN_URL')) define('WP_PLUGIN_URL', WP_CONTENT_URL. '/plugins');
if (!defined('WP_PLUGIN_DIR')) define('WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins');
?>