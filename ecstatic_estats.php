<?php
global $wpdb;
global $estats_links;

$admin_email = get_option('admin_email');
$headers = "From: {$ecstatic->blogname} <{$admin_email}>\r\n";
$headers .= "Reply-To: {$ecstatic->blogname} <{$admin_email}>\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";

$estats_table = $ecstatic->make_table("estats");
$estats = $wpdb->get_row("SELECT * FROM $estats_table");

$headers .= "X-ecSTATic: {$estats->xheader}\r\n";

$estats_links = $estats->mlinks;

if (!isset($ttt)) { //if not test run
	$nextout  = mktime($estats->email_time, 0, 0, date("m"), date("d")+$estats->email_every, date("Y"));
	$wpdb->query($wpdb->prepare("UPDATE {$ecstatic->options_table} SET estats=%s", $nextout)); //set time trigger for next email
	}

$today_start_time  = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
$statstart = mktime(0, 0, 0, date("m"), date("d")-$estats->email_every, date("Y"));
$startdate = date("m/d/Y", $statstart);
$enddate = date("m/d/Y", $today_start_time-1);
if ($startdate == $enddate)
	$datestr = $startdate;
else
	$datestr = $startdate . " - " . $enddate;

$startcume = date("Y-m-d", $statstart);
$cumes = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$ecstatic->cumulative_table} WHERE day >= %s ORDER BY day ASC LIMIT %d", $startcume, $estats->email_every));

$regi = $regp = $feedi = $feedp = $boti = $botp = 0;
foreach ($cumes as $cu) {
	$regi += $cu->regi;
	$regp += $cu->regp;
	$feedi += $cu->feedi;
	$feedp += $cu->feedp;
	$boti += $cu->boti;
	$botp += $cu->botp;
	}
$sums = $wpdb->get_row("SELECT sum(regi) as 'regi', sum(regp) as 'regp', sum(feedp) as 'feedp', sum(botp) as 'botp' FROM {$ecstatic->cumulative_table}", ARRAY_A);

$body = "<html>\n<head>\n<title>{$ecstatic->blogname} {$datestr}: Visitors: {$regi} Pages: {$regp} Bots: {$boti} Total: {$sums["regi"]}</title>\n";
$body .= "<style type='text/css'>\n";
$body .= "body {color:#222;}\n";
$body .= "h2, h3 {line-height: 0.95em;}\n";
$body .= "table {border-collapse:collapse;}\n";
$body .= "th, td {border:1px solid #ccc;padding: 0 2px;}\n";
$body .= ".tinyh2 {font-family:Verdana,Arial;font-style:normal;font-size:x-small;font-weight:normal;}\n";
$body .= ".sviz {color:#090;font-weight:bolder;}\n";
$body .= ".srss {color:#00f;}\n";
$body .= ".skill {font-style:italic;}\n";
$body .= ".sbot {color:brown;}\n";
$body .= ".na {color:#33a;}\n";
$body .= ".unrecog {color:red;font-weight:bolder;}\n";
$body .= ".sderr {text-decoration:line-through;}\n";
$body .= ".malign {color:red;}\n";
$body .= ".xlogin a:link, .xlogin a:visited {color:firebrick;font-weight:bold;}\n";
$body .= "</style>\n</head>\n<body>\n";

$eba = new estat_before_after($ecstatic);
$seref = new estat_seref($ecstatic);

$h = $ecstatic->hits_table;
$i = $ecstatic->iurr_tables["ip"];
$u = $ecstatic->iurr_tables["ua"];
$r = $ecstatic->iurr_tables["ref"];
$q = $ecstatic->iurr_tables["ruri"];
$ucache = $dcache = $qcache = array();

$NSB = NO_SHOW_BIT;
$rQ = "SELECT $h.datetime, $h.ip AS ipx, $h.ua AS uax, $h.ref AS refx, $h.ruri AS rurix, $h.scorebits, $h.score, $i.ip, $i.score AS ipscore, $u.ua, $u.browser, $u.os, $u.score AS uascore, $r.ref, $r.score AS refscore, $q.ruri, $q.score AS ruriscore FROM $h, $i, $u, $r, $q WHERE $h.ip=$i.id AND $h.ua=$u.id AND $h.ref=$r.id AND $h.ruri=$q.id AND NOT ($h.scorebits & {$NSB}) AND $h.datetime > $statstart AND $h.datetime < $today_start_time ORDER BY $h.datetime DESC";
$hits_today = $wpdb->get_var($wpdb->prepare("SELECT count(*) FROM {$ecstatic->hits_table} WHERE datetime > %d AND datetime < %d", $statstart, $today_start_time));
$hits = $wpdb->get_results($rQ);

$logins = 0;
foreach($hits as $hit) {
	if (strpos($hit->ruri, "login") !== false OR ($hit->scorebits & LOGIN_ERROR_BITS))
		$logins++;
	}

$subject = "";
$qe = preg_match_all("/\[.+?\]|[a-zA-Z0-9]+/", $estats->subject, $shorts); //"/\[.*\]+/U"
foreach ($shorts[0] as $short) {
	switch ($short) {
		case "[date]":
			$subject .= $datestr . " ";
			break;
		case "[visitors]":
			$subject .= "! Visitors: $regi !";
			break;
		case "[pages]":
			$subject .= "! Pages: $regp !";
			break;
		case "[feeds]":
			$subject .= "! Feeds: $feedp !";
			break;
		case "[bots]":
			$subject .= "! Bots: $botp !";
			break;
		case "[totalv]":
			$subject .= "! Total Visitors: {$sums["regi"]} !";
			break;
		case "[totalp]":
			$subject .= "! Total Pages: {$sums["regp"]} !";
			break;
		case "[logins]":
			$subject .= "! Logins: $logins !";
			break;
		default:
			$subject .= " " . $short . " ";
			break;
		} //switch
	} //foreach
$subject = str_replace("!!", "-", $subject);
$subject = trim(str_replace("!", "", $subject));

$body .= "<h2>{$ecstatic->blogname} {$datestr}</h2>\n";
$killp = $hits_today-$botp-$feedp-$regp;
$body .= "<h3>Visitors: {$regi}&nbsp; Pages: {$regp}&nbsp; Feeds: {$feedp}&nbsp; Bots: {$botp}&nbsp; Killed: {$killp}&nbsp; Total: {$hits_today}&nbsp; LogIns: {$logins}<br />All Time Visitors: {$sums["regi"]}&nbsp; All Time Pages: {$sums["regp"]}</h3>\n";
$body .= "<span class='tinyh2'><span class='sviz'>Visitor</span>&nbsp; <span class='srss'>FeedRead</span>&nbsp; <span class='sbot'>Spider/Bot</span>&nbsp; <span class='skill'>KILLed</span>&nbsp; <span class='unrecog'>NewBot</span></span><br />\n";
$body .= "<table summary='sequentialism' cellspacing='0' cellpadding='0'>\n";

$colgroup = "<colgroup>";
$thead = "<thead><tr>";
foreach($estats as $key => $val) {
	if ($val) {
		switch ($key) {
			case "mdate":
				$colgroup .= "<col />";
				$thead .= "<th>Date/Time</th>";
				break;
			case "mip":
				$colgroup .= "<col />";
				$thead .= "<th>IP</th>";
				break;
			case "mipq":
				$colgroup .= "<col />";
				$thead .= "<th>Q</th>";
				break;
			case "mbrowser":
				$colgroup .= "<col />";
				$thead .= "<th>Browser</th>";
				break;
			case "mos":
				$colgroup .= "<col />";
				$thead .= "<th>OS</th>";
				break;
			case "mruri":
				$colgroup .= "<col width=20% />";
				$thead .= "<th>Requested Page</th>";
				break;
			case "mref":
				$colgroup .= "<col width=20% />";
				$thead .= "<th>Referrer</th>";
				break;
			case "mscore":
				$colgroup .= "<col />";
				$thead .= "<th>Score</th>";
				break;
			default:
				break;
			} //switch
		} //if
	} //foreach
$colgroup .= "</colgroup>\n";
$thead .= "</tr></thead>\n<tbody>\n";
$body .= $colgroup . $thead;

foreach($hits as $hit) {
	$td_class = $browser = $renderer = $os = $anchor = $ruri_link = "";
	$body .= "<tr>";
	$its = $hit->datetime;
	$mits = date("m/d H:i:s", $its);
	$imash = $hit->ipx . "." . $hit->uax . "." . $its; //make the mash
	if ($estats->mdate)
		$body .= "<td>" . str_replace(" ", "&nbsp;", $mits) . "</td>\n";

	$ruri_link = estat_makelink($hit->ruri);
	if ($eba->ruriz[$hit->rurix])
		$td_class = "srss";

	if ($hit->ua == "")
		$browser = $os = "(empty)";
	elseif ($hit->browser) {
		$browser = $hit->browser;
		$os = $hit->os;
		}
	else {
		include_once(WP_PLUGIN_DIR . "/" . dirname(plugin_basename(__FILE__)) . '/ecstatic_ua_parser.php');
		if (!isset($ua_parser))
			$ua_parser = new USER_AGENT();
		$rob = $ua_parser->load_from_string($hit->ua);
		$browser = $rob->browser;
		$renderer = $rob->renderer;
		$os = $rob->os;
		if (!$ucache[$hit->uax]) {
			$wpdb->query($wpdb->prepare("UPDATE {$ecstatic->iurr_tables["ua"]} SET browser=%s, os=%s, renderer=%s WHERE id=%d", $browser, $os, $renderer, $hit->uax));
			$ucache[$hit->uax] = true;
			}
		}
	if ($hit->ref != "") {
		$se = $seref->referendum($hit->ref);
		if (isset($se->not_in_aux_se))
			$anchor = " <span style='color:#396;'>" . $se->name . "</span>: " . $se->anchor;
		else
			$anchor = $se->anchor;
		}

	if ($hit->score > 9)
		$td_class .= " skill";
	if ($eba->uaz[$hit->uax] OR $eba->ipz[$hit->ipx])
		$td_class .= " sbot";
	elseif (preg_match("/bot|spider|crawl/i", $hit->ua))
		$td_class .= " unrecog";
	if ($td_class == "")
		$td_class = "sviz";

	if ($estats->mip)
		$body .= "<td>{$hit->ip}</td>";

	if ($estats->mipq) {
		if ($qcache[$hit->ipx])
			$freq = $qcache[$hit->ipx];
		else
			$qcache[$hit->ipx] = $freq = $wpdb->get_var($wpdb->prepare("SELECT count(*) FROM {$ecstatic->hits_table} WHERE ip=%d", $hit->ipx));
		$body .= "<td>{$freq}</td>";
		}

	if ($estats->mbrowser) {
		if (strpos($browser, "Unknown") !== false)
			$body .= "<td class='{$td_class}'>{$hit->ua}</td>"; //td_class
		else
			$body .= "<td class='{$td_class}'>{$browser}</td>"; //td_class
		}
	if ($estats->mos)
		$body .= "<td>{$os}</td>";
//	if ($estats->mruri)
//		$body .= "<td>{$ruri_link}</td>";
	if ($estats->mruri) {
		if (strpos($hit->ruri, "login") !== false OR $hit->scorebits & 0xe10)
			$body .= "<td class='xlogin'>{$ruri_link}</td>";
		else
			$body .= "<td>{$ruri_link}</td>";
		}
	if ($estats->mref)
		$body .= "<td>{$anchor}</td>";

	if ($estats->mscore) {
		$extra_class = "";
		if ($hit->score == -1) {
			$hit->score = "*";
			$extra_class = " wl";
			}

		if ($estats->mlinks)
			$body .= "<td class='{$extra_class}'><a href='" . $ecstatic->url . "/wp-admin/admin.php?page=ecstatic_mash&amp;imash={$imash}' target='_blank'>{$hit->score}</a></td>";
		else
			$body .= "<td class='{$extra_class}'>{$hit->score}</td>";
		}
	$body .= "</tr>\n";
	} //foreach

$body .= "</tbody>\n</table>\n\n";
$body .=  "</body>\n</html>\n";

@wp_mail($estats->addys, $subject, $body, $headers); //wp_mail($to, $subject, $message, [$headers], [$attachments]);


/****************************************************/
function estat_format_requri($uri) {
global $wpdb, $url_bobbed;
static $wp_users_table = "";
static $wp_posts_table = "";

$furi = "";
$uri = htmlspecialchars_decode(urldecode($uri));
if (strpos($uri, "login") !== false)
	$furi .= "Login";
if (strpos($uri, "trackback") !== false) { //apparently can be "wp-trackback.php?p=x", etc., or "?p=x/trackback/"
	$furi .= "Trackback:";
	$uri = preg_replace('/(\/|\/trackback|\/trackback\/)$/', '', $uri);
	}

$permalink_structure = get_option("permalink_structure");
if($permalink_structure == "") { //no fancy permalinks

	$q = explode("?", $uri);
	$uri = rtrim($q[1], "/"); //concentrate on everything after the ?
	if ($uri == "") {
		if ($furi)
			return $furi;
		else
			return "&middot; home &middot;";
		}
	$pairs = explode("&", $uri);

	foreach ($pairs as $pair) {
		if ($furi)
			$furi .= " ";
		list($k, $v) = explode("=", $pair);
		switch ($k) {
			case "author":
				if (!$wp_users_table)
					$wp_users_table = $wpdb->prefix . "users";
				$a = $wpdb->get_var($wpdb->prepare("SELECT display_name FROM $wp_users_table WHERE ID=%d", $v));
				$furi .= "Author: " . $a;
				break;
			case "author_name":
				$furi .= "Author: " . $v;
				break;
			case "cat":
				$furi .= "Cat: " . get_cat_name($v);
				break;
			case "category_name":
				$catobj = get_category_by_slug($v);
				$furi .= "Cat: " . $catobj->name;
				break;
			case "feed":
				$furi .= strtoupper($v) . " feed";
				break;
			case "m":
				$furi .= "Date: ";
				switch (strlen($v)) {
					case 4:
						$furi .= $v;
						break;
					case 6:
						$q = preg_match("/([0-9]{4})([0-9]{2})/", $v, $vm);
						$q = mktime(0, 0, 0, $vm[2], 1, $vm[1]);
						$furi .= date("M Y", $q);
						break;
					case 8:
						$q = preg_match("/([0-9]{4})([0-9]{2})([0-9]{2})/", $v, $vm);
						$q = mktime(0, 0, 0, $vm[2], $vm[3], $vm[1]);
						$furi .= date("M d, Y", $q);
						break;
					}
				break;
			case "p":
				$post = get_post($v);
				$furi .= $post->post_title;
				break;
			case "page_id":
				$post = get_page($v);
				$furi .= "Page: " . $post->post_title;
				break;
			case "paged":
				$furi .= " (pg. {$v})";
				break;
			case "s":
				$furi .= "Search: {$v}";
				break;
			case "tag":
			case "pagename":
			case "comments_popup":
			case "comments":
			case "minute":
			case "hour":
			case "day":
			case "monthnum":
			case "year":
			case "name":
			default:
				$furi .= $k . ":" . $v . " ";
				break;
			} //switch
		} //foreach
	return $furi;
	}
else { //************************************************** fancy permalinks (yaaay)
	if (strpos($uri, "?s=") !== false) {
		$st = explode("=", $uri);
		if ($furi)
			$furi .= " ";
		$furi .= "Search: " . $st[1];
		return $furi;
		}

	$sbob = strlen($url_bobbed);
	$uri = trim(substr($uri, $sbob), "/");
	if ($uri == "") {
		if ($furi)
			return $furi;
		else
			return "&middot; home &middot;";
		}

	$tag_base = get_option('tag_base');
	$category_base = get_option('category_base');

	$pnum = false; //flag for "/" control in dates
	$post_title = "";
	if (!$wp_posts_table)
		$wp_posts_table = $wpdb->prefix . "posts";

	$permalink_structure = trim($permalink_structure, "/");
	$permarray = explode("/", $permalink_structure);
	$uri = trim($uri, "/");
	$uri_parm = explode("/", $uri);
	for ($u=0; $u<sizeof($uri_parm); $u++) {
		if ($furi AND !$pnum)
			$furi .= " ";
		switch ($uri_parm[$u]) {
			case "category":
			case $category_base:
				$furi .= "Cat: " . $uri_parm[++$u];
				break;
			case "tag":
			case $tag_base:
				$furi .= "Tag: " . $uri_parm[++$u];
				break;
			case "page":
				$furi .= "(pg. " . $uri_parm[++$u] . ")";
				break;
			case "feed":
				$furi .= "Feed";
				break;
			case "date":
				while ($u < sizeof($uri_parm)) {
					$furi .= $uri_parm[++$u];
					if ($u < sizeof($uri_parm)-1)
						$furi .= "/";
					} //while
				break;
			default:
				switch ($permarray[$u]) {
					case "%year%":
					case "%monthnum%":
					case "%day%":
					case "%hour%":
					case "%minute%":
					case "%second%":
						if ($pnum)
							$furi .= "/";
						$furi .= $uri_parm[$u];
						$pnum = true;
						break;
					case "%post_id%":
						$postobj = $wpdb->get_row($wpdb->prepare("SELECT post_title, post_type FROM $wp_posts_table WHERE ID = %d", $uri_parm[$u]));
						if ($postobj->post_type == "post")
							$post_title = $postobj->post_title;
						elseif ($postobj->post_type == "page")
							$post_title = "Page: " . $postobj->post_title;
						else
							$furi .= "/" . $uri_parm[$u];
						$pnum = false;
						break;
					case "%postname%":
						$postobj = $wpdb->get_row($wpdb->prepare("SELECT post_title, post_type FROM $wp_posts_table WHERE post_name = %s", $uri_parm[$u]));
						if ($postobj->post_type == "post")
							$post_title = $postobj->post_title;
						elseif ($postobj->post_type == "page")
							$post_title = "Page: " . $postobj->post_title;
						else
							$furi .= "/" . $uri_parm[$u];
						$pnum = false;
						break;
					case "%category%":
						$furi .= "Cat: " . $uri_parm[$u];
						$pnum = false;
						break;
					case "%author%":
						$furi .= "Author: " . $uri_parm[$u];
						$pnum = false;
						break;
					case "date":
						$furi .= "Date: ";
						$pnum = false;
						break;
					case "archives":
						$pnum = false;
						break;
					default:
						$furi .= "/" . $uri_parm[$u];
						$pnum = false;
						break;
					} //switch
				break;
			} //switch
		} //for

	if ($post_title)
		$furi .= " " . $post_title;

	return $furi;
	} //else
} //estat_format_requri

/****************************************************/
function estat_makelink($arg) {
global $plain_ruris, $schost, $estats_links;
if ($plain_ruris)
	if ($estats_links)
		$narg = "<a href='{$schost}" . $arg . "' title='{$schost}" . $arg . "' target='_blank'>" . $arg . "</a>";
	else
		$narg = $arg;
else
	if ($estats_links)
		$narg = "<a href='{$schost}" . $arg . "' title='{$schost}" . $arg . "' target='_blank'>" . estat_format_requri($arg) . "</a>";
	else
		$narg = estat_format_requri($arg);
return $narg;
} //estat_makelink

/****************************************************/
class estat_before_after {
public $uaz;
public $ipz;
public $ruriz;
/****************************************************/
function __construct($ecstatic) { //classify new uas & ips into spider/non-spider, classify new referrer strings as non-se/se, and preload spider and feed indices
global $wpdb;
$zr = $wpdb->get_results("SELECT id, ua, aux FROM {$ecstatic->iurr_tables["ua"]} WHERE aux^1");
foreach ($zr as $z) {
	if ($z->aux == 0) {
		if ($ecstatic->is_in_lists2("", $z->ua, "", "", 0x1)) { //wnks 0001b - spider
			$this->uaz[$z->id] = true;
			$z->aux = 2;
			}
		else
			$z->aux = 1;
		$wpdb->query($wpdb->prepare("UPDATE {$ecstatic->iurr_tables["ua"]} SET aux=%d WHERE id=%d", $z->aux, $z->id));
		}
	else
		$this->uaz[$z->id] = true;
	}

$zr = $wpdb->get_results("SELECT id, ip, aux FROM {$ecstatic->iurr_tables["ip"]} WHERE aux^1&3"); //select for aux=0 and aux=2, regardless of aux with the 0100b set
foreach ($zr as $z) {
	if (!($z->aux & 0x2)) { //if not aux=2
		if ($ecstatic->is_in_lists2($z->ip, "", "", "", 0x1)) { //wnks 0001b - spider
			$this->ipz[$z->id] = true;
			$z->aux |= 0x2;
			}
		else
			$z->aux |= 0x1;
		$wpdb->query($wpdb->prepare("UPDATE {$ecstatic->iurr_tables["ip"]} SET aux=%d WHERE id=%d", $z->aux, $z->id));
		}
	else
		$this->ipz[$z->id] = true;
	}

$zr = $wpdb->get_results($wpdb->prepare("SELECT id FROM {$ecstatic->iurr_tables["ruri"]} WHERE ruri LIKE %s", "%feed%"));
foreach ($zr as $z)
	$this->ruriz[$z->id] = true;

$zr = $wpdb->get_results("SELECT id, ref, aux FROM {$ecstatic->iurr_tables["ref"]} WHERE aux=0");
if(sizeof($zr)){
	$seref = new estat_seref($ecstatic); //**********************************************************************differs from function in ecstatic_interface.php
	foreach ($zr as $z) {
		$se = $seref->referendum($z->ref); //parse the referrer
		if (isset($se->qvar)) //search engine ref
			$z->aux = 2;
		else
			$z->aux = 1;
		$wpdb->query($wpdb->prepare("UPDATE {$ecstatic->iurr_tables["ref"]} SET aux=%d WHERE id=%d", $z->aux, $z->id));
		}
	}
} //constructor
} //estat_before_after

/****************************************************/
class estat_seref { //search engine referrer mangling.
public $sez = array();
public $refparts = array(); //scheme (protocol), host, port, user, pass, path, query, fragment

/****************************************************/
function better_parse_str($query) {
$qparts = array();
$u = explode("&", $query);
foreach($u as $z) {
	list($qsig, $qvar) = explode("=", $z, 2);
	$qparts[$qsig] = urldecode($qvar);
	if ($qsig == "prev" AND (strpos($this->refparts["host"], "google")) !== false) { //extra google image processing
		$sub_qvar = explode("?", $qparts[$qsig]);
		$sub_sub_qvar = explode("&", $sub_qvar[1]);
		list($qsig, $qvar) = explode("=", $sub_sub_qvar[0], 2);
		$qparts[$qsig] = urldecode($qvar);
		}
	}
return $qparts;
} //better_parse_str

/****************************************************/
function research($path="") {
$uri = $this->refparts['scheme'] . "://" . $this->refparts['host'];
if ($path == "")
	$uri .= (substr($this->refparts['path'],0,1) == '/') ? $this->refparts['path'] : '/' . $this->refparts['path'];
elseif ($path != "none")
	$uri .= $path;
return $uri;
} //research

/****************************************************/
function referendum($ref) {
global $estats_links;
$reefer = new stdClass;
$this->refparts = @parse_url($ref);
if (!isset($this->refparts["scheme"]) OR strpos($this->refparts["scheme"], "http") === false) {
	$reefer->anchor = "<span class='malign'>" . $ref . "</span>";
	$reefer->host = "*-malign referrer-*"; //for little graphs
	return $reefer;
	}
if (array_key_exists("query", $this->refparts)) {
	$query = $this->refparts["query"];
	$qparts = $this->better_parse_str($query);
	$sep = "?";
	foreach($this->sez as $se) { //look for query signature in aux_se table
		if (strpos($se->token, "*"))
			$tk = "~" . str_replace("*", ".+", $se->token) . "~";
		else
			$tk = "~" . $se->token . "~";
		if (preg_match($tk, $ref)) {
			foreach ($qparts as $qsig => $qvar) {
				if ($qsig == $se->qsig AND $qvar) {
					$reefer->name = $se->name;
					$reefer->qvar = $qvar;
					$research = $this->research($se->path);
					if (strpos($se->path, "?") !== false)
						$sep = "&";
					$research .= $sep . $se->qsig . "=" . rawurlencode($reefer->qvar);
					if ($estats_links)
						$reefer->anchor = "<a href='{$research}' title='{$ref}' target='_blank'>{$reefer->qvar}</a>";
					else
						$reefer->anchor = $reefer->qvar;
					$reefer->not_in_aux_se = true;
					return $reefer;
					}
				}
			}
		}
	foreach ($qparts as $qsig => $qvar) { //look for generic query signatures
		if(preg_match('~^(q|p|w|s|su|ask|search|searchfor|query|key|keywords|buscar|qry|pesquisa|question|word)$~i', $qsig) AND $qvar){
			if(ctype_digit(trim($qvar))) //generally p=[0-9]+ , i.e., another blog referrer
				break;
			$reefer->name = $this->refparts["host"];
			$reefer->qvar = $qvar;
			$research = $this->research();
			$research .= "?" . $qsig . "=" . rawurlencode($reefer->qvar);
			if ($estats_links)
				$reefer->anchor = "<a href='{$research}' title='{$ref}' target='_blank'>{$reefer->qvar}</a>";
			else
				$reefer->anchor = $reefer->qvar;
			$reefer->not_in_aux_se = true;
			return $reefer;
			}
		}
	}
if (strpos($ref, "google") !== false AND $this->refparts["path"]) { //Apr 19, 2013 - account for google referrers that can't be reverse engineered
	if (strpos($ref, "google") !== false) {
		$reefer->qvar = " ";
		$reefer->name = $ref;
		$reefer->host = $this->refparts["scheme"] . "://" . $this->refparts["host"];
		if ($estats_links)
			$reefer->anchor = "<a href='" . $ref . "' title='{$ref}' target='_blank'>{$reefer->host}</a>";
		else
			$reefer->anchor = $reefer->host;
		return $reefer;
		}
	}
$ref = htmlentities($ref);
$reefer->name = $ref; //used in little graphs, and build_rfbk_record
$reefer->host = $this->refparts["scheme"] . "://" . $this->refparts["host"];
if ($estats_links)
	$reefer->anchor = "<a href='{$ref}' title='{$ref}' target='_blank'>{$reefer->host}</a>";
else
	$reefer->anchor = $reefer->host;
return $reefer;
} //referendum

/****************************************************/
function __construct($ecstatic) {
global $wpdb;
$table_name = $ecstatic->make_table("aux_se");
$this->sez = $wpdb->get_results("SELECT * FROM $table_name");
}
} //class estat_seref
?>