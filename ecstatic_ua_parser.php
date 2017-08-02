<?php
/****************************************************************************
Source credit: Marco Von Ballmoos
http://www.earthli.com/software/webcore
http://earthli.com/software/browser_detector/
20130413 - big changes - ms
****************************************************************************/

define ('Browser_netscape_4', 'netscape_4');
define ('Browser_gecko', 'gecko');
define ('Browser_khtml', 'khtml');
define ('Browser_opera', 'opera');
define ('Browser_presto', 'presto');
define ('Browser_webtv', 'webtv');
define ('Browser_ie', 'ie');
define ('Browser_icab', 'icab');
define ('Browser_omniweb', 'omniweb');
define ('Browser_text', 'lynx');
define ('Browser_newsreader', 'newsreader');
define ('Browser_previewer', 'previewer');
define ('Browser_os_windows', 'windows');
define ('Browser_os_mac', 'macos');
define ('Browser_os_linux', 'linux');
define ('Browser_unknown', 'Unknown');

/***************************************/
class USER_AGENT {
public $renderer_ids = array();
public $os_ids = array();
public $system_ids = array();
public $ignored_ids = array();
public $special_case_ids = array();
public $parser;
/***************************************/
function load_from_string($s) {
$rob = $this->parser->make_properties_from($s); //UAP returned
if ($rob->version)
	$rob->browser .= " " . $rob->version;
if ($rob->renderer_version)
	$rob->renderer .= " " . $rob->renderer_version;
return $rob;
}
/***************************************/
function __construct() {
$tables = new USER_AGENT_PARSE_TABLES();
$this->special_case_ids = $tables->special_case_ids();
$this->renderer_ids = $tables->renderer_ids();
$this->os_ids = $tables->os_ids();
$this->system_ids = $tables->system_ids();
if (add_option("ecstatic_ignored_ids"))
	include_once(WP_PLUGIN_DIR . "/" . dirname(plugin_basename(__FILE__)) . '/ecstatic_ignored_ids.php');
$this->ignored_ids = get_option("ecstatic_ignored_ids");
$this->parser = new USER_AGENT_PARSER($this);
}
} //class USER AGENT

/***************************************/
class USER_AGENT_PROPERTIES {
public $browser = Browser_unknown;
public $version = '';
public $renderer = Browser_unknown;
public $renderer_version = '';
public $os = Browser_unknown;
public $ua_ids = array();
public $num_ignored = 0;
} //class USER_AGENT_PROPERTIES

/***************************************/
class USER_AGENT_PARSER {
public $tables;
/***************************************/
function __construct($tables) {
$this->tables = $tables;
}
/***************************************/
function make_properties_from($s) {
$index = 0;
$parts = array();
$renderer = $current_version = $current_renderer = null;
$continue_processing = true;
$browser_is_final = false;
$UAP = new USER_AGENT_PROPERTIES();
$UAP->num_ignored = sizeof($this->tables->ignored_ids);

if (!$s) { //empty user agent string
	$UAP->browser = $UAP->os = $UAP->renderer = "(empty)";
	return $UAP;
	}
foreach($this->tables->special_case_ids as $key => $val) {
	if (stripos($s, $key) !== false) {
		$UAP->browser = $val;
		return $UAP;
		}
	}

preg_match_all ('/([a-zA-Z]|[a-zA-Z]+[0-9]+|[a-zA-Z]+[ 0-9]+[a-zA-Z]|[a-zA-Z][ \-&a-zA-Z]*[a-zA-Z])[-\/: ]?[vV]?([0-9][0-9a-z]*([\.-][0-9][0-9a-z]*)*)/', $s, $parts); //version 3.4

$ids = $parts[1];
$vers = $parts[2];

while ($continue_processing && ($index < sizeof($ids))) {
	$ver = ltrim($vers[$index], "vV");
	$id = strtolower($ids[$index]);
	if (!strcmp(substr($id, -2), ' v')){ //last two chars = " v"
		$id = substr($id, 0, -2);
		$ids[$index] = substr($ids[$index], 0, -2); //preserve case
		}
	if (strlen($id) > 3) {
		if (isset($this->tables->renderer_ids[$id])) {
			$renderer = $this->tables->renderer_ids[$id];
			if (empty($current_renderer) || ($current_renderer->renderer_can_be_overridden())) {
				if ($renderer->is_mozilla_gecko($ver))
					$current_renderer = $this->tables->renderer_ids[Browser_gecko];
				else
					$current_renderer = $renderer;
				if ($id != Browser_gecko)
					$current_version = $ver;
				}
			}
		$UAP->ua_ids[$ids[$index]] = true;
		if (!isset($this->tables->ignored_ids[$id]) && !isset($this->tables->system_ids[$id])) {
			if (!$browser_is_final && (empty($current_renderer) || $current_renderer->browser_can_be_overridden())) {
				$UAP->version = $ver;
				if (isset($this->tables->renderer_ids[$id])) {
					$renderer = $this->tables->renderer_ids[$id];
					if ($renderer->is_mozilla_gecko($ver))
						$UAP->browser = 'Mozilla';
					else
						$UAP->browser = $renderer->display_name;
					if ($current_renderer->precedence == User_agent_final_browser_temporary_renderer)
						$browser_is_final = true;
					$continue_processing = $renderer->continue_processing_ids();
					}
				else
					$UAP->browser = $ids[$index]; // Use the id in original case
				}
			}
		elseif (isset($this->tables->ignored_ids[$id]))
			$UAP->ua_ids[$ids[$index]] = false; //id found in ignored_ids
		}
	$index++;
	} //while

if (!empty($current_renderer)) {
	$UAP->renderer = $current_renderer->technology_name;
	$UAP->renderer_version = $current_version;
	if (!$current_renderer->browser_can_be_overridden()) {
		$UAP->browser = $current_renderer->display_name;
		$UAP->version = $UAP->renderer_version;
		}
	}

$sl = strtolower($s);
while (list($key, $value) = each($this->tables->os_ids)) { //do the OS
	$keys = explode (',', $key);
	$match = true;
	foreach ($keys as $key)
		$match = $match && (strpos($sl, $key) !== false); //match against all keys
	if ($match) {
		$UAP->os = $value;
		break;
		}
	}

if ($UAP->browser == Browser_unknown) { //one last shot at attempted ID
	if (preg_match("/www\.([^.;\/\(,:@]+)|([^.;\/\(,:@]+)/", $s, $match))
		$UAP->browser = trim($match[sizeof($match)-1]);
	}

return $UAP;
} //make_properties_from
} //class USER_AGENT_PARSER

define ('User_agent_temporary_renderer', 1);
define ('User_agent_final_renderer', 2);
define ('User_agent_final_browser', 3);
define ('User_agent_final_browser_abort', 4);
define ('User_agent_final_browser_temporary_renderer', 5);

/***************************************/
class USER_AGENT_RENDERER_INFO {
public $id;
public $technology_name;
public $display_name;
public $precedence;
/***************************************/
function __construct($id, $tech, $prec, $disp = '') {
$this->id = $id;
$this->technology_name = $tech;
$this->precedence = $prec;
if ($disp)
	$this->display_name = $disp;
else
	$this->display_name = $tech;
}
/***************************************/
function renderer_can_be_overridden() {
return ($this->precedence == User_agent_temporary_renderer) || ($this->precedence == User_agent_final_browser_temporary_renderer);
}
/***************************************/
function browser_can_be_overridden() {
return $this->precedence != User_agent_final_browser;
}
/***************************************/
function continue_processing_ids() {
return $this->precedence != User_agent_final_browser_abort;
}
/***************************************/
function is_mozilla_gecko ($ver) {
return ($this->id == Browser_netscape_4) && ($ver[0] > 4);
}
} //class USER_AGENT_RENDERER_INFO

/***************************************/
class USER_AGENT_PARSE_TABLES {
/***************************************/
function renderer_ids() {
//User_agent_temporary_renderer -- Marks a potential renderer and browser
//User_agent_final_browser_abort --Marks a final browser name (skips remaining entries)
//User_agent_final_renderer -- Marks a renderer that is not necessarily the final browser
//User_agent_final_browser_temporary_renderer -- Marks a final browser name, but overridable renderer
//User_agent_final_browser -- Marks a renderer and browser that cannot be replaced
//USER_AGENT_RENDERER_INFO(id, technology_name, precedence, [display_name])
return array (
'mozilla' => new USER_AGENT_RENDERER_INFO (Browser_netscape_4, 'Netscape 4.x', User_agent_temporary_renderer)
,'msie' => new USER_AGENT_RENDERER_INFO (Browser_ie, 'Trident (IE)', User_agent_temporary_renderer, 'Internet Explorer')
,'rv' => new USER_AGENT_RENDERER_INFO (Browser_gecko, 'Gecko', User_agent_temporary_renderer, 'Mozilla')
,'gecko' => new USER_AGENT_RENDERER_INFO (Browser_gecko, 'Gecko', User_agent_temporary_renderer, 'Mozilla')
,'opera' => new USER_AGENT_RENDERER_INFO (Browser_opera, 'Presto (Opera)', User_agent_temporary_renderer, 'Opera')

,'shiira' => new USER_AGENT_RENDERER_INFO (Browser_khtml, 'Webcore', User_agent_final_browser_abort, 'Shiira')
,'chrome' => new USER_AGENT_RENDERER_INFO (Browser_khtml, 'Google Chrome', User_agent_final_browser_abort)

,'applewebkit' => new USER_AGENT_RENDERER_INFO (Browser_khtml, 'Webcore', User_agent_final_renderer)
,'presto' => new USER_AGENT_RENDERER_INFO (Browser_presto, 'Presto (Opera)', User_agent_final_renderer, 'Opera')

,'opera mini' => new USER_AGENT_RENDERER_INFO (Browser_opera, 'Presto (Opera)', User_agent_final_browser_temporary_renderer, 'Opera Mini')

,'netscape6' => new USER_AGENT_RENDERER_INFO (Browser_gecko, 'Netscape', User_agent_final_browser)
,'version' => new USER_AGENT_RENDERER_INFO (Browser_presto, 'Presto (Opera)', User_agent_final_browser, 'Opera')
,'konqueror' => new USER_AGENT_RENDERER_INFO (Browser_khtml, 'KHTML', User_agent_final_browser, 'Konqueror')
,'omniweb' => new USER_AGENT_RENDERER_INFO (Browser_omniweb, 'OmniWeb', User_agent_final_browser)
,'webtv' => new USER_AGENT_RENDERER_INFO (Browser_webtv, 'WebTV', User_agent_final_browser)
,'lynx' => new USER_AGENT_RENDERER_INFO (Browser_text, 'Text', User_agent_final_browser, 'Lynx')
,'icab' => new USER_AGENT_RENDERER_INFO (Browser_icab, 'iCab', User_agent_final_browser)
,'applesyndication' => new USER_AGENT_RENDERER_INFO (Browser_newsreader, 'Safari Newsreader', User_agent_final_browser)
,'netnewswire' => new USER_AGENT_RENDERER_INFO (Browser_newsreader, 'Net News Wire', User_agent_final_browser)
,'yahoofeedseeker' => new USER_AGENT_RENDERER_INFO (Browser_newsreader, 'Yahoo Newsreader', User_agent_final_browser)
,'newsgatoronline' => new USER_AGENT_RENDERER_INFO (Browser_newsreader, 'NewsGator', User_agent_final_browser)
,'bloglines' => new USER_AGENT_RENDERER_INFO (Browser_newsreader, 'Bloglines', User_agent_final_browser)
,'feedfetcher-google' => new USER_AGENT_RENDERER_INFO (Browser_newsreader, 'Google Feedfetcher', User_agent_final_browser)
,'newzcrawler' => new USER_AGENT_RENDERER_INFO (Browser_newsreader, 'NewzCrawler', User_agent_final_browser)
,'facebookexternalhit' => new USER_AGENT_RENDERER_INFO (Browser_previewer, 'Facebook Preview', User_agent_final_browser)
);
} //renderer_ids
/***************************************/
function system_ids() { //list of systems known to provide version info in the user agent.
return array (
'windows nt' => 'Windows NT'
,'win 9x' => 'Windows 9x'
,'linux' => 'Linux'
,'debian' => 'Debian'
,'amigaos' => 'AmigaOS'
,'debian package' => 'Debian'
,'suse' => 'SUSE'
,'series80' => 'Series 80'
,'winnt' => 'Windows NT'
,'freebsd' => 'FreeBSD'
);
} //system_ids
/***************************************/
function os_ids() { //mapping of user agent fragments to platform ids, translating the different user agent platform ids onto standard ones. (e.g. 'nt 4' and 'nt4' both map onto 'Windows NT 4.x').
return array (
'win,nt 6.2' => 'Windows 8'
,'win,nt 6.1' => 'Windows 7'
,'win,nt 6.0' => 'Windows Vista'
,'win,nt 5.2' => 'Windows Server 2003'
,'win,nt 5.1' => 'Windows XP'
,'win,nt 5' => 'Windows 2000'
,'win,2000' => 'Windows 2000'
,'win,9x 4.9' => 'Windows ME'
,'win,98' => 'Windows 98'
,'win,95' => 'Windows 95'
,'win,nt 4' => 'Windows NT 4.x'
,'win,nt4' => 'Windows NT 4.x'
,'win,3.1' => 'Windows 3.1'
,'win,nt 3' => 'Windows NT 3.x'
,'win,nt' => 'Windows NT'
,'win,16' => 'Windows 3.x'
,'win' => 'Windows'
,'mac,68k' => 'MacOS 68k'
,'mac,68000' => 'MacOS 68k'
,'mac,os x' => 'Mac OS X'
,'mac,ppc' => 'MacOS PPC'
,'mac,powerpc' => 'MacOS PPC'
,'macintosh' => 'MacOS'
,'applesyndication' => 'Mac OS X' // Safari newsreader
,'ubuntu' => 'Ubuntu Linux'
,'debian' => 'Debian Linux'
,'linux' => 'Linux'
,'series80' => 'Series 80'
,'amigaos' => 'AmigaOS'
,'beos' => 'BeOS'
,'os/2' => 'OS/2'
,'webtv' => 'WebTV'
,'sunos' => 'Sun/Solaris'
,'irix' => 'Irix'
,'hpux' => 'HP Unix'
,'aix' => 'AIX'
,'dec' => 'DEC-Alpha'
,'alpha' => 'DEC-Alpha'
,'osf1' => 'DEC-Alpha'
,'ultrix' => 'DEC-Alpha'
,'sco' => 'SCO'
,'unix_sv' => 'SCO'
,'vax' => 'VMS'
,'openvms' => 'VMS'
,'sinix' => 'Sinix'
,'reliantunix' => 'Reliant/Unix'
,'freebsd' => 'FreeBSD'
,'openbsd' => 'OpenBSD'
,'netbsd' => 'NetBSD'
,'bsd' => 'BSD'
,'unix_system_v' => 'UnixWare'
,'ncr' => 'MPRAS'
,'x11' => 'Unix'
,'android' => 'Android'
);
}
/***************************************/
function special_case_ids() { //catches exceptions, mostly bots
return array (
'scoutjet' => 'ScoutJet'
,'sistrix' => 'Sistrix'
,'openindex' => 'Openindex'
,'socialsearcher' => 'SocialSearcher'
,'proximic' => 'Proximic'
,'archive.org' => 'Archive.org'
,'80legs' => '80legs'
,'searchmetrics' => 'Searchmetrics'
,'google wireless transcoder' => 'GoogleWirelessTranscoder'
,'stumbleupon' => 'StumbleUpon'
,'trendiction' => 'trendiction'
,'fairshare' => 'Fairshare'
,'butterfly' => 'Topsy/Butterfly'
,'appengine-google' => 'AppEngine-Google'
,'spinn3r' => 'Spinn3r'
,'magpie' => 'magpie'
,'google web preview' => 'GoogleWebPreview'
);
}
} //class USER_AGENT_PARSE_TABLES
?>