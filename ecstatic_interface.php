<?php
/****************************************************/
function ecstatic_integrate_menu() {
$role_capability = "manage_options";
//add_menu_page(page_title, menu_title, role/capability, file, [function], [icon_url]);
add_menu_page('ecSTATic', 'ecSTATic', $role_capability, __FILE__, 'ecstatic_main', WP_PLUGIN_URL . '/' . dirname(plugin_basename(__FILE__)) . '/ecstatic.png');
//add_submenu_page(parent, page_title, menu_title, role/capability, file, [function]);
$a = add_submenu_page(__FILE__, 'Main', 'Main', $role_capability, __FILE__, 'ecstatic_main');
$b = add_submenu_page(__FILE__, 'Visitors', 'Visitors', $role_capability, 'ecstatic_visitors', 'ecstatic_visitors');
$c = add_submenu_page(__FILE__, 'Feeds', 'Feeds', $role_capability, 'ecstatic_feeds', 'ecstatic_feeds');
$d = add_submenu_page(__FILE__, 'Spider/Bots', 'Spider/Bots', $role_capability, 'ecstatic_spiderbots', 'ecstatic_spiderbots');
$e = add_submenu_page(__FILE__, 'Maleagents', 'Maleagents', $role_capability, 'ecstatic_kills', 'ecstatic_kills');
$f = add_submenu_page(__FILE__, 'Sequential', 'Sequential', $role_capability, 'ecstatic_sequential', 'ecstatic_sequential');
$g = add_submenu_page(__FILE__, 'SomeStats', 'SomeStats', $role_capability, 'ecstatic_play', 'ecstatic_play');
$m = add_submenu_page(__FILE__, 'New Charts', 'New Charts', $role_capability, 'ecstatic_charts', 'ecstatic_charts');
$k = add_submenu_page(__FILE__, 'Manual Purge', 'Manual Purge', $role_capability, 'ecstatic_manual_purge', 'ecstatic_manual_purge');
if (isset($_GET["imash"]))
	$j = add_submenu_page('', '', '', $role_capability, 'ecstatic_mash', 'ecstatic_mash');
$h = add_submenu_page(__FILE__, 'Settings', 'Settings', $role_capability, 'ecstatic_options', 'ecstatic_options');
$i = add_submenu_page(__FILE__, 'Help!', 'Help!', $role_capability, 'ecstatic_help', 'ecstatic_help');

add_action("admin_print_styles-$a", 'jam_ecstatic_stylesheet');
add_action("admin_print_styles-$b", 'jam_ecstatic_stylesheet');
add_action("admin_print_styles-$c", 'jam_ecstatic_stylesheet');
add_action("admin_print_styles-$d", 'jam_ecstatic_stylesheet');
add_action("admin_print_styles-$e", 'jam_ecstatic_stylesheet');
add_action("admin_print_styles-$f", 'jam_ecstatic_stylesheet');
add_action("admin_print_styles-$g", 'jam_ecstatic_stylesheet');
add_action("admin_print_styles-$h", 'jam_ecstatic_stylesheet');
add_action("admin_print_styles-$i", 'jam_ecstatic_stylesheet');
add_action("admin_print_styles-$j", 'jam_ecstatic_stylesheet');
add_action("admin_print_styles-$k", 'jam_ecstatic_stylesheet');
add_action("admin_print_styles-$m", 'jam_ecstatic_stylesheet');

add_action("admin_print_scripts-$a", 'jam_ecstatic_javascript');
add_action("admin_print_scripts-$b", 'jam_ecstatic_javascript');
add_action("admin_print_scripts-$c", 'jam_ecstatic_javascript');
add_action("admin_print_scripts-$d", 'jam_ecstatic_javascript');
add_action("admin_print_scripts-$e", 'jam_ecstatic_javascript');
add_action("admin_print_scripts-$f", 'jam_ecstatic_javascript');
add_action("admin_print_scripts-$j", 'jam_ecstatic_javascript');
} //ecstatic_integrate_menu

/****************************************************/
function jam_ecstatic_javascript() {
wp_enqueue_script('jquery');
wp_enqueue_script('ecstatic_tinytable', WP_PLUGIN_URL . '/' . dirname(plugin_basename(__FILE__)) . '/tinytablev2.js', false, "07"); //change the final parameter if the .js file changes.  Value not important, but a change must be made to override cache.
if (!isset($_GET["se"]) AND isset($_GET["imash"])) {
	wp_enqueue_script('ecstatic_js', WP_PLUGIN_URL . '/' . dirname(plugin_basename(__FILE__)) . '/ecstatic.js', false, "08"); //as above on the final (version) parameter

echo <<<XXX
<script type="text/javascript">
	document.write('<style type="text/css">');
	document.write('div.domtab div.tab {display:none;}<');
	document.write('/s'+'tyle>');
</script>

XXX;
	}
} //jam_ecstatic_javascript

/****************************************************/
function jam_ecstatic_stylesheet() {
wp_enqueue_style('ecSTATicCSS', WP_PLUGIN_URL . '/' . dirname(plugin_basename(__FILE__)) . "/ecstatic.css");
if (file_exists("../wp-content/ecstatic/my_ecstatic.css"))
	wp_enqueue_style('MYecSTATicCSS', content_url() . "/ecstatic/my_ecstatic.css");
} //jam_ecstatic_stylesheet

/****************************************************/
function ecstatic_ecstatic($ecstatic, $title) { //logo version and page title
echo "<div id='ecbanner'>";
echo "&nbsp; ecSTATic <span class='version'>version {$ecstatic->version}</span><span class='pagetitle'>{$title}</span>";
echo "</div><!--ecbanner-->\n\n";
/*** output test stub
echo "<pre>";
var_dump($x);
echo "</pre>";
*/
/*
$qq = 0x1004;
$format = '%0' . (PHP_INT_SIZE * 4) . "b<br />\n";
$gg = 0x1e15;
printf('&~  val=' . $format, $gg);
$gg &= ~NO_SHOW_BIT;
printf('&~  val=' . $format, $gg);
*/
} //ecstatic_ecstatic

/****************************************************/
function ecstatic_banner_graph($ecstatic) {
global $wpdb;

if (!$ecstatic->options["showbannergraph"])
	return;

$graph_width = "99"; //in percent, dude
$mdata = $mdat = array();
$days_to_graph = $ecstatic->options["daystograph"] - 1;
$today_start_time = $ecstatic->zero_am_today; //midnight zero
$start_day_i = (int)(date('Ymd', $today_start_time - 86400 * $days_to_graph));
$start_day_s = date('Y-m-d', $today_start_time - 86400 * $days_to_graph);
$start_of_week = get_option('start_of_week'); //Monday
$tyear = date('Y', $today_start_time);
$tmonth = date('m', $today_start_time);
$vmonth = date('M', $today_start_time);
$vday = date('d', $today_start_time);

$start_year = date("Y-m-d", mktime(0, 0, 0, $tmonth-1, 1, $tyear-1));
$cume_table = $ecstatic->make_table("cumulative");
$cumq = $wpdb->get_results($wpdb->prepare("SELECT * FROM $cume_table WHERE day > %s ORDER BY day ASC", $start_year));
$cur_mo = strtotime(date("M", $today_start_time) . $tyear);
$cur_year = strtotime("jan 1 " . $tyear);

foreach ($cumq as $yq) {
	$month = date("Y-m", strtotime($yq->day));
	if ($ecstatic->options["showgraphregi"]) {
		$mdata[$month]["regi"] += $yq->regi;
		$mdat[$month] += $yq->regi;
		}
	if ($ecstatic->options["showgraphregp"]) {
		$mdata[$month]["regp"] += $yq->regp;
		$mdat[$month] += $yq->regp;
		}
	if ($ecstatic->options["showgraphfeedi"]) {
		$mdata[$month]["feedi"] += $yq->feedi;
		$mdat[$month] += $yq->feedi;
		}
	if ($ecstatic->options["showgraphfeedp"]) {
		$mdata[$month]["feedp"] += $yq->feedp;
		$mdat[$month] += $yq->feedp;
		}
	if ($ecstatic->options["showgraphboti"]) {
		$mdata[$month]["boti"] += $yq->boti;
		$mdat[$month] += $yq->boti;
		}
	if ($ecstatic->options["showgraphbotp"]) {
		$mdata[$month]["botp"] += $yq->botp;
		$mdat[$month] += $yq->botp;
		}
	$oday = strtotime($yq->day);
	if ($oday >= $cur_year) {
		$viz["xyear"] += $yq->regi;
		$pages["xyear"] += $yq->regp;
		$bots["xyear"] += $yq->botp;
		$feeds["xyear"] += $yq->feedp;
		if ($oday >= $cur_mo) {
			$viz["xmo"] += $yq->regi;
			$pages["xmo"] += $yq->regp;
			$bots["xmo"] += $yq->botp;
			$feeds["xmo"] += $yq->feedp;
			if ($oday == $today_start_time) {
				$viz["xday"] += $yq->regi;
				$pages["xday"] += $yq->regp;
				$bots["xday"] += $yq->botp;
				$feeds["xday"] += $yq->feedp;
				}
			}
		}
	} //foreach
if (!empty($mdat))
	$numax = max($mdat); //use that variable once and then throw it away!
else
	$numax = 100;

$td_width = sprintf("%1.0f%%", ($graph_width - 12) / ($days_to_graph + 14)); //99% - width of small cume table / days to graph + months to graph

$find_max_str = "";
if ($ecstatic->options["showgraphregi"])
	$find_max_str .= "+regi";
if ($ecstatic->options["showgraphregp"])
	$find_max_str .= "+regp";
if ($ecstatic->options["showgraphfeedi"])
	$find_max_str .= "+feedi";
if ($ecstatic->options["showgraphfeedp"])
	$find_max_str .= "+feedp";
if ($ecstatic->options["showgraphboti"])
	$find_max_str .= "+boti";
if ($ecstatic->options["showgraphbotp"])
	$find_max_str .= "+botp";

$r = "SELECT MAX(" . $find_max_str . ") FROM $cume_table WHERE day >= %s"; //2009-09-27: changed >= to >
$most = $wpdb->get_var($wpdb->prepare($r, $start_day_s));
if (!$most) //whatever
	$most = 100; //imperfect, at best

$px_regi = $px_regp = $px_feedi = $px_feedp = $px_boti = $px_botp = 0;
echo "<table width='{$graph_width}%' border='0' summary='banner'><tr>\n";
foreach ($mdata as $key=>$cq) {
	if ($ecstatic->options["showgraphregi"])
		$px_regi = round($cq[regi] * 100 / $numax);
	if ($ecstatic->options["showgraphregp"])
		$px_regp = round($cq[regp] * 100 / $numax);
	if ($ecstatic->options["showgraphfeedi"])
		$px_feedi = round($cq[feedi] * 100 / $numax);
	if ($ecstatic->options["showgraphfeedp"])
		$px_feedp = round($cq[feedp] * 100 / $numax);
	if ($ecstatic->options["showgraphboti"])
		$px_boti = round($cq[boti] * 100 / $numax);
	if ($ecstatic->options["showgraphbotp"])
		$px_botp = round($cq[botp] * 100 / $numax);
	$px_white = 100 - $px_regi - $px_regp - $px_feedi - $px_feedp - $px_boti - $px_botp;
	echo "<td width='{$td_width}' valign='bottom'>\n";
	echo "<div class='bannergraph'>\n";
	echo "\t<div class='px_white' style='height:{$px_white}px;'></div>\n";
	if ($px_botp)
		echo "\t<div class='botp' style='height:{$px_botp}px;' title='{$cq[botp]} bot pages'></div>\n";
	if ($px_boti)
		echo "\t<div class='Bots' style='height:{$px_boti}px;' title='{$cq[boti]} bots'></div>\n";
	if ($px_feedp)
		echo "\t<div class='feedp' style='height:{$px_feedp}px;' title='{$cq[feedp]} feed pages'></div>\n";
	if ($px_feedi)
		echo "\t<div class='RSS' style='height:{$px_feedi}px;' title='{$cq[feedi]} feeds'></div>\n";
	if ($px_regp)
		echo "\t<div class='Pages' style='height:{$px_regp}px;' title='{$cq[regp]} ind pages'></div>\n";
	if ($px_regi)
		echo "\t<div class='Visits' style='height:{$px_regi}px;' title='{$cq[regi]} individuals'></div>\n";
	echo "\t<div class='bline'></div>\n\t";

	$hack = strtotime($key); //the whole thing's a freakin' hack, man
	$month = date("M", $hack);
	$year = date("y", $hack);
	echo "{$month}<br />'{$year}\n</div></td>\n";
	} //foreach

$hits_table = $ecstatic->make_table("hits");
$hitq = $wpdb->get_results($wpdb->prepare("SELECT datetime FROM $hits_table WHERE score > %d ORDER BY datetime ASC", 9));
foreach ($hitq as $hit) {
	if ($hit->datetime >= $cur_year) {
		$kill["xyear"]++;
		if ($hit->datetime >= $cur_mo) {
			$kill["xmo"]++;
			if ($hit->datetime >= $today_start_time)
				$kill["xday"]++;
			}
		}
	}

echo "<td style='width:12%;' valign='top'>";
echo "<div style='height:110px; border:4px solid #fff;'>";
echo "<table class='tiny_gtable' summary='cumulative table'>\n";
echo "<thead><tr><th></th><th>{$vday}</th><th>{$vmonth}</th><th>{$tyear}</th></tr></thead>\n";
$cats = array( "Visits" => $viz, "Pages" => $pages, "RSS" => $feeds, "Bots" => $bots, "KILL" => $kill);
$shortcut = array("Visits" => "ecstatic/ecstatic_interface.php", "Pages" => "ecstatic_visitors", "RSS" => "ecstatic_feeds", "Bots" => "ecstatic_spiderbots", "KILL" => "ecstatic_kills");
$title = array("Visits" => "Main Panels", "Pages" => "Reg. Visitor Panel", "RSS" => "Feed Panel", "Bots" => "Spider/Bot Panel", "KILL" => "Maleagents");
foreach ($cats as $key => $cat) {
	echo "<tr class='{$key}'>\n";
	$sc = "<a href='" . $ecstatic->url . "/wp-admin/admin.php?page=" . $shortcut[$key] . "' title='" . $title[$key] . "'>" . $key . "</a>";
	echo "<td style='padding-left: 4px;'>{$sc}</td>\n";
	echo "<td align='right'>{$cat["xday"]}</td>\n";
	echo "<td align='right'>{$cat["xmo"]}</td>\n";
	echo "<td align='right'>{$cat["xyear"]}</td>\n";
	echo "</tr>\n";
	}
echo "</table>";
echo "</div></td>";

$px_regi = $px_regp = $px_feedi = $px_feedp = $px_boti = $px_botp = 0;
foreach ($cumq as $cq) {
	$day = (int)(date("Ymd", strtotime($cq->day)));
	if ($day < $start_day_i)
		continue;
	if ($ecstatic->options["showgraphregi"])
		$px_regi = round($cq->regi * 100 / $most);
	if ($ecstatic->options["showgraphregp"])
		$px_regp = round($cq->regp * 100 / $most);
	if ($ecstatic->options["showgraphfeedi"])
		$px_feedi = round($cq->feedi * 100 / $most);
	if ($ecstatic->options["showgraphfeedp"])
		$px_feedp = round($cq->feedp * 100 / $most);
	if ($ecstatic->options["showgraphboti"])
		$px_boti = round($cq->boti * 100 / $most);
	if ($ecstatic->options["showgraphbotp"])
		$px_botp = round($cq->botp * 100 / $most);
	$px_white = 100 - $px_regi - $px_regp - $px_feedi - $px_feedp - $px_boti - $px_botp;
	$the_day = strtotime($cq->day);
	$the_date = date('M d, Y', $the_day);
	$the_day_anchor = "<a title='Show {$the_date}' href='" . $ecstatic->url . "/wp-admin/admin.php?page=ecstatic/ecstatic_interface.php&amp;showday={$the_day}'>" . date('d', $the_day) . "<br />" . date('M', $the_day) . "</a>";
	echo "<td width='{$td_width}' valign='bottom'>\n";
	if ($start_of_week == date('w', $the_day+86400))
		echo "<div class='bannergraph weekmark'>\n";
	else
		echo "<div class='bannergraph'>\n";
	echo "\t<div class='px_white' style='height:{$px_white}px;'></div>\n";
	if ($px_botp)
		echo "\t<div class='botp' style='height:{$px_botp}px;' title='{$cq->botp} bot pages'></div>\n";
	if ($px_boti)
		echo "\t<div class='Bots' style='height:{$px_boti}px;' title='{$cq->boti} bots'></div>\n";
	if ($px_feedp)
		echo "\t<div class='feedp' style='height:{$px_feedp}px;' title='{$cq->feedp} feed pages'></div>\n";
	if ($px_feedi)
		echo "\t<div class='RSS' style='height:{$px_feedi}px;' title='{$cq->feedi} feeds'></div>\n";
	if ($px_regp)
		echo "\t<div class='Pages' style='height:{$px_regp}px;' title='{$cq->regp} ind pages'></div>\n";
	if ($px_regi)
		echo "\t<div class='Visits' style='height:{$px_regi}px;' title='{$cq->regi} individuals'></div>\n";

	echo "\t<div class='bline'></div>\n\t";
	echo $the_day_anchor . "\n</div></td>\n";
	} //foreach
echo "</tr></table>\n";
} //ecstatic_banner_graph

/****************************************************/
function ecstatic_loadlatest() {
$loadlatest = get_option("ecstatic_loadlatest", 0);
if (isset($_POST["latest"])) {
	if (is_numeric($_POST["latest"])) {
		if ($loadlatest != $_POST["latest"]) {
			$loadlatest = $_POST["latest"];
			update_option("ecstatic_loadlatest", $_POST["latest"]);
			}
		}
	}
return $loadlatest;
} //ecstatic_loadlatest

/****************************************************/
function ecstatic_format_requri($uri) { //called by play_stats, ecstatic_makelink
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
				$furi .= "Date: ";
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
						$pnum = false;
						$postobj = $wpdb->get_row($wpdb->prepare("SELECT post_title, post_type FROM $wp_posts_table WHERE ID = %d", $uri_parm[$u]));
						if ($postobj->post_type == "post")
							$post_title = $postobj->post_title;
						elseif ($postobj->post_type == "page")
							$post_title = "Page: " . $postobj->post_title;
						else {
							while ($u < sizeof($uri_parm)) {
								$furi .= $uri_parm[$u++];
								if ($u < sizeof($uri_parm))
									$furi .= "/";
								} //while
							}
						break;
					case "%postname%":
						$pnum = false;
						$postobj = $wpdb->get_row($wpdb->prepare("SELECT post_title, post_type FROM $wp_posts_table WHERE post_name = %s", $uri_parm[$u]));
						if ($postobj->post_type == "post")
							$post_title = $postobj->post_title;
						elseif ($postobj->post_type == "page")
							$post_title = "Page: " . $postobj->post_title;
						else {
							while ($u < sizeof($uri_parm)) {
								$furi .= $uri_parm[$u++];
								if ($u < sizeof($uri_parm))
									$furi .= "/";
								} //while
							}
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
						while ($u < sizeof($uri_parm)) {
							$furi .= $uri_parm[$u++];
							if ($u < sizeof($uri_parm))
								$furi .= "/";
							} //while
						break;
					} //switch
			} //switch
		} //for

	if ($post_title)
		$furi .= " " . $post_title;

	return $furi;
	} //else
} //ecstatic_format_requri

/****************************************************/
function ecstatic_makelink($arg) {
global $plain_ruris, $schost;
if ($plain_ruris)
	$narg = "<a href='{$schost}" . $arg . "' title='{$schost}" . $arg . "' target='_blank'>" . $arg . "</a>";
else
	$narg = "<a href='{$schost}" . $arg . "' title='{$schost}" . $arg . "' target='_blank'>" . ecstatic_format_requri($arg) . "</a>";
return $narg;
} //ecstatic_makelink

/****************************************************/
function ecstatic_href_ua($ua) {
$tua = urldecode($ua);
$mercy = preg_match("/http[^;,)[:space:]]*/", $tua, $href);
if ($mercy)
	$ua = preg_replace("/http[^;,)[:space:]]*/", "<a href='{$href[0]}' target='_blank'>{$href[0]}</a>" , $tua);
else {
	$lordy = preg_match("/www\.[^;,)[:space:]]*/", $tua, $href);
	if ($lordy)
		$ua = preg_replace("/www\.[^;,)[:space:]]*/", "<a href='http://{$href[0]}' target='_blank'>{$href[0]}</a>" , $tua);
	}
return $ua;
} //ecstatic_href_ua

/****************************************************/
class ecstatic_seref { //search engine referrer mangling.
public $sez = array();
public $refparts = array(); //scheme (protocol), host, port, user, pass, path, query, fragment
private $anonref = 0;

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
$reefer = new stdClass;
$anonref = array(0 => "", 1 => "http://anonym.to/?", 2 => "http://surfsneaky.org/?", 3 => "http://linkscheck.net/?", 4 => "http://www.nullrefer.com/?", 5 => "http://urlink2.com/?");
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
					$reefer->anchor = "<a href='" . $anonref[$this->anonref] . $research . "' title='{$ref}' target='_blank'>{$reefer->qvar}</a>";
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
			$reefer->anchor = "<a href='" . $anonref[$this->anonref] . $research . "' title='{$ref}' target='_blank'>{$reefer->qvar}</a>";
			$reefer->not_in_aux_se = true;
			return $reefer;
			}
		}
	} //if
if (strpos($ref, "google") !== false AND $this->refparts["path"]) { //Apr 19, 2013 - account for google referrers that can't be reverse engineered
	if (strpos($ref, "google") !== false) {
		$reefer->qvar = " ";
		$reefer->name = $ref;
		$reefer->host = $this->refparts["scheme"] . "://" . $this->refparts["host"];
		$reefer->anchor = "<a href='" . $anonref[$this->anonref] . $ref . "' title='{$ref}' target='_blank'>{$reefer->host}</a>";
//		$reefer->not_in_aux_se = true;
		return $reefer;
		}
	}
$ref = htmlentities($ref);
$reefer->name = $ref; //used in little graphs, and build_rfbk_record
$reefer->host = $this->refparts["scheme"] . "://" . $this->refparts["host"];
//$reefer->host = $this->refparts["scheme"] . "://" . $this->refparts["host"] . $this->refparts["path"];
$reefer->anchor = "<a href='" . $anonref[$this->anonref] . $ref . "' title='{$ref}' target='_blank'>{$reefer->host}</a>";
return $reefer;
} //referendum

/****************************************************/
function __construct($ecstatic) {
global $wpdb;
$this->anonref = $ecstatic->options["anonref"];
$table_name = $ecstatic->make_table("aux_se");
$this->sez = $wpdb->get_results("SELECT * FROM $table_name");
}
} //class ecstatic_seref

/****************************************************/
class rfbk_record {
var $pages = 0;
var $half = 0;
var $ip = "";
var $ua = "";
var $ref = "";
var $hit = array();
var $wnksobj = array();
} //class rfbk_record

/****************************************************/
class ecstatic_interface extends ecstatic {
public $shizitz = array();
public $rfb_count = array();
public $show_kill = 0;
public $when = "today";
private $loadlatest = 0;

/****************************************************/
function abbreviator($str, $len) {
if (strlen($str) > $len)
	return substr($str, 0, $len) . "...";
return $str;
} //abbreviator

/****************************************************/
function build_rfbk_record($tab, $ipua) {
global $wpdb;

static $ipz = array();
static $uaz = array();
static $ruriz = array();
static $seref;

$rec = new rfbk_record();
list($ipidx, $uaidx) = explode(".", $ipua);

if (!isset($seref))
	$seref = new ecstatic_seref($this); //search engine referrers
$rec->pages = count($this->shizitz[$tab][$ipua]);
$rec->half = (int)($rec->pages / 2);
if ($rec->pages % 2)
	$rec->half++;
if (isset($uaz[$uaidx]))
	$rec->ua = $uaz[$uaidx];
else {
$rec->ua = $wpdb->get_row($wpdb->prepare("SELECT ua, score FROM {$this->iurr_tables["ua"]} WHERE id = %d", $uaidx));
	$rec->ua->ualink = ecstatic_href_ua($rec->ua->ua);
	$uaz[$uaidx] = $rec->ua;
	}
if (isset($ipz[$ipidx]))
	$rec->ip = $ipz[$ipidx];
else {
$rec->ip = $wpdb->get_row($wpdb->prepare("SELECT ip, domain, score FROM {$this->iurr_tables["ip"]} WHERE id = %d", $ipidx));
	if (!$rec->ip->domain) {
		$dom = new ecstatic_get_host($ipidx, $rec->ip->ip, $this);
		$rec->ip->domain = $dom->domain_name();
		}
	else {
		$dom = new ecstatic_get_host();
		$rec->ip->domain = $dom->domain_name($rec->ip->domain);
		}
	$ipz[$ipidx] = $rec->ip;
	}
foreach ($this->shizitz[$tab][$ipua] as $record) {
	$rec->ref = $ref = $wpdb->get_row($wpdb->prepare("SELECT ref, score FROM {$this->iurr_tables["ref"]} WHERE id = %d", $record["ref"]));
	if ($ref->ref != "") {
		$se = $seref->referendum($ref->ref);
		$imash = $ipua . "." . $record["datetime"];
		$staref = "<a href='{$this->url}/wp-admin/admin.php?page=ecstatic_mash&amp;se={$record["ref"]}&amp;imash={$imash}' title='Add to or Edit Search Engine database table' target='_blank'><span style='color:green;'>&loz;</span></a>";
		if (isset($se->not_in_aux_se))
			$rec->ref->reflink = " <span style='color:blue;'>" . $se->name . "</span>: " . $se->anchor . " " . $staref;
		else
			$rec->ref->reflink = $se->anchor . " " . $staref;
		}
	if (isset($ruriz[$record["ruri"]]))
		$rec->hit["ruri"][] = $ruriz[$record["ruri"]];
	else {
		$rq = $wpdb->get_row($wpdb->prepare("SELECT ruri, score FROM {$this->iurr_tables["ruri"]} WHERE id = %d", $record["ruri"]));
		$rq->wnksobj = $this->is_in_lists2($rec->ip->ip, $rec->ua->ua, $rec->ref->ref, $rq->ruri, 0x2); //wnks 0010b - kill
		$ruriz[$record["ruri"]] = $rec->hit["ruri"][] = $rq;
		}
	$rec->hit["datetime"][] = $record["datetime"];
	$rec->hit["score"][] = $record["score"];
	$rec->hit["scorebits"][] = $record["scorebits"];
	} //foreach

$rec->wnksobj = $this->is_in_lists2($rec->ip->ip, $rec->ua->ua, $rec->ref->ref, "", 0x3); //wnks 0011b - kill + spiderbot
/*
echo "<pre>";
print_r($rec);
echo "</pre>";
*/
return $rec;
} //build_rfbk_record

/****************************************************/
function rfbk_panels($tab) {
$blurb = array("reg" => "Visitors", "feed" => "Feed Readers", "bot" => "Bots and Spidies");
$callers = array("ecstatic/ecstatic_interface.php", "ecstatic_kills");
$killed = array("", "<strong class='malign'>Killed</strong> ");
$leftright = array("left", "right");
$panel_title = "{$killed[$this->show_kill]}{$blurb[$tab]} for {$this->when}";

if (isset($_GET["page"]))
	$nope = $_GET["page"];

if ($this->loadlatest)
	$panel_title = "{$killed[$this->show_kill]}{$blurb[$tab]} since " . date("g:i:s a", $this->loadlatest) . ".&nbsp; Today's";

echo "<div><span class='panelTitle'>{$panel_title} Individuals: {$this->rfb_count[$tab]["ind"]}&nbsp;&nbsp; Pages: {$this->rfb_count[$tab]["pg"]}</span>\n";
if ($this->options["manual_purge"] AND $tab == "reg" AND $nope != "ecstatic_visitors") {
//	echo "<span class='manual_purge'><form id='manual_purge' method='post' action=''>";
	echo "<span class='manual_purge'><form id='manual_purge' method='post' action='../wp-admin/admin.php?page=ecstatic_manual_purge'>";
//	echo "<input type='hidden' name='ecstatit' value='manual_purge' />\n";
	echo "<input type='hidden' name='caller' value='{$callers[$this->show_kill]}' />\n";
	echo "<input class='purge_button' type='submit' name='manual_purge' value='Manual Purge' />";
	echo "</form></span>\n";
	}
if ($tab == "reg" AND $nope != "ecstatic_visitors") {
	echo "<span class='load_span'><form id='load_all' method='post' action=''>";
	echo "<input type='hidden' name='latest' value='0' />\n";
	echo "<input class='load_all_button' type='submit' name='load_all' value='Load All' />";
	echo "</form></span>\n";
	echo "<span class='load_span'><form id='load_latest' method='post' action=''>";
	if (isset($this->shizitz[$tab]))
		echo "<input type='hidden' name='latest' value='" . $this->shizitz[$tab][key($this->shizitz[$tab])][0]["datetime"] . "' />\n"; //whew -- error when shizitz is empty
	else
		echo "<input type='hidden' name='latest' value='{$this->datetime}' />\n";
	echo "<input class='load_latest_button' type='submit' name='load_latest' value='Load Latest' />";
	echo "</form></span>\n";
	echo "</div>\n";
	}
if (isset($this->shizitz[$tab])) {
	$popups = 0;
	foreach ($this->shizitz[$tab] as $ipua => $val) {
		$imash = $ipua . "." . $val[0]["datetime"];
		$garp = $this->build_rfbk_record($tab, $ipua);
		echo "<table class='ectable ecpop' summary='vizrec'>\n<thead><tr><th>\n<div style='width:100%;float:left;'>\n";
		echo "<div class='more'><a href='" . $this->url . "/wp-admin/admin.php?page=ecstatic_mash&amp;imash={$imash}' target='_blank'><img src='" . WP_PLUGIN_URL . "/" . dirname(plugin_basename(__FILE__)) . "/ecstatic_more.png' height='22' width='22' border='0' alt='more' /></a><br />more</div>\n"; //fixit
		echo "ID#: " . $ipua;
		if ($tab == "bot") {
			for ($zed=0; $zed<sizeof($garp->wnksobj); $zed++) {
				if ($garp->wnksobj[$zed]->wnks & 1) { //wnks 0001b - spiderbot
					echo "&nbsp;&nbsp; Name: " . $garp->wnksobj[$zed]->name;
					break;
					}
				}
			}
		echo "&nbsp;&nbsp; IP: " . $garp->ip->ip . "&nbsp;&nbsp; Domain: " . $garp->ip->domain . "&nbsp;&nbsp; Pages: " . $garp->pages . "<br />\n";
		echo "UA: " . $garp->ua->ualink . "<br />\n";
		if ($garp->ref->reflink != "")
			echo "Referrer: {$garp->ref->reflink}<br />\n";
		if ($this->show_kill) {
			for ($zed=0; $zed<sizeof($garp->wnksobj); $zed++) {
				if ($garp->wnksobj[$zed]->wnks & 0x2) { //wnks 0010b - kill
					echo "Found with Token: <strong style='color:black;'>{$garp->wnksobj[$zed]->token}</strong>&nbsp; &nbsp; Name: <strong style='color:black;'>{$garp->wnksobj[$zed]->name}</strong>\n";
					break;
					}
				}
			}
		echo "</div>\n</th></tr></thead>\n<tbody><tr><td>\n";
		$split = array(array(0, $garp->half), array($garp->half, $garp->pages));
		for ($y=0; $y<2; $y++) {
			if ($y < 1 OR $garp->pages > 1) {
				echo "<table width='48%' align='{$leftright[$y]}' summary='halflist'>\n";
				echo "<thead><tr><th>Date/Time</th><th>Requested URI</th><th>Score</th></tr></thead>\n";
				for ($x=$split[$y][0]; $x<$split[$y][1]; $x++) {
					$score_style = "tdPop score_reg";
					if ($garp->hit["score"][$x] > 4)
						$score_style .= " score_hot";
					$popUpstring = buildpopUpstring($this, $garp->hit["scorebits"][$x], $garp->ip->score, $garp->ua->score, $garp->ref->score, $garp->hit["ruri"][$x]->score, $garp->hit["ruri"][$x]->wnksobj);
					if ($this->options["stop_popups"] AND (++$popups > $this->options["stop_popups_beyond"] OR $garp->hit["score"][$x] < $this->options["no_lowscore_popups"]))
						echo "<tr><td width='24%'>" . date("m/d", $garp->hit["datetime"][$x]) . "&nbsp; " . date("H:i:s", $garp->hit["datetime"][$x]) . "</td><td width='66%' style='color: #008'>" . ecstatic_makelink($garp->hit["ruri"][$x]->ruri) . "</td><td class='NO{$score_style}'>" . $garp->hit["score"][$x] . "</td></tr>\n";
					else
						echo "<tr><td width='24%'>" . date("m/d", $garp->hit["datetime"][$x]) . "&nbsp; " . date("H:i:s", $garp->hit["datetime"][$x]) . "</td><td width='66%' style='color: #008'>" . ecstatic_makelink($garp->hit["ruri"][$x]->ruri) . "</td><td class='{$score_style}'>" . $garp->hit["score"][$x] . "<span class='pop'>{$popUpstring}</span></td></tr>\n";
					}
				echo "</table>\n";
				}
			}
		echo "</td></tr></tbody></table>\n";
		} //foreach
	} //if isset
} //rfbk_panels

/****************************************************/
function main_suck_from_db() {
global $wpdb, $ba;

$NSB = NO_SHOW_BIT; //don't show hits that you don't want to show
if (isset($_GET["showday"])) {
	$earliest_time = $_GET["showday"];
	if ($earliest_time < 0 OR $earliest_time > $this->datetime)
		$earliest_time = $this->zero_am_today; //midnight zero
	$this->when = date('F d, Y', $earliest_time) . "...";
	$range = $earliest_time + (60*60*24);
	$today_start_time = $display_start_time = $earliest_time;
	$qstring = array("SELECT * FROM $this->hits_table WHERE datetime > %d AND datetime < %d AND NOT (scorebits & $NSB) AND score < 10 ORDER BY datetime DESC", "SELECT * FROM $this->hits_table WHERE datetime > %d AND datetime < %d AND NOT (scorebits & $NSB) AND score > 9 ORDER BY datetime DESC");
	$hits = $wpdb->get_results($wpdb->prepare($qstring[$this->show_kill], $earliest_time, $range), ARRAY_A);
	}
else {
	$this->when = "latest " . $this->options["daystoshow"] * 24 . " hours.&nbsp; Today's";
	$display_start_time = $this->datetime - (60 * 60 * 24 * $this->options["daystoshow"]); //*
	$today_start_time = $this->zero_am_today; //midnight zero
	$earliest_time = min($display_start_time, $today_start_time);
	$qstring = array("SELECT * FROM $this->hits_table WHERE datetime > %d AND score < 10 AND NOT (scorebits & $NSB) ORDER BY datetime DESC", "SELECT * FROM $this->hits_table WHERE datetime > %d AND NOT (scorebits & $NSB) AND score > 9 ORDER BY datetime DESC");
	$hits = $wpdb->get_results($wpdb->prepare($qstring[$this->show_kill], $earliest_time), ARRAY_A);
	}

if ($this->loadlatest)
	$display_start_time = $this->loadlatest;

$temp4count = array();
foreach ($hits as $hit) {
	$ipua = $hit["ip"] . "." . $hit["ua"];
	if (isset($ba->uaz[$hit["ua"]]) OR isset($ba->ipz[$hit["ip"]])) { //no need for a refz
		if ($hit["datetime"] > $display_start_time)
			$this->shizitz["bot"][$ipua][] = $hit;
		$temp4count["bot"][$ipua][] = $hit;
		}
	elseif (isset($ba->ruriz[$hit["ruri"]])) {
		if ($hit["datetime"] > $display_start_time)
			$this->shizitz["feed"][$ipua][] = $hit;
		$temp4count["feed"][$ipua][] = $hit;
		}
	else {
		if ($hit["datetime"] > $display_start_time)
			$this->shizitz["reg"][$ipua][] = $hit;
		$temp4count["reg"][$ipua][] = $hit;
		}
	}
if (!$this->show_kill) {
	$today = date('Y-m-d', $today_start_time);
	$daytots = $wpdb->get_row($wpdb->prepare("SELECT * FROM $this->cumulative_table WHERE day=%s", $today));
	$this->rfb_count["reg"]["ind"] = $daytots->regi;
	$this->rfb_count["reg"]["pg"] = $daytots->regp;
	$this->rfb_count["feed"]["ind"] = $daytots->feedi;
	$this->rfb_count["feed"]["pg"] = $daytots->feedp;
	$this->rfb_count["bot"]["ind"] = $daytots->boti;
	$this->rfb_count["bot"]["pg"] = $daytots->botp;
	}
else {
	$newviselapse = $this->options["newvisitorminutes"] * 60;
	foreach ($temp4count as $rfb => $y) {
		foreach ($y as $z) {
			$last_time = 0;
			foreach ($z as $visitor) {
				if ($visitor["datetime"] > $today_start_time) { //fills array for "today" stats
					$this->rfb_count[$rfb]["pg"]++;
					if (($rfb == "reg" AND ($last_time - $visitor["datetime"] > $newviselapse)) OR $last_time == 0)
						$this->rfb_count[$rfb]["ind"]++;
					}
				$last_time = $visitor["datetime"];
				}
			}
		}
	}
} //main_suck_from_db

/************************************************************************************/
function show_panels($tab="") {
$panelz = array($this->options["panel1"], $this->options["panel2"], $this->options["panel3"]);
$showz = array("reg" => $this->options["showreg"], "feed" => $this->options["showfeed"], "bot" => $this->options["showbot"]);
if ($tab)
	$this->rfbk_panels($tab);
else {
	foreach ($panelz as $panel)
		if ($showz[$panel])
			$this->rfbk_panels($panel);
	}
if (!$this->options["manual_purge"])
	ecstatic_purge($this);
} //show_panels

/****************************************************/
function __construct($title) {
global $ba;
parent::__construct();
ecstatic_ecstatic($this, $title); //logo banner
$ba = new ecstatic_before_after($this);
ecstatic_banner_graph($this);
$this->loadlatest = ecstatic_loadlatest(); //check Load Latest state
$this->main_suck_from_db();
}
} //class ecstatic_interface

/****************************************************/
class ecstatic_show_kill extends ecstatic_interface{
/****************************************************/
function __construct($title) {
$this->show_kill = 1;
parent::__construct($title);
}
} //ecstatic_show_kill

/****************************************************/
function ecstatic_purge($ecstatic) {
global $wpdb, $ba;
$purges = array("preg" => $ecstatic->options["purgeolderthan"], "pbot" => $ecstatic->options["purgebotolderthan"], "prss" => $ecstatic->options["purgerssolderthan"]);
arsort($purges);
$y = 0;
$tally = array();
$big_cutoff = $ecstatic->datetime - (min($purges) * 60 * 60 * 24);
$p = $wpdb->get_results("SELECT datetime, ip, ua, ruri FROM {$ecstatic->hits_table} WHERE datetime < '{$big_cutoff}'");
$y = count($p);
for ($x=0; $x<3; $x++) {
list($key, $val) = each($purges);
if ($val) {
	$i = 0;
	$plists = array();
	$cutoff = $ecstatic->datetime - ($val * 60 * 60 * 24);
	switch($key) {
		case "pbot":
			if ($p) {
				for ($j=0;$j<$y;$j++) { //consolidate to save db hits
					if ($p[$j]->datetime < $cutoff) {
						if (isset($ba->uaz[$p[$j]->ua])) //saved from preload presuck - marks a bot
							$plists["ua"][$p[$j]->ua]++;
						elseif (isset($ba->ipz[$p[$j]->ip])) //likewise
							$plists["ip"][$p[$j]->ip]++;
						}
					}
				foreach ($plists as $field => $plist) {
					foreach ($plist as $id => $superfluous) {
						mysql_query("DELETE FROM {$ecstatic->hits_table} WHERE datetime < '{$cutoff}' AND {$field} = '{$id}'");
						$i += mysql_affected_rows();
						}
					}
				}
			break;
		case "preg":
			if ($p) {
				for ($j=0;$j<$y;$j++) { //consolidate
					if ($p[$j]->datetime < $cutoff) {
						if (!isset($ba->uaz[$p[$j]->ua]) AND !isset($ba->ipz[$p[$j]->ip]) AND !isset($ba->ruriz[$p[$j]->ruri]))
							$plists[$p[$j]->ip][$p[$j]->ua]++;
						}
					}
				foreach ($plists as $ipdex => $uad) {
					foreach ($uad as $uadex => $superfluous) {
						mysql_query("DELETE FROM {$ecstatic->hits_table} WHERE datetime < '{$cutoff}' AND ip = '{$ipdex}' AND ua = '{$uadex}'");
						$i += mysql_affected_rows();
						}
					}
				}
			break;
		case "prss":
			if ($p) {
				for ($j=0;$j<$y;$j++) { //consolidate
					if ($p[$j]->datetime < $cutoff) {
						if (!isset($ba->uaz[$p[$j]->ua]) AND !isset($ba->ipz[$p[$j]->ip]) AND isset($ba->ruriz[$p[$j]->ruri]))
							$plists[$p[$j]->ruri][$p[$j]->ip][$p[$j]->ua]++;
						}
					}
				foreach ($plists as $ruridex => $ipd) {
					foreach($ipd as $ipdex => $uad) {
						foreach ($uad as $uadex => $superfluous) {
							mysql_query("DELETE FROM {$ecstatic->hits_table} WHERE datetime < '{$cutoff}' AND ip = '{$ipdex}' AND ua = '{$uadex}' AND ruri = '{$ruridex}'");
							$i += mysql_affected_rows();
							}
						}
					}
				}
			break;
		} //switch
	unset($plists);
	$tally[$key] = $i;
	} //if
	} //for 3

$h = $ecstatic->hits_table;
foreach ($ecstatic->iurr_tables as $key => $t)
	$num[$key] = $wpdb->query("DELETE {$t}.* FROM {$t} LEFT JOIN {$h} ON {$t}.id = {$h}.{$key} WHERE {$h}.{$key} IS NULL");
$q = $wpdb->get_results("SHOW TABLE STATUS LIKE '%ecstatic%'", OBJECT_K);
$rows[$h] = $q[$h]->Rows;
if ($rows[$h]) { //prevent divide by zero errs with brand new, empty tables
	$frag[$h] = $q[$h]->Data_free / $q[$h]->Data_length;
	if ($frag[$h] > 0.05) {
		$wpdb->query("OPTIMIZE TABLE {$h}");
		$frag[$h] = 0;
		}
	foreach ($ecstatic->iurr_tables as $key => $t) {
		$rows[$key] = $q[$t]->Rows;
		$frag[$key] = $q[$t]->Data_free / $q[$t]->Data_length;
		if ($frag[$key] > 0.05) {
			$wpdb->query("OPTIMIZE TABLE {$t}");
			$frag[$key] = 0;
			}
		}
	echo "<table id='purgetable' summary='purges'>\n";
	echo "<tr><th class='purgetitle' rowspan='2'>ecSTATic<br />PURGE</th><th colspan='3' class='bigger'>Hits Table</th><th colspan='4' class='bigger'>Orphans</th></tr>\n";
	echo "<tr><td>HTML Hits</td><td>Spider/Bots</td><td>Feed Reads</td><td>IP table</td><td>UA table</td><td>Ref table</td><td>RURI table</td></tr>\n";
	echo "<tr><th class='left_th'>Purged</th><td>{$tally["preg"]}</td><td>{$tally["pbot"]}</td><td>{$tally["prss"]}</td><td>{$num["ip"]}</td><td>{$num["ua"]}</td><td>{$num["ref"]}</td><td>{$num["ruri"]}</td></tr>\n";
	echo "<tr><th class='left_th'>Older than</th><td>{$purges["preg"]} days</td><td>{$purges["pbot"]} days</td><td>{$purges["prss"]} days</td><td colspan='4'></td></tr>\n";
	echo "<tr><th class='left_th'>Rows/Frag%</th><td colspan='3'>{$rows[$h]}/" . sprintf("%2.2f%%", $frag[$h]*100) . "</td><td>{$rows["ip"]}/" . sprintf("%2.2f%%", $frag["ip"]*100) . "</td><td>{$rows["ua"]}/" . sprintf("%2.2f%%", $frag["ua"]*100) . "</td><td>{$rows["ref"]}/" . sprintf("%2.2f%%", $frag["ref"]*100) . "</td><td>{$rows["ruri"]}/" . sprintf("%2.2f%%", $frag["ruri"]*100) . "</td></tr>\n";
	echo "</table>\n";
	echo "<p class='frag'>Tables individually auto-Optimize when Fragmentation reaches 5.0%.</p><br /><br /><br /><br />\n";
	}
} //ecstatic_purge

/****************************************************/
class ecstatic_before_after {
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
	$seref = new ecstatic_seref($ecstatic);
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
} //ecstatic_before_after

/****************************************************/
function buildpopUpstring ($ecstatic, $scorebits, $ip_score, $ua_score, $ref_score, $ruri_score, $wnksobj) {
$str = "";
$scoreblurb = array("Bad IP", "No User Agent", "No Referrer", "WayTooFast", "{$ecstatic->options['login_limit']}+ Login Fails", "Lost Password", "Trackback", "No Domain", "New Bot", "Cookie Hash Err", "Cookie User Err", "Failed Login", "NoShow", "MozPrefetch");
$scorescore = array($ecstatic->options['mal_ip'], $ecstatic->options['empty_ua'], $ecstatic->options['empty_ref'], 10, 10, $ecstatic->options['lostpassword'], $ecstatic->options['trackback'], $ecstatic->options['dom_check_score'], 10, "&nbsp;", "&nbsp;", "&nbsp;", "&nbsp;", 10);
if ($ip_score)
	$str .= "|IP:{$ip_score}";
if ($ua_score)
	$str .= "|User Agent:{$ua_score}";
if ($ref_score)
	$str .= "|Referrer:{$ref_score}";
if ($ruri_score)
	$str .= "|Req. URI:{$ruri_score}";
for ($i=1, $j=0;$j<count($scoreblurb);$i*=2, $j++)
	if ($scorebits & $i)
		$str .= "|{$scoreblurb[$j]}:{$scorescore[$j]}";
$x = 0;
for ($zed=0; $zed<sizeof($wnksobj); $zed++) {
	if ($wnksobj[$zed]->wnks & 0x2) { //wnks 0010b - killer
		switch($x) {
			case 0:
				$str .= "|Killed:10";
				$x++;
			default:
				$str .= "|{$wnksobj[$zed]->name}:{$wnksobj[$zed]->type}";
			}
		}
	} //for
if (!$str)
	$str = "x";
return $str;
} //buildpopUpstring

/****************************************************/
/*
function buildWNKSstring ($ecstatic, $wnksobj) {
$str = "";
$bits = array(0x8, 0x4, 0x2, 0x1, 0x80); //wlist nolog kill spider xwlist
$chrs = array("W", "N", "K", "S", "X");
for ($a=0; $a<5; $a++) {
	for ($zed=0; $zed<sizeof($wnksobj); $zed++) {
		if ($wnksobj[$zed]->wnks & $bits[$a]) {
			$str .= $chrs[$a];
			break;
			}
		} //for
	} //for
return $str;
} //buildWNKSstring
*/

/****************************************************/
class ecstatic_sequentialview extends ecstatic {
private $loadlatest = 0;

/****************************************************/
function sequentialview() {
global $wpdb, $ba;

$ucache = $dcache = $qcache = array();
$display_start_time = $this->datetime - (60 * 60 * 24 * $this->options["daystoshow"]);
$today_start_time = $this->zero_am_today; //midnight zero
$panel_title = "All hits for latest " . $this->options["daystoshow"] * 24 . " hours.";

if ($this->loadlatest) {
	$display_start_time = $this->loadlatest;
	$panel_title = "Hits since " . date("g:i:s a", $display_start_time) . ".";
	}

$seref = new ecstatic_seref($this);

$h = $this->hits_table;
$i = $this->iurr_tables["ip"];
$u = $this->iurr_tables["ua"];
$r = $this->iurr_tables["ref"];
$q = $this->iurr_tables["ruri"];

$toShow = 0;
$Show = array("NOT ($h.scorebits & " . NO_SHOW_BIT . ")", "($h.scorebits & " . NO_SHOW_BIT . ")");
$ShowB = array("ShowUn","UnShow");
if (isset($_POST["shownoshow"]) AND ($_POST["shownoshow"] == 0 OR $_POST["shownoshow"] == 1))
	$toShow = $_POST["shownoshow"];

if ($this->options["skip_rip"])
	$rQ = "SELECT $h.datetime, $h.ip AS ipx, $h.ua AS uax, $h.ref AS refx, $h.ruri AS rurix, $h.scorebits, $h.score, $i.ip, $i.score AS ipscore, $u.ua, $u.browser, $u.os, $u.score AS uascore, $r.ref, $r.score AS refscore, $q.ruri, $q.score AS ruriscore FROM $h, $i, $u, $r, $q WHERE $h.ip=$i.id AND $h.ua=$u.id AND $h.ref=$r.id AND $h.ruri=$q.id AND {$Show[$toShow]} AND $h.datetime > $display_start_time ORDER BY $h.datetime DESC";
else {
	$xdom = new ecstatic_get_host();
	$rQ = "SELECT $h.datetime, $h.ip AS ipx, $h.ua AS uax, $h.ref AS refx, $h.ruri AS rurix, $h.scorebits, $h.score, $i.ip, $i.domain, $i.score AS ipscore, $u.ua, $u.browser, $u.os, $u.score AS uascore, $r.ref, $r.score AS refscore, $q.ruri, $q.score AS ruriscore FROM $h, $i, $u, $r, $q WHERE $h.ip=$i.id AND $h.ua=$u.id AND $h.ref=$r.id AND $h.ruri=$q.id AND {$Show[$toShow]} AND $h.datetime > $display_start_time ORDER BY $h.datetime DESC";
	}
$hits_today = $wpdb->get_var($wpdb->prepare("SELECT count(*) FROM $this->hits_table WHERE score < 10 AND datetime >= %d", $today_start_time));
$killed_today = $wpdb->get_var($wpdb->prepare("SELECT count(*) FROM $this->hits_table WHERE score > 9 AND datetime >= %d", $today_start_time));
$hits = $wpdb->get_results($rQ);
echo "<div><span class='panelTitle'>{$panel_title}&nbsp; Hits today: {$hits_today} <span style='font-size:9pt;color:dimgray;font-weight:normal;'>+ {$killed_today} blocked</span>&nbsp; &nbsp; &nbsp; &nbsp; <span class='tinyh2'><span class='sviz'>Visitor</span>&nbsp; <span class='srss'>Feed Read</span>&nbsp; <span class='sbot'>Spider/Bot</span>&nbsp; <span class='skill'>KILLed</span>&nbsp; <span class='unrecog'>NewBot</span></span></span>\n";
if ($this->options["manual_purge"]) {
//	echo "<span class='manual_purge'><form id='manual_purge' method='post' action=''>";
	echo "<span class='manual_purge'><form id='manual_purge' method='post' action='../wp-admin/admin.php?page=ecstatic_manual_purge'>";
//	echo "<input type='hidden' name='ecstatit' value='manual_purge' />\n";
	echo "<input type='hidden' name='caller' value='ecstatic_sequential' />\n";
	echo "<input class='purge_button' type='submit' name='manual_purge' value='Manual Purge' />";
	echo "</form></span>\n";
	}
echo "<span class='load_span'><form id='shownoshow' method='post' action=''>"; //Load Latest
echo "<input type='hidden' name='shownoshow' value='" . ($toShow^1) . "' />\n";
echo "<input class='{$ShowB[$toShow]}' type='submit' name='showshow' value='". $ShowB[$toShow] . "' />";
echo "</form></span>\n";
echo "<span class='load_span'><form id='load_all' method='post' action=''>"; //Load ALL
echo "<input type='hidden' name='latest' value='0' />\n";
echo "<input class='load_all_button' type='submit' name='load_all' value='Load All' />";
echo "</form></span>\n";
echo "<span class='load_span'><form id='load_latest' method='post' action=''>"; //Load Latest
echo "<input type='hidden' name='latest' value='{$hits[0]->datetime}' />\n";
echo "<input class='load_latest_button' type='submit' name='load_latest' value='Load Latest' />";
echo "</form></span>\n";
echo "</div>\n";

echo "<table id='sequence' class='ectable sortable ecpop' summary='sequentialism' cellspacing='0' cellpadding='0'>\n";

if ($this->options["skip_rip"]) {
	echo "<colgroup><col /><col /><col /><col /><col /><col width='20%' /><col width='20%' /></colgroup>\n";
	echo "<thead><tr><th>Date/Time</th><th class='natsort'>IP</th><th>Q</th><th>Browser</th><th>OS</th><th>Page</th><th>Referrer</th><th>Score</th></tr></thead>\n<tbody>\n";
	}
else {
	echo "<colgroup><col /><col /><col align='right' /><col /><col /><col /><col width='20%' /><col width='20%' /><col /></colgroup>\n";
	echo "<thead><tr><th>Date/Time</th><th class='natsort'>IP</th><th>Q</th><th>Domain</th><th>Browser</th><th>OS</th><th>Page</th><th>Referrer</th><th>Score</th></tr></thead>\n<tbody>\n";
	}

$popups = 0;
foreach($hits as $hit) {
	$td_class = $browser = $renderer = $os = $anchor = $ruri_link = "";
	$its = $hit->datetime;
	$mits = date("m/d H:i:s", $its);
	$imash = $hit->ipx . "." . $hit->uax . "." . $its; //make the mash
	echo "<tr><td><!--{$its}-->" . str_replace(" ", "&nbsp;", $mits) . "</td>\n";

	if (!$this->options["skip_rip"]) {
		if (!$hit->domain) {
			if ($dcache[$hit->ipx]) //saves a mySQL call (in ecstatic_get_host()) here and there
				$hit->domain = $dcache[$hit->ipx];
			else {
				$dom = new ecstatic_get_host($hit->ipx, $hit->ip, $this);
				$dcache[$hit->ipx] = $hit->domain = $dom->domain_name();
				unset($dom);
				}
			}
		else
			$hit->domain = $xdom->domain_name($hit->domain);
		}

	$ruri_link = ecstatic_makelink($hit->ruri);
	if ($ba->ruriz[$hit->rurix])
		$td_class = "srss";

//	if ($hit->ua == "")
//		$browser = $os = "(empty)";
//	elseif ($hit->browser AND $hit->browser != "Unknown") { //on the second test, new ua parser code attempts to eliminate Unknowns - this may be useless now
//	if ($hit->browser AND $hit->browser != "Unknown") { //on the second test, new ua parser code attempts to eliminate Unknowns - this may be useless now
	if ($hit->browser) {
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
			$wpdb->query($wpdb->prepare("UPDATE {$this->iurr_tables["ua"]} SET browser=%s, os=%s, renderer=%s WHERE id=%d", $browser, $os, $renderer, $hit->uax));
			$ucache[$hit->uax] = true;
			}
		}
	if ($hit->ref != "") {
		$se = $seref->referendum($hit->ref);
		$staref = "<a href='{$this->url}/wp-admin/admin.php?page=ecstatic_mash&amp;se={$hit->refx}&amp;imash={$imash}' title='Add to or Edit Search Engine database table' target='_blank'><span style='color:green;'>&loz;</span></a>";
		if (isset($se->not_in_aux_se))
			$anchor = " <span style='color:blue;'>" . $se->name . "</span>: " . $se->anchor . " " . $staref;
		else
			$anchor = $se->anchor . " " . $staref;
		}

	if ($hit->score > 9)
		$td_class .= " skill";
	if ($ba->uaz[$hit->uax] OR $ba->ipz[$hit->ipx])
		$td_class .= " sbot";
	elseif (preg_match("/bot|spider|crawl/i", $hit->ua))
		$td_class .= " unrecog";
	if ($td_class == "")
		$td_class = "sviz";

	if (!$this->options["skip_rip"]) //skip reverse ip lookup
		echo "<td>{$hit->ip}</td>";
	else
		echo "<td class='{$td_class}'>{$hit->ip}</td>"; //td_class goes here when skip_rip is enabled

	if ($qcache[$hit->ipx])
		$freq = $qcache[$hit->ipx];
	else
		$qcache[$hit->ipx] = $freq = $wpdb->get_var($wpdb->prepare("SELECT count(*) FROM {$this->hits_table} WHERE ip=%d", $hit->ipx));
	echo "<td>{$freq}</td>";
	if (!$this->options["skip_rip"])
		echo "<td class='{$td_class}'>{$hit->domain}</td>"; //td_class goes here

	if (strpos($browser, "Unknown") !== false)
		echo "<td>{$hit->ua}</td>";
	else
		echo "<td>{$browser}</td>";
	echo "<td>{$os}</td>";
	if (strpos($hit->ruri, "login") !== false OR $hit->scorebits & 0xe10) //??????????? - 0000 1110 0001 0000b
		echo "<td class='xlogin'>{$ruri_link}</td>";
	else
		echo "<td>{$ruri_link}</td>";
	echo "<td>{$anchor}</td>";

	$extra_class = "";
	if ($hit->score == -1) {
		$hit->score = "*";
		$extra_class = " wl";
		}

	$wnksobj = $this->is_in_lists2($hit->ip, $hit->ua, $hit->ref, $hit->ruri, 0x2); //wnks 0010b - kill
//	$wnksobj = $this->is_in_lists2($hit->ip, $hit->ua, $hit->ref, $hit->ruri, 0x8f); //wnks 10001111b - xwlist - - - wlist nolog kill spider
//	$WNKSstring = buildWNKSstring($this, $wnksobj);
//	echo "<td>{$WNKSstring}</td>";

	$popUpstring = buildpopUpstring($this, $hit->scorebits, $hit->ipscore, $hit->uascore, $hit->refscore, $hit->ruriscore, $wnksobj);
	if ($this->options["stop_popups"] AND (++$popups > $this->options["stop_popups_beyond"] OR $hit->score < $this->options["no_lowscore_popups"]))
		echo "<td class='NOtdPop{$extra_class}'><a href='" . $this->url . "/wp-admin/admin.php?page=ecstatic_mash&amp;imash={$imash}' title='{$popUpstring}' target='_blank'>{$hit->score}</a></td>";
	else
		echo "<td class='tdPop{$extra_class}'><a href='" . $this->url . "/wp-admin/admin.php?page=ecstatic_mash&amp;imash={$imash}' target='_blank'>{$hit->score}</a><span class='pop'>{$popUpstring}</span></td>";
	echo "</tr>\n";
	} //foreach

echo "</tbody>\n</table>\n";

echo <<<XXX
<script type="text/javascript">
var sorter = new TINY.table.sorter("sorter");
sorter.reverse = true;
sorter.init("sequence",0);
</script>

XXX;

if (!$this->options["manual_purge"])
	ecstatic_purge($this);
} //sequentialview

/****************************************************/
function __construct() {
global $ba;
parent::__construct();
ecstatic_ecstatic($this, "Sequential View"); //logo banner
ecstatic_banner_graph($this);
$ba = new ecstatic_before_after($this);
$this->loadlatest = ecstatic_loadlatest(); //check Load Latest state
}
} //class ecstatic_sequentialview

/****************************************************/
class ecstatic_manual_purger extends ecstatic {
/****************************************************/
function __construct() {
global $ba;
parent::__construct();
$ba = new ecstatic_before_after($this);
}
} //class ecstatic_manual_purger

/****************************************************/
function ecstatic_main() { //"Main" menu entry
$ecstatic = new ecstatic_interface("Main Panels");
$ecstatic->show_panels();
} //ecstatic_main

/****************************************************/
function ecstatic_visitors() {
$ecstatic = new ecstatic_interface("Browser Visitors");
$ecstatic->show_panels("reg");
} //ecstatic_visitors

/****************************************************/
function ecstatic_feeds() {
$ecstatic = new ecstatic_interface("Feed Readers");
$ecstatic->show_panels("feed");
} //ecstatic_feeds

/****************************************************/
function ecstatic_spiderbots() {
$ecstatic = new ecstatic_interface("Spiders/Bots");
$ecstatic->show_panels("bot");
} //ecstatic_spiderbots

/****************************************************/
function ecstatic_kills() {
$ecstatic = new ecstatic_show_kill("Maleagents");
$ecstatic->show_panels();
} //ecstatic_kills

/****************************************************/
function ecstatic_mash() { //non-menued program forkéz - edit search engine referrers vs details vs import/export aux_lists
if (isset($_GET["se"]) AND isset($_GET["imash"])) {
	include_once(WP_PLUGIN_DIR . "/" . dirname(plugin_basename(__FILE__)) . '/ecstatic_seref.php');
	$ecstatic = new ecstatic_se_ref();
	$ecstatic->edit_se_ref();
	}
/*
elseif (isset($_GET["imex"]) AND isset($_GET["imash"])) { //import/export - never implemented???
	include("ecstatic_forms.php");
	$ecstatic = new ecstatic_imex();
	$ecstatic->imex();
	}
*/
elseif (isset($_GET["ignored_ids"]) AND isset($_GET["imash"])) {
//	include_once(WP_PLUGIN_DIR . "/" . dirname(plugin_basename(__FILE__)) . '/ecstatic_forms.php');
	include("ecstatic_forms.php"); //short version works?
	$ecstatic = new ecstatic_ignored_ids();
	$ecstatic->ignored_ids();
	}
elseif (isset($_GET["imash"])) {
	include_once(WP_PLUGIN_DIR . "/" . dirname(plugin_basename(__FILE__)) . '/ecstatic_details.php');
	$ecstatic = new ecstatic_details();
	$ecstatic->details();
	}
} //ecstatic_mash

/****************************************************/
function ecstatic_sequential() {
$ecstatic = new ecstatic_sequentialview();
$ecstatic->sequentialview();
} //ecstatic_sequential

/****************************************************/
function ecstatic_manual_purge() {
$callers = array("ecstatic/ecstatic_interface.php" => "Main Panels", "ecstatic_kills" => "Kill Panels", "ecstatic_sequential" => "Sequential View");
$ecstatic = new ecstatic_manual_purger();
ecstatic_purge($ecstatic);
if (isset($_POST["caller"])) {
	$caller = $_POST["caller"];
	if (!isset($callers[$caller])) //sanitize sanitize sanitize
		$caller = "ecstatic_sequential";
//	echo "<form id='redirect' method='post' action=''>";
	echo "<form id='redirect' method='post' action='../wp-admin/admin.php?page={$caller}'>";
//	echo "<input type='hidden' name='ecstatit' value='continue' />\n";
//	echo "<input type='hidden' name='continue' value='{$caller}' />\n";
	echo "<input class='continue_button' type='submit' name='Continue' value='Back to {$callers[$caller]}' />";
	echo "</form>\n";
	}
} //ecstatic_manual_purge

/****************************************************/
function ecstatic_play() {
include_once(WP_PLUGIN_DIR . "/" . dirname(plugin_basename(__FILE__)) . '/ecstatic_stats.php');
$ecstatic = new ecstatic_stats();
$ecstatic->play_stats();
} //ecstatic_play

/****************************************************/
function ecstatic_charts() {
include_once(WP_PLUGIN_DIR . "/" . dirname(plugin_basename(__FILE__)) . '/ecstatic_charts.php');
$chartPage = new ecstatic_charter();
$chartPage->make_chart();
} //ecstatic_charts

/****************************************************/
function ecstatic_options() {
include("ecstatic_forms.php");
$ecstatic = new ecstatic_options();
$ecstatic->option_form();
} //ecstatic_options

/****************************************************/
function ecstatic_help() {
include("ecstatic_help.php");
} //ecstatic_help
?>