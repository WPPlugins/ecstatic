<?php
/****************************************************/
class ecstatic_stats extends ecstatic {
/****************************************************/
function hexer($high_color, $rdelta, $gdelta, $bdelta) {
$rd = hexdec(substr($high_color, 0, 2));
$gd = hexdec(substr($high_color, 2, 2));
$bd = hexdec(substr($high_color, 4, 2));
$rd -= $rdelta;
$gd -= $gdelta;
$bd -= $bdelta;
$red = 0x10000 * max(0, min(255, $rd));
$green = 0x100 * max(0, min(255, $gd));
$blue = max(0, min(255, $bd));
return str_pad(strtoupper(dechex($red + $green + $blue)), 6, "0", STR_PAD_LEFT); //convert the combined value to hex and zero-fill to 6 digits
} //hexer

/****************************************************/
function little_graph($title, $graphee, $count) {

$max = sizeof($graphee);
if (!$count OR $max < 1)
	return;
if ($this->options["topentriestoshow"] AND $max > $this->options["topentriestoshow"]) {
	$mix = "Top {$this->options["topentriestoshow"]} of $max";
	$max = $this->options["topentriestoshow"];
	$temp = $graphee;
	arsort($temp);
	reset($temp);
	unset($graphee);
	$x = 0;
	foreach ($temp as $key => $val) {
		$graphee[$key] = $val;
		if (++$x > $max-1)
			break;
		}
	}
if (!$this->options["graphsort"])
	ksort($graphee);
else
	arsort($graphee);
if ($max > 1)
	$max--;
$high_color = "66dd77"; //hex strings
$low_color = "7788cc";
$rdif = hexdec(substr($high_color, 0, 2)) - hexdec(substr($low_color, 0, 2));
$gdif = hexdec(substr($high_color, 2, 2)) - hexdec(substr($low_color, 2, 2));
$bdif = hexdec(substr($high_color, 4, 2)) - hexdec(substr($low_color, 4, 2));
$rdelta = round($rdif / $max);
$gdelta = round($gdif / $max);
$bdelta = round($bdif / $max);

$graph_width = 224;
$graph_jiggle = max($graphee) / $count * 100;
$x = 0;
echo "<div class='lilgraph'><h2>{$title}</h2>\n";
echo "<table cellpadding='0' cellspacing='0' summary='graph'>";
echo "<thead><tr><th>{$mix}</th><th style='text-align: left;'>{$count} Pages</th></tr></thead>\n";
foreach($graphee as $bowse => $num) {
	$percent = round(($num * 100 / $count), 1);
	if ($title == "Referrers")
		echo "<tr><td style='width: 224px; overflow: hidden; white-space: nowrap; text-overflow: ellipsis; padding: 0 1.5em;'><a href='{$bowse}' title='{$bowse}' target='_blank'>" . substr($bowse, 0, 30) . "</a></td>";
	else
		echo "<tr><td style='width: 224px; overflow: hidden; white-space: nowrap; text-overflow: ellipsis; padding: 0 1.5em;'>" . substr($bowse, 0, 30) . "</td>";
	echo "<td><div style='width: {$graph_width}px;'>";
	echo "<div style='text-align: left; padding: 4px 0 2px 6px; font-size: 7pt; font-weight: bold; height: 16px; width:" . number_format($graph_width * $percent / $graph_jiggle) . "px;";
	echo " background: #" . $high_color . ";";
	echo " border-top: 1px solid #" . $this->hexer($high_color, -20, -20, -20) . ";";
	echo " border-right: 1px solid #" . $this->hexer($high_color, -40, -40, -40) . ";";
	echo " border-bottom: 1px solid #" . $this->hexer($high_color, 20, 20, 20) . ";";
	echo "'>{$num}&nbsp;({$percent}%)</div>\n</div></td></tr>\n";
	$high_color = $this->hexer($high_color, $rdelta, $gdelta, $bdelta);
	if (++$x > $max)
		break;
	}
echo "</table>\n</div>\n";
} //little_graph

/****************************************************/
function play_stats() {
global $wpdb;

$count_os = $count_browse = $count_ref = $count_se_ref = $count_se_phrase = $count_spidies = $count_domain = 0; //Spiders/Bots
$ipx = $uax = $refx = array();
$cumhits = $wpdb->get_results("SELECT ip, ua, ref FROM $this->hits_table WHERE score < 10");
foreach($cumhits as $chits) {
	$ipx[$chits->ip]++;
	$uax[$chits->ua]++;
	$refx[$chits->ref]++;
	}
unset($cumhits, $chits);

foreach($uax as $id => $num) {
	$agent = $wpdb->get_row($wpdb->prepare("SELECT browser, aux, os FROM {$this->iurr_tables["ua"]} WHERE id=%d", $id));
	if ($agent->aux == 2) {
		$graph_spidies[$agent->browser] += $num;
		$count_spidies += $num;
		}
	elseif ($agent->aux == 1) {
		$graph_browse[$agent->browser] += $num;
		$count_browse += $num;
		$graph_os[$agent->os] += $num;
		$count_os += $num;
		}
	}
$this->little_graph("Browsers", $graph_browse, $count_browse);
$this->little_graph("Operating Systems", $graph_os, $count_os);
echo "<div style='height: 2em; clear: both;'></div>\n";
$this->little_graph("Spiders/Bots", $graph_spidies, $count_spidies);
unset($uax, $graph_browse, $graph_os, $graph_spidies);

$dom = new ecstatic_get_host();
foreach($ipx as $id => $num) {
	$domain = $wpdb->get_var($wpdb->prepare("SELECT domain FROM {$this->iurr_tables["ip"]} WHERE id=%d", $id));
	if ($domain == "")
		continue;
	$dom->domain_name($domain);
	if (!$dom->is_domain_error()) {
		$graph_domain[$domain] += $num;
		$count_domain += $num;
		}
	}
$this->little_graph("Domains", $graph_domain, $count_domain);
unset($ipx, $domain, $graph_domain);

echo "<div style='height: 2em; clear: both;'></div>\n";

$seref = new ecstatic_seref($this); //load search engine signature strings

foreach($refx as $id => $num) {
	if ($reefer = $wpdb->get_var($wpdb->prepare("SELECT ref FROM {$this->iurr_tables["ref"]} WHERE id=%d", $id))) {
		$se = $seref->referendum($reefer);
		if (isset($se->qvar)) {
			$graph_se_ref[$se->name] += $num;
			$count_se_ref += $num;
			$graph_se_phrase[$se->qvar] += $num;
//			if (strlen($se->qvar) < 3)
//				echo $reefer . "<br /><br />";
			$count_se_phrase += $num;
			}
		elseif ($num) {
			$graph_ref[$se->host] += $num;
			$count_ref += $num;
			}
		}
	}
$this->little_graph("Referrers", $graph_ref, $count_ref);
$this->little_graph("Search Engine Referrers", $graph_se_ref, $count_se_ref);
echo "<div style='height: 2em; clear: both;'></div>\n";

$this->little_graph("SE Phrases", $graph_se_phrase, $count_se_phrase);

unset($seref, $graph_ref, $graph_se_ref, $graph_se_phrase);

$count_cats = 0;
$cats = $wpdb->get_results("SELECT ruri, aux FROM {$this->iurr_tables["ruri"]} WHERE ruri LIKE '%cat=%'");
foreach($cats as $cat) {
	$lecat = ecstatic_format_requri($cat->ruri);
	$graph_cats[$lecat] += $cat->aux;
	$count_cats += $cat->aux;
	}
$this->little_graph("Categories", $graph_cats, $count_cats);

echo "<div style='height: 2em; clear: both;'></div>\n";
unset($cats, $graph_cats);

$count_pages = 0;
$pages = $wpdb->get_results("SELECT ruri, aux FROM {$this->iurr_tables["ruri"]} WHERE ruri LIKE '%p=%'");
foreach($pages as $page) {
	$lepage = ecstatic_format_requri($page->ruri);
	$graph_pages[$lepage] += $page->aux;
	$count_pages += $page->aux;
	}
$this->little_graph("Pages", $graph_pages, $count_pages);
unset($pages, $graph_pages);

$count_feeds = 0;
$feeds = $wpdb->get_results("SELECT ruri, aux FROM {$this->iurr_tables["ruri"]} WHERE ruri LIKE '%feed=%'");
foreach($feeds as $feed) {
	$lefeed = $this->is_feed($feed->ruri);
	$graph_feeds[$lefeed] += $feed->aux;
	$count_feeds += $feed->aux;
	}
$this->little_graph("Feed Reads", $graph_feeds, $count_feeds);
unset($feeds, $graph_feeds);

echo "<div style='height: 2em; clear: both;'></div>\n";
} //play_stats

/****************************************************/
function is_feed($ruri) { //called by play_stats
$ruri = strtolower($ruri);
$feedstrings = array("comments_atom" => "COMMENT ATOM", "comment_rss2" => "COMMENT RSS2", "rss2" => "RSS2", "rdf" => "RDF", "atom" => "ATOM", "rss" => "RSS", "wp-feed.php" => "RSS2", "/feed" => "RSS2");
foreach ($feedstrings as $key => $val) {
	if (strpos($ruri, $key) !== false)
		return $val;
	}
return "";
} //is_feed

/****************************************************/
function __construct() {
parent::__construct();
ecstatic_ecstatic($this, "Little Graphs");
ecstatic_banner_graph($this);
}
} //class ecstatic_stats
?>