<?php
/***********************************************************************************/
class ecstatic_fidgetizer extends ecstatic {
public $sums;
public $dtots;
public $aux;
public $online;
/***********************************************************************************/
function __construct($online4) {
global $wpdb;
parent::__construct();
$this->sums = $wpdb->get_row("SELECT sum(regi) as 'regi', sum(regp) as 'regp', sum(feedp) as 'feedp', sum(botp) as 'botp' FROM {$this->cumulative_table}", ARRAY_A);
$today = date("Y-m-d", $this->datetime + (60 * 60 * $this->timezoneoffset));
$this->dtots = $wpdb->get_row($wpdb->prepare("SELECT regi, regp, feedp, botp FROM {$this->cumulative_table} WHERE day = %s", $today), ARRAY_A);
//copy of code from ecstatic.php hit_parade function
if (isset($_SERVER['REQUEST_URI']))
	$ruri = $_SERVER['REQUEST_URI'];
elseif (isset($_SERVER['QUERY_STRING']))
	$ruri = $_SERVER['SCRIPT_NAME'] .'?'. $_SERVER['QUERY_STRING'];
elseif (isset($_SERVER['argv']))
	$ruri = $_SERVER['SCRIPT_NAME'] .'?'. $_SERVER['argv'][0];
else
	$ruri = $_SERVER['SCRIPT_NAME'];
$ruri = '/'. ltrim($ruri, '/');
//end copy
$this->aux = $wpdb->get_var($wpdb->prepare("SELECT aux FROM {$this->iurr_tables["ruri"]} WHERE ruri=%s", $ruri));
$this->online = $wpdb->get_var($wpdb->prepare("SELECT count(DISTINCT ip) FROM {$this->hits_table} WHERE score < 10 AND datetime > %d", $this->datetime-(60 * $online4)));
} //constructor
} //class ecstatic_fidgetizer

/***********************************************************************************/
class ecstatic_widget extends WP_Widget {
private $ids = array("viztot" => "viztotu", "pagetot" => "pagetotu", "feedtot" => "feedtotu", "bottot" => "bottotu", "curpagetot" => "curpagetotu", "vizday" => "vizdayu", "pageday" => "pagedayu", "feeday" => "feedayu", "botday" => "botdayu", "online" => "onlineu");
private $default = array("viztot" => "Visitors", "pagetot" => "All Pages", "feedtot" => "Feed Reads", "bottot" => "Spider/Bots", "curpagetot" => "This Page", "vizday" => "Viz Today", "pageday" => "Pages Today", "feeday" => "RSS Today", "botday" => "Bots Today", "online" => "Visitors Online");
/***********************************************************************************/
function widget($args, $instance) {
extract($args);
$title = apply_filters('widget_title', $instance['title']);
echo "\n" . $before_widget . "\n";
if ($title)
	echo $before_title . $title . $after_title;
$ecwidget = new ecstatic_fidgetizer($instance['online4']);
echo "<table class='ecstatic_widget_table' summary='ecstatic widget table'>\n";
echo "<thead><tr></tr></thead>\n";
if (isset($instance["ecstatic_link"]))
	echo "<tfoot><tr><td colspan='2' class='ecstatic_widget_blurb'>&uuml;ber&nbsp;<a href='http://wordpress.org/extend/plugins/ecstatic/' title='ecSTATic Visitor Stats Plugin for WordPress' target='blank'>ec<i>STAT</i>ic</a></td></tr></tfoot>\n";
echo "<tbody>\n";
$val = array("viztot" => $ecwidget->sums["regi"], "pagetot" => $ecwidget->sums["regp"], "feedtot" => $ecwidget->sums["feedp"], "bottot" => $ecwidget->sums["botp"], "curpagetot" => $ecwidget->aux, "vizday" => $ecwidget->dtots["regi"], "pageday" => $ecwidget->dtots["regp"], "feeday" => $ecwidget->dtots["feedp"], "botday" => $ecwidget->dtots["botp"], "online" => $ecwidget->online);
$ordered = $unordered = array();
foreach($this->ids as $key1 => $key2) {
	if (isset($instance[$key1])) {
		if ($instance[$key2]) {
			if (preg_match("/^[0-9]-/", $instance[$key2])) {
				list($key, $label) = explode("-", $instance[$key2]);
				if (!$label)
					$label = $this->default[$key1];
				$ordered[$key] = "\t<tr><td class='ecstatic_label'>{$label}:</td><td class='ecstatic_num'>" . number_format($val[$key1]) . "</td></tr>\n";
				continue;
				}
			$label = $instance[$key2];
			}
		else
			$label = $this->default[$key1];
		$unordered[] = "\t<tr><td class='ecstatic_label'>{$label}:</td><td class='ecstatic_num'>" . number_format($val[$key1]) . "</td></tr>\n";
		}
	}
ksort($ordered);
foreach($ordered as $order)
	echo $order;
foreach($unordered as $unorder)
	echo $unorder;

echo "</tbody>\n";
echo "</table>\n";
echo $after_widget . "\n";
} //widget
/***********************************************************************************/
function update($new_instance, $old_instance) {
$new_instance['title'] = strip_tags($new_instance['title']);
foreach($this->ids as $key2)
	$new_instance[$key2] = strip_tags($new_instance[$key2]);
return $new_instance;
} //update
/***********************************************************************************/
function checkered($arg) {return $arg ? "checked" : "";}
/***********************************************************************************/
function form($instance) {
if ($instance["hasloaded"])
	$defaults = array("title" => "Visitors", "online4" => "30");
else
	$defaults = array("title" => "Visitors", "online4" => "30", "ecstatic_link" => true);
$instance = wp_parse_args((array) $instance, $defaults);
echo "<p><label for='{$this->get_field_id("title")}'>Title:</label>\n";
echo "<input type='text' id='{$this->get_field_id("title")}' name='{$this->get_field_name("title")} 'value='{$instance["title"]}' />\n</p>\n";
echo <<<XXX
<style type="text/css">
.widget table {width:100%;margin:auto;}
.widget table th, table td {font-size:x-small;text-align:left;}
</style>

XXX;

echo "<table>\n";
echo "<thead><th>Default</th><th>Customize</th><th>&#10004;</th></thead>\n";
echo "<tbody>\n";
foreach($this->ids as $key1 => $key2) {
	echo "<tr>\n";
	echo "<td><label for='{$this->get_field_id($key1)}'>{$this->default[$key1]} </label></td>\n";
	echo "<td><input type='text' size='10' id='{$this->get_field_id($key2)}' name='{$this->get_field_name($key2)}' value='{$instance[$key2]}' /></td>\n";
	echo "<td><input type='checkbox' id='{$this->get_field_id($key1)}' name='{$this->get_field_name($key1)}' value='true'" . $this->checkered($instance[$key1]) . " /></td>\n";
	echo "</tr>\n";
	}
echo "</tbody>\n</table><br />\n";
echo "Count <b>Online</b> within last <input type='text' size='2' id='{$this->get_field_id("online4")}' name='{$this->get_field_name("online4")}' value='{$instance["online4"]}' /> minutes<br /><br />\n";
echo "<input type='checkbox' id='{$this->get_field_id("ecstatic_link")}' name='{$this->get_field_name("ecstatic_link")}' " . $this->checkered($instance["ecstatic_link"]) . " />\n";
echo "<label for='{$this->get_field_id("ecstatic_link")}'> \"powered by\" link to ecSTATic home</label><br /><br />\n";
echo "<input type='hidden' id='{$this->get_field_id("hasloaded")}' name='{$this->get_field_name("hasloaded")}' value='hasloaded' />\n";

echo "Tip: Precede custom labels with a number and a dash (1-MyViz Today), or just enter a number and dash (2-) in the Customize boxes to override default order. Numbered items will be displayed first, followed by unnumbered items in their default order.";
}
/***********************************************************************************/
function ecstatic_widget() { //old style constructor: __construct locks up
$widget_ops = array('classname' => 'ecstaticwidget', 'description' => 'Visitor and page stats');
$this->WP_Widget('ecSTATicWidget', 'ecSTATic', $widget_ops);
}
} //class ecstatic_widget
?>